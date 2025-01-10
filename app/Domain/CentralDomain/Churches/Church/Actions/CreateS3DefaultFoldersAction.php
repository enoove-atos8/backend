<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\ConnectS3;

class CreateS3DefaultFoldersAction
{

    private ConnectS3 $s3;

    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }

    /**
     * @throws GeneralExceptions
     */
    public function __invoke(array $folders, string $tenant): void
    {
        try
        {
            $s3 = $this->s3->getInstance();

            $this->createRecursive($folders, $tenant, $s3);
        }
        catch (S3Exception|GeneralExceptions $e)
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
