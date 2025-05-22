<?php

namespace Devlat\Blog\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Devlat\Blog\Model\Post as PostModel;
use Devlat\Blog\Model\ResourceModel\Post as PostResourceModel;

class Collection extends AbstractCollection
{

    protected function _construct(): void
    {
        $this->_init(PostModel::class,
            PostResourceModel::class);
    }
}
