<?php

namespace Devlat\Blog\Controller\Adminhtml\Post;

use Devlat\Blog\Model\Post;
use Devlat\Blog\Model\PostFactory;
use Devlat\Blog\Model\ResourceModel\Post as PostResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends Action implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Devlat_Blog::post_save';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;
    /**
     * @var PostFactory
     */
    private PostFactory $postFactory;
    /**
     * @var PostResourceModel
     */
    private PostResourceModel $postResource;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PostFactory $postFactory
     * @param PostResourceModel $postResource
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PostFactory $postFactory,
        PostResourceModel $postResource
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    /**
     * Edits the post selected in grid, but first validates the request.
     * @return Json
     */
    public function execute(): Json
    {
        $json = $this->jsonFactory->create();
        $messages = [];
        $error = false;

        $isAjax =   $this->getRequest()->getParam('isAjax', false);
        $items  =   $this->getRequest()->getParam('items', []);

        if (!$isAjax || !count($items)) {
            $messages[] = __('Please correct the data sent.');
            $error = true;
        }

        if (!$error) {
            foreach ($items as $item) {
                $id = $item['id'];
                try {
                    /** @var Post $post */
                    $post = $this->postFactory->create();
                    $this->postResource->load($post, $id);
                    $post->setData(array_merge($post->getData(), $item));
                    $this->postResource->save($post);
                } catch(\Exception $e) {
                    $messages[] = __("Something went wrong while saving item $id");
                    $error = true;
                }
            }
        }

        return $json->setData([
            'messages'  =>  $messages,
            'error'     =>  $error,
        ]);
    }
}
