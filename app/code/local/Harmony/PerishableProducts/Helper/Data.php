<?php
/**
* Harmony Perishable Products Helper
* 
* This class holds a collection of functions to be used to help this module.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
class Harmony_PerishableProducts_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $logName = 'perishableproducts.log';
    
    /**
    * This function will return all the module configuration settings in a multidimensional array.
    * 
    * @return array
    */
    public function getSettings()
    {
        $fields = array('threshold','enabledebug');
        $settings = array();
        foreach ($fields as $key) {
            // System config section name/group name/field name
            $settings[$key] = Mage::getStoreConfig("perishableproducts/settings/$key", Mage::app()->getStore());
        }
        $this->logDebug(__METHOD__.' Settings: '.var_export($settings, true));
        return $settings;
    }
    /**
    * This function returns the debug setting.
    * 
    * @return int
    */
    public function isDebug()
    {
        return Mage::getStoreConfig('perishableproducts/settings/enabledebug', Mage::app()->getStore());
    }
    /**
    * This function logs a debug message if debugging is enabled.
    * 
    * @param string $message Message to write to log
    * @return void
    */
    public function logDebug($message)
    {
        if (! empty($message) and $this->isDebug()) {
            // $forceLog true indicates that the log file should always be created even if developer logging is off
            Mage::log($message, null, $this->logName, true);
        }
    }
    /**
    * This function logs an error message to the log file.
    * 
    * @param string $message Message to write to log
    * @return void
    */
    public function logError($message)
    {
        if (! empty($message)) {
            // $forceLog true indicates that the log file should always be created even if developer logging is off
            Mage::log("ERROR - $message", null, $this->logName, true);
        }
    }
}
