<?php

namespace Devlat\Blog\Model\Service;

use Magento\Framework\App\ResourceConnection;

class Management
{
    const TABLE_NAME = "devlat_blog_post";
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * Constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Deletes the table devlat_blog_post.
     * @return void
     */
    public function dropTable(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName(self::TABLE_NAME);

        if ($connection->isTableExists($table)) {
            $connection->dropTable($table);
        }
    }
}
