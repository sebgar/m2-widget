<?php
namespace Sga\Widget\Block\Widget;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Widget\Block\BlockInterface;
use Magento\Cms\Helper\Wysiwyg\Images as HelperImageWysiwyg;

abstract class AbstractWidget extends Template implements BlockInterface
{
    protected $_helperImageWysiwyg;
    protected $_jsonSerializer;
    protected $_urlBuilder;

    public function __construct(
        Context $context,
        HelperImageWysiwyg $helperImageWysiwyg,
        Json $jsonSerializer,
        array $data = []
    ) {
        $this->_helperImageWysiwyg = $helperImageWysiwyg;
        $this->_jsonSerializer = $jsonSerializer;
        $this->_urlBuilder = $context->getUrlBuilder();

        parent::__construct($context, $data);
    }

    public function getJsonSerializer()
    {
        return $this->_jsonSerializer;
    }

    public function getCmsFilter()
    {
        return $this->_cmsFilter;
    }

    public function getFieldValue($key)
    {
        $encodedStr = (string)$this->getEncoded();
        if ($encodedStr != '') {
            $encoded = explode(',', $encodedStr);
            if (in_array($key, $encoded)) {
                $value = base64_decode($this->getData($key));
            } else {
                $value = $this->getData($key);
            }
        } else {
            $value = $this->getData($key);
        }

        return (string)$value;
    }

    public function getBlocksData($prefix, $nb, $keyNeeded)
    {
        $list = array();

        for ($i = 1; $i <= $nb; $i++) {
            if ((string)$this->getFieldValue($prefix.$i.'_'.$keyNeeded) !== '') {
                // load fields
                $d = $this->getData();
                $data = [];
                foreach ($d as $k => $v) {
                    $match = array();
                    if (preg_match('/^'.$prefix.$i.'_(.*)$/', $k, $match)) {
                        $data[$match[1]] = $this->getFieldValue($k);
                    }
                }

                $list[] = $data;
            }
        }

        return $list;
    }

    public function getValue($data, $key, $default = '')
    {
        if (isset($data[$key])) {
            return $data[$key];
        } else {
            return $default;
        }
    }

    public function getImageUrl($image)
    {
        return $this->_helperImageWysiwyg->getCurrentUrl().ltrim($image, '/');
    }
}
