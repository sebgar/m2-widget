<?php

namespace Sga\Widget\Block\Adminhtml\Widget;

class Options extends \Magento\Widget\Block\Adminhtml\Widget\Options
{
    public function getFieldset($code, $label)
    {
        if ($this->_getData($code.'_fieldset') instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
            return $this->_getData($code.'_fieldset');
        }

        $mainFieldsetHtmlId = 'options_fieldset_'.$code.'_' . md5($this->getWidgetType());
        $this->setMainFieldsetHtmlId($mainFieldsetHtmlId);
        $fieldset = $this->getForm()->addFieldset(
            $mainFieldsetHtmlId,
            ['legend' => __($label), 'class' => 'fieldset-wide fieldset-widget-options', 'collapsable' => true]
        );
        $this->setData($code.'_fieldset', $fieldset);

        // add dependence javascript block
        $block = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class);
        $this->setChild('form_after', $block);

        return $fieldset;
    }

    protected function _addField($parameter)
    {
        $form = $this->getForm();

        ///// BEGIN DS
        $fieldsetCode = (string)$parameter->getFieldset();
        if($fieldsetCode === '' || $parameter->getFieldset() === 'main'){
            ///// END DS
            $fieldset = $this->getMainFieldset();
            //$form->getElement('options_fieldset');
            ///// BEGIN DS
        } else {
            $fieldset = $this->getFieldset(str_replace(' ', '_', $fieldsetCode), ucfirst($fieldsetCode));
        }
        ///// END DS

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = [
            'name' => $form->addSuffixToName($fieldName, 'parameters'),
            'label' => __($parameter->getLabel()),
            'required' => $parameter->getRequired(),
            'class' => 'widget-option',
            'note' => __($parameter->getDescription()),
        ];

        if ($values = $this->getWidgetValues()) {
            $data['value'] = isset($values[$fieldName]) ? $values[$fieldName] : '';
        } else {
            $data['value'] = $parameter->getValue();
        }

        //prepare unique id value
        if ($fieldName == 'unique_id' && $data['value'] == '') {
            $data['value'] = hash('sha256', microtime(1));
        }

        if (is_array($data['value'])) {
            foreach ($data['value'] as &$value) {
                $value = html_entity_decode((string)$value);
            }
        } else {
            $data['value'] = html_entity_decode((string)$data['value']);
        }

        // prepare element dropdown values
        if ($values = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = [];
            foreach ($values as $option) {
                $data['values'][] = ['label' => __($option['label']), 'value' => $option['value']];
            }
            // otherwise, a source model is specified
        } elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = $this->_sourceModelPool->get($sourceModel)->toOptionArray();
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
            // just an element renderer
        } elseif ($fieldType && $this->_isClassName($fieldType)) {
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
            $helperBlock = $this->getLayout()->createBlock(
                $helper->getType(),
                '',
                ['data' => $helper->getData()]
            );
            if ($helperBlock instanceof \Magento\Framework\DataObject) {
                $helperBlock->setConfig(
                    $helper->getData()
                )->setFieldsetId(
                    $fieldset->getId()
                )->prepareElementHtml(
                    $field
                );
            }
        }

        // dependencies from other fields
        $dependenceBlock = $this->getChildBlock('form_after');
        $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        if ($parameter->getDepends()) {
            foreach ($parameter->getDepends() as $from => $row) {
                $values = isset($row['values']) ? array_values($row['values']) : (string)$row['value'];
                $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            }
        }

        return $field;
    }
}
