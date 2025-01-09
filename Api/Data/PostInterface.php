<?php

namespace Devlat\Blog\Api\Data;

interface PostInterface
{
    const ENTITY_ID = 'id';
    const TITLE = 'title';
    const PUBLISH_DATE = 'publish_date';
    const CONTENT = 'content';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @param $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title): self;

    /**
     * @return \DateTime
     */
    public function getPublishDate(): \DateTime;

    /**
     * @param $publishDate
     * @return $this
     */
    public function setPublishDate($publishDate): self;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content): self;
}
