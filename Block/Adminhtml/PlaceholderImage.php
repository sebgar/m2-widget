<?php
namespace Sga\Widget\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Widget\Model\WidgetFactory;

class PlaceholderImage extends Template
{
    protected $_widgetFactory;
    protected $_jsonSerializer;

    public function __construct(
        Context $context,
        WidgetFactory $widgetFactory,
        Json $jsonSerializer,
        array $data = []
    ){
        $this->_widgetFactory = $widgetFactory;
        $this->_jsonSerializer = $jsonSerializer;

        parent::__construct($context, $data);
    }

    public function getJsonSerializer()
    {
        return $this->_jsonSerializer;
    }

    public function getWidgetPlaceholderImage()
    {
        $allWidgets = $this->_widgetFactory->create()->getWidgets();
        foreach ($allWidgets as $widget) {
            if (isset($widget['placeholder_image']) && (string)$widget['placeholder_image'] !== '') {
                $key = $widget['@']['type'];
                $result[$key] = $this->getViewFileUrl($widget['placeholder_image']);
            }
        }
        return $result;
    }
}
