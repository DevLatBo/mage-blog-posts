<?php

namespace Devlat\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Post extends AbstractDb
{

    /** @var string Main Table Name */
    const MAIN_TABLE = 'devlat_blog_post';
    /** @var string Main table primary key field name */
    const ID_FIELD_NAME = 'id';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
