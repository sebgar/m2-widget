<?php
namespace Sga\Widget\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class LoadOptions implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions) {
            $paramsJson = $action->getRequest()->getParam('widget');

            if ($paramsJson) {
                $params = json_decode($paramsJson, true);

                // decode values
                if (isset($params['values']['encoded'])) {
                    $encodedFields = explode(',', $params['values']['encoded']);
                    if (is_array($encodedFields) && count($encodedFields) > 0) {
                        foreach ($encodedFields as $encodedField) {
                            $encodedField = trim($encodedField);
                            if ($encodedField != '' && isset($params['values'][$encodedField])) {
                                $params['values'][$encodedField] = base64_decode($params['values'][$encodedField]);
                            }
                        }
                        $action->getRequest()->setParam('widget', json_encode($params));
                    }
                }
            }
        }
    }
}
