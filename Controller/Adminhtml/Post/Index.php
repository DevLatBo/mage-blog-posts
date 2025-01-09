<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Devlat_Blog::posts';

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * Sets the title as "Posts".
     * @return Page
     */
    public function execute() : Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Devlat_Blog::posts');
        $resultPage->getConfig()->getTitle()->prepend(__("Posts"));

        return $resultPage;
    }
}
