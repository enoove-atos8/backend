<?php

/**
 * Script para recuperar comprovantes de saídas órfãs em PRODUÇÃO
 *
 * Executa a recuperação dos comprovantes que falharam devido ao erro de FK
 * ao tentar criar histórico com user_id inválido
 *
 * Uso em produção:
 *   php scripts/recover_orphaned_receipts_prod.php <tenant> [--dry-run]
 *
 * Exemplos:
 *   php scripts/recover_orphaned_receipts_prod.php iebrd --dry-run   (testa sem fazer alterações)
 *   php scripts/recover_orphaned_receipts_prod.php iebrd             (executa as alterações)
 *   php scripts/recover_orphaned_receipts_prod.php pibc
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Parâmetros
$tenant = $argv[1] ?? null;
$dryRun = in_array('--dry-run', $argv);

if (! $tenant) {
    echo "❌ Erro: Você precisa especificar o tenant\n";
    echo "Uso: php scripts/recover_orphaned_receipts_prod.php <tenant> [--dry-run]\n";
    echo "Exemplo: php scripts/recover_orphaned_receipts_prod.php iebrd --dry-run\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "  RECUPERAÇÃO DE COMPROVANTES ÓRFÃOS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if ($dryRun) {
    echo "🔍 MODO DRY-RUN ATIVADO - Nenhuma alteração será feita\n\n";
}

echo "🔄 Processando tenant: {$tenant}\n\n";

try {
    tenancy()->initialize($tenant);

    // Busca registros órfãos (status ERROR)
    $orphanedRecords = DB::table('sync_storage')
        ->where('doc_type', 'exits')
        ->whereIn('doc_sub_type', ['transfer', 'ministerial_transfer'])
        ->where('status', 'error')
        ->orderBy('id', 'desc')
        ->get();

    if ($orphanedRecords->isEmpty()) {
        echo "✅ Nenhum comprovante órfão encontrado!\n";
        echo "   Todos os comprovantes de transfer/ministerial_transfer foram processados com sucesso.\n";
        exit(0);
    }

    echo "📋 Encontrados {$orphanedRecords->count()} comprovantes órfãos para recuperar\n\n";
    echo "───────────────────────────────────────────────────────────────\n\n";

    $processedCount = 0;
    $errorCount = 0;

    // Resolve dependências
    $minioService = app(Infrastructure\Services\External\minIO\MinioStorageService::class);
    $updateReceiptLinkAction = app(Domain\Financial\Exits\Exits\Actions\UpdateReceiptLinkAction::class);
    $updateStatusAction = app(Domain\SyncStorage\Actions\UpdateStatusAction::class);

    foreach ($orphanedRecords as $record) {
        try {
            echo "🔧 Processando sync_storage ID: {$record->id}\n";
            echo "   Type: {$record->doc_sub_type}\n";
            echo "   Path: {$record->path}\n";

            // Encontra a saída órfã associada (sem receipt_link)
            $exit = DB::table('exits')
                ->where('exit_type', $record->doc_sub_type)
                ->where('account_id', $record->account_id)
                ->where(function ($query) {
                    $query->whereNull('receipt_link')
                        ->orWhere('receipt_link', '');
                })
                ->orderBy('id', 'desc')
                ->first();

            if (! $exit) {
                echo "   ⚠️  Saída não encontrada ou já possui receipt_link\n";
                echo "   → Pulando este registro\n\n";

                continue;
            }

            echo "   → Exit ID encontrada: {$exit->id}\n";
            echo "   → Valor da saída: R$ {$exit->amount}\n";

            if ($dryRun) {
                echo "\n   [DRY-RUN] O que seria feito:\n";
                echo "   1. Baixar arquivo de: {$record->path}\n";
                $newPath = str_replace('shared_receipts', 'stored_receipts', $record->path);
                echo "   2. Fazer upload para: {$newPath}\n";
                echo "   3. Atualizar receipt_link na exit ID {$exit->id}\n";
                echo "   4. Atualizar status do sync_storage ID {$record->id} para DONE\n";
                echo "   5. Deletar arquivo antigo de shared_receipts\n\n";
                $processedCount++;

                continue;
            }

            // === PROCESSAMENTO REAL ===

            // 1. Baixa o arquivo do MinIO
            $basePathTemp = "/var/www/backend/html/storage/tenants/{$tenant}/temp";
            $minioService->deleteFilesInLocalDirectory($basePathTemp);

            echo "   → Baixando arquivo do MinIO...\n";
            $downloadedFile = $minioService->downloadFile($record->path, $tenant, $basePathTemp);

            if (! is_array($downloadedFile)) {
                throw new \Exception('Falha ao baixar arquivo do MinIO');
            }

            // 2. Calcula o novo path (stored_receipts)
            $newPath = str_replace('shared_receipts', 'stored_receipts', $record->path);
            $urlParts = explode('/', $newPath);
            array_pop($urlParts);
            $destinationPath = implode('/', $urlParts);

            // 3. Faz upload para o novo path
            echo "   → Fazendo upload para stored_receipts...\n";
            $fileUrl = $minioService->upload($downloadedFile['fileUploaded'], $destinationPath, $tenant);

            if (empty($fileUrl)) {
                throw new \Exception('Falha ao fazer upload do arquivo para MinIO');
            }

            echo "   ✅ Arquivo movido com sucesso\n";
            echo "      URL: {$fileUrl}\n";

            // 4. Deleta o arquivo antigo de shared_receipts
            echo "   → Deletando arquivo antigo de shared_receipts...\n";
            $minioService->delete($record->path, $tenant);

            // 5. Atualiza o receipt_link na tabela exits
            echo "   → Atualizando receipt_link na exit {$exit->id}...\n";
            $updateReceiptLinkAction->execute($exit->id, $fileUrl);
            echo "   ✅ Receipt link atualizado\n";

            // 6. Atualiza o status do sync_storage para DONE
            echo "   → Atualizando status do sync_storage {$record->id}...\n";
            $updateStatusAction->execute($record->id, 'done');
            echo "   ✅ Status atualizado para DONE\n";

            // 7. Limpa arquivos temporários
            $minioService->deleteFilesInLocalDirectory($basePathTemp);

            echo "\n   ✅ SUCESSO! Comprovante recuperado completamente\n";
            echo "───────────────────────────────────────────────────────────────\n\n";

            $processedCount++;

        } catch (\Exception $e) {
            $errorCount++;
            echo "\n   ❌ ERRO ao processar sync_storage ID {$record->id}\n";
            echo "   Mensagem: {$e->getMessage()}\n";
            echo "   Arquivo: {$e->getFile()}:{$e->getLine()}\n";
            echo "───────────────────────────────────────────────────────────────\n\n";
        }
    }

    echo "\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  RESUMO DO PROCESSAMENTO\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "✅ Processados com sucesso: {$processedCount}\n";

    if ($errorCount > 0) {
        echo "❌ Erros encontrados: {$errorCount}\n";
        echo "\n⚠️  ATENÇÃO: Alguns comprovantes não foram recuperados.\n";
        echo "   Verifique os erros acima e tente novamente.\n";
    } else {
        echo "\n🎉 Todos os comprovantes foram recuperados com sucesso!\n";
    }

    echo "\n";

} catch (\Exception $e) {
    echo "\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  ERRO FATAL\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "❌ {$e->getMessage()}\n";
    echo "   Arquivo: {$e->getFile()}:{$e->getLine()}\n\n";
    exit(1);
}
