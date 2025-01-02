<?php

namespace Devlat\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Post extends AbstractDb
{

    protected $_idFieldName = 'id';

    public function __construct(
        Context $context,
        $connectionName = null)
    {
        parent::__construct($context, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init('blog_posts', 'id');
    }
}
