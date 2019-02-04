<?php
/**
* Harmony Perishable Products Block Widget Grid Column Renderer Perishable
* 
* This class holds a collection of functions to be used to display the modified sales order grid values.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
class Harmony_PerishableProducts_Block_Widget_Grid_Column_Renderer_Perishable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_options = false;

    protected function _getOptions()
    {
        if (! $this->_options) {
            $methods = array(
                0 => Mage::helper('core')->__('No'),
                1 => Mage::helper('core')->__('Yes'),
            );

            $this->_options = $methods;
        }
        return $this->_options;
    }

    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        $options = $this->_getOptions();
        return isset($options[$value]) ? $options[$value] : $value;
    }
}
