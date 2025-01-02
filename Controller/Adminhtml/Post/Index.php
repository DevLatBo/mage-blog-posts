<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;


use Devlat\Blog\Model\ResourceModel\Post\Collection;
use Devlat\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{

    private PageFactory $pageFactory;
    private PostCollectionFactory $postColletionFactory;

    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        PostCollectionFactory $postColletionFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->postColletionFactory = $postColletionFactory;
    }

    /**
     * Sets the title as "Posts".
     * @return Page
     */
    public function execute() : Page
    {
        /** @var Collection $collection */
        $collection = $this->postColletionFactory->create();

        foreach($collection as $item) {
            echo "<pre>";
            var_dump($item->getData());
            echo "</pre>";
        }
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__("Posts"));

        return $resultPage;
    }
}
