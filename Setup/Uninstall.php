<?php

namespace Devlat\Blog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{

    const TABLE_NAME = 'devlat_blog_post';
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $tableName = $setup->getTable(self::TABLE_NAME);

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }

        $setup->endSetup();
    }
}
