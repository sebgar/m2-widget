<?php
namespace Sga\Widget\Observer\Adminhtml;

use Zend\Stdlib\Parameters;
use Magento\Framework\Event\ObserverInterface;

class BuildWidget implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof \Magento\Widget\Controller\Adminhtml\Widget\BuildWidget) {
            $post = $action->getRequest()->getPost()->toArray();
            if (isset($post['parameters']['encoded'])) {
                $encodedFields = explode(',', $post['parameters']['encoded']);
                if (is_array($encodedFields) && count($encodedFields) > 0) {
                    foreach ($encodedFields as $encodedField) {
                        $encodedField = trim($encodedField);
                        if ($encodedField != '' && isset($post['parameters'][$encodedField])) {
                            $post['parameters'][$encodedField] = base64_encode($post['parameters'][$encodedField]);
                        }
                    }
                    $action->getRequest()->setPost(new Parameters($post));
                }
            }
        }
    }
}
