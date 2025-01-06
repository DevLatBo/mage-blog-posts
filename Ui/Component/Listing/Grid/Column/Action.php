<?php

namespace Devlat\Blog\Ui\Component\Listing\Grid\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Action extends Column
{

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;


    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach( $dataSource['data']['items'] as &$item) {
            if (!isset($item['id'])) {
                continue;
            }

            $item[$this->getData('name')]['edit'] = [
                'href'  =>  $this->urlBuilder->getUrl('blog/post/action', ['id' => $item['id']]),
                'label' =>  __('Edit'),
            ];
            $item[$this->getData('name')]['delete'] = [
                'href'  => $this->urlBuilder->getUrl('blog/post/delete', ['id' => $item['id']]),
                'label' =>  __('Delete'),
            ];
        }
        return $dataSource;
    }
}
