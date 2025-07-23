<?php

namespace Belsignum\DeleteProcessedImage\Service;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;

final readonly class FileUsageCacheInvalidator
{
    public function __construct(
        private CacheManager   $cacheManager,
        private ConnectionPool $connectionPool,
    ) {}

    public function flushPageCacheForFile(File $file): void
    {
        $fileUid = $file->getUid();

        // Aktuell nur tt_content. Optional: news, pages, ...
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder
            ->select('c.pid')
            ->from('sys_file_reference', 'sfr')
            ->join(
                'sfr',
                'tt_content',
                'c',
                $queryBuilder->expr()->eq('sfr.uid_foreign', 'c.uid')
            )
            ->where(
                $queryBuilder->expr()->eq('sfr.uid_local', $queryBuilder->createNamedParameter($fileUid, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('sfr.tablenames', $queryBuilder->createNamedParameter('tt_content'))
            )
            ->groupBy('c.pid');

        $rows = $queryBuilder->executeQuery()->fetchFirstColumn();

        foreach ($rows as $pid) {
            $this->cacheManager->flushCachesByTag('pageId_' . (int)$pid);
        }
    }
}
