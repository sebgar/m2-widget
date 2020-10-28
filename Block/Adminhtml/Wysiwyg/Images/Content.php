<?php
namespace Sga\Widget\Block\Adminhtml\Wysiwyg\Images;

class Content extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content
{
    public function getOnInsertUrl()
    {
        $isWidget = (bool)$this->_request->getParam('widget');
        if ($isWidget) {
            return $this->getUrl('sgawidget/wysiwyg_images/onInsert');
        }
        return parent::getOnInsertUrl();
    }
}
