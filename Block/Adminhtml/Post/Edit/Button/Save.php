<?php

namespace Devlat\Blog\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Save implements ButtonProviderInterface
{

    /**
     * Button Save for admin post form (add/edit)
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label'             =>  __('Save'),
            'class'             =>  'save primary',
            'data_attribute'    =>  [
                'mage-init' => [
                    'button'    =>  [
                        'event' =>  'save',
                    ],
                ],
            ],
        ];
    }
}
