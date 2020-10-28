<?php
namespace Sga\Widget\Model\Config;

class SchemaLocator extends \Magento\Widget\Model\Config\SchemaLocator
{
    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        parent::__construct($moduleReader);

        $etcDir = $moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Sga_Widget');
        $this->_schema = $etcDir . '/widget.xsd';
        $this->_perFileSchema = $etcDir . '/widget_file.xsd';
    }
}
