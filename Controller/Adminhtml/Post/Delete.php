<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;

use Devlat\Blog\Model\Post;
use Devlat\Blog\Model\PostFactory;
use Devlat\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Delete extends Action implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Devlat_Blog::post_delete';

    /**
     * @var PostFactory
     */
    private PostFactory $postFactory;
    /**
     * @var PostResource
     */
    private PostResource $postResource;

    public function __construct(
        Context $context,
        PostFactory $postFactory,
        PostResource $postResource
    )
    {
        parent::__construct($context);
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    public function execute(): Redirect
    {
        try {
            $id = $this->getRequest()->getParam("id");

            /** @var Post $post */
            $post = $this->postFactory->create();
            $this->postResource->load($post, $id);
            if ($post->getId()) {
                $this->postResource->delete($post);
                $this->messageManager->addSuccessMessage(__('The post has been deleted succesfully'));
            } else {
                $this->messageManager->addErrorMessage(__('The record does not exist.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('*/*');
    }
}
