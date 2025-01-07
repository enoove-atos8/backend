<?php

namespace Infrastructure\Util\Storage\S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateDirectory
{
    private ConnectS3 $s3;

    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }



    /**
     * Cria os diretórios especificados em um bucket no MinIO.
     *
     * @param array $folders Estrutura hierárquica de diretórios para criar.
     * @param string $tenant Nome do bucket no MinIO.
     * @return void
     * @throws GeneralExceptions
     */
    public function create(array $folders, string $tenant): void
    {
        $env = App::environment();

        try
        {
            $s3 = $this->s3->getInstance();

            $this->createRecursive($folders, $tenant, $s3);
        }
        catch (S3Exception $e)
        {
            throw new GeneralExceptions('Erro ao criar diretórios no S3', 500);
        }
    }

    /**
     * Cria os diretórios recursivamente no bucket.
     *
     * @param array $folders Estrutura de pastas.
     * @param string $bucket Nome do bucket.
     * @param S3Client $s3 Instância do S3Client.
     * @param string $basePath Caminho base (usado para a recursão).
     * @return void
     */
    private function createRecursive(array $folders, string $bucket, $s3, string $basePath = ''): void
    {
        foreach ($folders as $folder => $subFolders) {

            $currentFolder = is_numeric($folder) ? $subFolders : $folder;
            $currentPath = rtrim($basePath, '/') . '/' . $currentFolder . '/';

            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $currentPath. 'empty',
                'Body'   => '',
            ]);

            if (is_array($subFolders) && !empty($subFolders))
                $this->createRecursive($subFolders, $bucket, $s3, $currentPath);
        }
    }
}
