<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Add extends Action implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Devlat_Blog::post_save';
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
     * Redirects to Add/Edit Page.
     * @return Page
     */
    public function execute(): Page
    {
        $id = $this->getRequest()->getParam("id");
        $title = is_null($id) ? "Add Post" : "Edit Post";

        $page = $this->pageFactory->create();
        $page->setActiveMenu('Devlat_Blog::posts');
        $page->getConfig()->getTitle()->prepend(__($title));
        return $page;
    }
}
