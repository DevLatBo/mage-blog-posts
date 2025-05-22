<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Devlat\Blog\Setup\Patch\Data;

use Devlat\Blog\Model\ResourceModel\Post;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class InitialPosts implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private ModuleDataSetupInterface $moduleDataSetup;
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceConnection $resourceConnection
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Do Upgrade
     *
     * @return $this
     */
    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->resourceConnection->getConnection();
        $data = [
            [
                'title'         =>  'Opinions about issues happening during the elections',
                'publish_date'  =>  '2013-02-12',
                'content'       =>  'There were some difficulties in the election process, it is something we can deal with it.',
            ],
            [
                'title'         =>  'Economic Crisis',
                'publish_date'  =>  '2019-07-02',
                'content'       =>  'The economic situation is getting worst and people start to complaining about the inflation',
            ],
            [
                'title'         =>  'Corruption',
                'publish_date'  =>  '2001-01-30',
                'content'       =>  'There is an investigation about it.'
            ],
            [
                'title'         =>  'Extra Special!',
                'publish_date'  =>  '2018-07-14',
                'content'       =>  'Just another topic.'
            ],
            [
                'title'         =>  'Whatever',
                'publish_date'  =>  '2016-05-10',
                'content'       =>  'Just for testing.'
            ],
        ];
        $connection->insertMultiple(Post::MAIN_TABLE, $data);
        $this->moduleDataSetup->endSetup();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
