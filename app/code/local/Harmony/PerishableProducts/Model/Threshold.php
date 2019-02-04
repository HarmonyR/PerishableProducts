<?php
/**
* Harmony Perishable Products Model Threshold
* 
* This class holds a collection of functions to be used to maintain the threshold setting in this module.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
class Harmony_PerishableProducts_Model_Threshold extends Mage_Core_Model_Config_Data
{
    /**
    * This function will validate the threshold value before allowing it to be saved in the module configuration.
    * 
    * @return void
    */
    public function save()
    {
        $threshold = $this->getValue();
        if (! is_numeric($threshold) or stripos($threshold, '.') !== false) {
            Mage::throwException(Mage::helper('core')->__('Threshold must be a numerical whole number.'));
        }
        return parent::save();
    }
}
