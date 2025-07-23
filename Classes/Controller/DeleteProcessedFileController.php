<?php

namespace Belsignum\DeleteProcessedImage\Controller;

use Belsignum\DeleteProcessedImage\Service\FileUsageCacheInvalidator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

readonly class DeleteProcessedFileController
{
    public function __construct(
        private ResourceFactory           $resourceFactory,
        private ProcessedFileRepository   $processedFileRepository,
        private FileUsageCacheInvalidator $cacheInvalidator
    ) {}

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $queryParams = $request->getQueryParams();
        $fileIdentifier = $queryParams['identifier'] ?? '';

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_processedfile');

        try {
            /** @var File $fileObject */
            $fileObject = $this->resourceFactory->retrieveFileOrFolderObject($fileIdentifier);
            if ($fileObject instanceof File) {
                $processedFiles = $this->processedFileRepository->findAllByOriginalFile($fileObject);
                foreach ($processedFiles as $processedFile)
                {
                    $processedFile->delete(true);
                    $connection->delete(
                        'sys_file_processedfile',
                        ['uid' => $processedFile->getUid()]
                    );
                }
                if(!empty($processedFiles))
                {
                    $this->cacheInvalidator->flushPageCacheForFile($fileObject);
                }
                $message = 'Processed files deleted successfully for: ' . $fileObject->getName();
                $status = 'success';
            } else {
                $message = 'Invalid file object.';
                $status = 'error';
            }
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $status = 'error';
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
        ]);
    }
}
