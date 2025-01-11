<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;

use Devlat\Blog\Model\Post;
use Devlat\Blog\Model\PostFactory;
use Devlat\Blog\Model\ResourceModel\Post as PostResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

class Save extends Action implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Devlat_Blog::post_save';
    private PostFactory $postFactory;
    private PostResourceModel $postResourceModel;

    public function __construct(
        Context $context,
        PostFactory $postFactory,
        PostResourceModel $postResourceModel
    )
    {
        parent::__construct($context);
        $this->postFactory = $postFactory;
        $this->postResourceModel = $postResourceModel;
    }

    public function execute(): Redirect
    {
        // Get the post data
        $postRequest = $this->getRequest()->getPost();
        $isExistingPost = $postRequest->id;

        /** @var Post $post */
        $post = $this->postFactory->create();
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        // Determine if this is a new or existing record
        if ($isExistingPost) {
            // Existing Post
            try {
                // If existing, load data from database and merge with posted data
                $this->postResourceModel->load($post, $postRequest->id);
                // Not found? Throw exception, display message to user & redirect back
                if (!$post->getData('id')) {
                    throw new NotFoundException(__('This post no longer exist.'));
                }

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('*/*/');
            }

        } else {
            // New Post
            // If new, build an object with the posted data to save it
            unset($postRequest->id);
        }

        $post->setData(array_merge($post->getData(), $postRequest->toArray()));

        // Save the data, and tell the user it's been saved
        // If problem saving data, display error message to user
        try {
            $this->postResourceModel->save($post);
            $this->messageManager->addSuccessMessage(__('Post has been saved successfully'));
        }
        catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the post.'));
            return $redirect->setPath('*/*/');
        }

        // On success, redirect back to admin grid
        return $redirect->setPath('*/*/');
    }
}
