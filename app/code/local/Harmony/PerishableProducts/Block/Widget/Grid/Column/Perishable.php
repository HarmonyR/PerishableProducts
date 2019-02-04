<?php
/**
* Harmony Parent Products Block Widget Grid Column Perishable
* 
* This class holds a collection of functions to be used to display the modified sales order grid.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
class Harmony_PerishableProducts_Block_Widget_Grid_Column_Perishable extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected $_options = false;

    protected function _getOptions()
    {
        if (! $this->_options) {
            $methods = array();
            $methods[] = array(
                'value' =>  '',
                'label' =>  ''
            );
            $methods[] = array(
                'value' =>  '0',
                'label' =>  Mage::helper('core')->__('No')
            );
            $methods[] = array(
                'value' =>  '1',
                'label' =>  Mage::helper('core')->__('Yes')
            );

            $this->_options = $methods;
        }
        return $this->_options;
    }
}
