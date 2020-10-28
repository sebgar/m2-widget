<?php

namespace Sga\Widget\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement as Element;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Backend\Block\Template;
use Magento\Cms\Helper\Wysiwyg\Images as HelperImageWysiwyg;

class ImageChooser extends Template
{
    protected $_elementFactory;
    protected $_helperImageWysiwyg;

    public function __construct(
        TemplateContext $context,
        FormElementFactory $elementFactory,
        HelperImageWysiwyg $helperImageWysiwyg,
        $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_helperImageWysiwyg = $helperImageWysiwyg;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file', 'widget' => '1']);

        /** @var \Magento\Backend\Block\Widget\Button $chooserBtn */
        $chooserBtn = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\''. $sourceUrl .'\')')
            ->setDisabled($element->getReadonly());

        /** @var \Magento\Framework\Data\Form\Element\Text $input */
        $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        $input->setValue($element->getValue());
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        /** @var \Magento\Backend\Block\Widget\Button $removeBtn */
        $removeBtn = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setClass('btn-delete')
            ->setLabel(__('Remove Image'))
            ->setOnClick("jQuery(\"#".$input->getId()."\").val('').trigger('change')")
            ->setStyle($element->getValue() === '' ? 'display:none' : '')
            ->setDisabled($element->getReadonly());

        $containerImage = '<div id="'.$element->getId().'_img">';
        if ((string)$element->getValue() !== '') {
            $containerImage .= '<img src="'.$this->_helperImageWysiwyg->getCurrentUrl().$element->getValue().'" style="max-height:100px;max-width:200px;margin: 10px 0 0 0;" />';
        }
        $containerImage .= '</div>';

        $js = '<script type="text/javascript">
jQuery("#'.$element->getId().'").on("change", function (event) {
    var element = jQuery(event.currentTarget);
    var container = jQuery("#'.$element->getId().'_img");
    if (element.val() !== "") {
        container.html("<img src=\"'.$this->_helperImageWysiwyg->getCurrentUrl().'"+element.val()+"\" style=\"max-height:100px;max-width:200px;margin: 10px 0 0 0;\" />");
        jQuery("#'.$removeBtn->getId().'").show();
    } else {
        container.html("");
        jQuery("#'.$removeBtn->getId().'").hide();
    }
});
</script>';

        $element->setData('after_element_html', $input->getElementHtml() . $chooserBtn->toHtml() . $removeBtn->toHtml() . $containerImage . $js);

        // reset element value because of rendering html create an admin-control with value
        $element->setValue(null);

        return $element;
    }
}
