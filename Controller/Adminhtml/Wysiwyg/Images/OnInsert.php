<?php

namespace Sga\Widget\Controller\Adminhtml\Wysiwyg\Images;

class OnInsert extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\OnInsert
{
    public function execute()
    {
        $imagesHelper = $this->_objectManager->get(\Magento\Cms\Helper\Wysiwyg\Images::class);
        $request = $this->getRequest();

        $filename = $request->getParam('filename');
        $filename = $imagesHelper->idDecode($filename);

        $path = $request->getParam('node');
        $path = $imagesHelper->idDecode($path);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($path.DIRECTORY_SEPARATOR.$filename);
    }
}
