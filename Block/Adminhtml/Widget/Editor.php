<?php
namespace Sga\Widget\Block\Adminhtml\Widget;

class Editor extends \Magento\Backend\Block\Template
{
    protected $_wysiwygConfig;
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_wysiwygConfig = $wysiwygConfig;

        parent::__construct($context, $data);
    }

    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $input = $this->_elementFactory->create("editor", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setWysiwyg(true);
        $input->setForceLoad(true);
        $input->setConfig($this->getWysiwygConfig($element));
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        // reset element value
        $element->setValue('');

        // append textarea html after
        $element->setData('after_element_html', $input->getElementHtml());
        return $element;
    }

    protected function getWysiwygConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $config = $this->_wysiwygConfig->getConfig();
        $config->setData('add_variables', false);
        $config->setData('add_widgets', false);
        $config->addData(
            [
                'settings' => [
                    'mode' => 'exact',
                    'elements' => $element->getHtmlId(),
                    'theme_advanced_buttons1' => 'bold,underline,italic,|,justifyleft,justifycenter,justifyright,|,styleselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                    'theme_advanced_buttons2' => null,
                    'theme_advanced_buttons3' => null,
                    'theme_advanced_buttons4' => null,
                ]
            ]
        );
        return $config;
    }
}
