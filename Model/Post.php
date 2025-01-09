<?php

namespace Devlat\Blog\Model;

use Devlat\Blog\Api\Data\PostInterface;
use Magento\Framework\Model\AbstractModel;
use Devlat\Blog\Model\ResourceModel\Post as PostResourceModel;

class Post extends AbstractModel implements PostInterface
{

    protected function _construct(): void
    {
        $this->_init(PostResourceModel::class);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param $entityId
     * @return PostInterface
     */
    public function setEntityId($entityId): PostInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param $title
     * @return PostInterface
     */
    public function setTitle($title): PostInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return \DateTime
     */
    public function getPublishDate(): \DateTime
    {
        return $this->getData(self::PUBLISH_DATE);
    }

    /**
     * @param $publishDate
     * @return PostInterface
     */
    public function setPublishDate($publishDate): PostInterface
    {
        return $this->setData(self::PUBLISH_DATE, $publishDate);
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @param $content
     * @return PostInterface
     */
    public function setContent($content): PostInterface
    {
        return $this->setData(self::CONTENT, $content);
    }
}
