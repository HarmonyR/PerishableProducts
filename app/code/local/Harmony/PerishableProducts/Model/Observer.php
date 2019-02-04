<?php
/**
* Harmony Perishable Products Observer
* 
* This class holds a collection of functions to be used to implement perishable products.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
class Harmony_PerishableProducts_Model_Observer
{
    /** @var array $settings Array of settings for this module */
    private $settings = array();
    
    public function __construct()
    {
        Mage::helper('perishableproducts')->logDebug(__METHOD__);
        $this->settings = Mage::helper('perishableproducts')->getSettings();
    }
    /**
    * This function flags an order if any products are considered perishable. A comment will also be added to the order.
    * 
    * @param Varien_Event_Observer $observer
    * @event checkout_submit_all_after
    * @return void
    */
    public function processOrderItems($observer)
    {
        Mage::helper('perishableproducts')->logDebug(__METHOD__);
        
        // Confirm module is enabled, case sensitive
        if (! Mage::helper('core')->isModuleOutputEnabled('Harmony_PerishableProducts')) {
            return;
        }
        
        // Get module settings
        $perishableThreshold = $this->settings['threshold'];
        
        // Get order data
        $order = $observer->getEvent()->getOrder();
        if (empty($order)) {
            Mage::helper('perishableproducts')->logError(__METHOD__.' Order cannot be found ('.$observer->getEvent()->getName().').');
            return;
        }
        
        // Loop through order items to determine if order contains a perishable product
        $items = $order->getAllItems();
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId()); // parent product or standard product
            $productShelfLife = $product->getShelfLifeDays();
            Mage::helper('perishableproducts')->logDebug(__METHOD__.' product_Shelf_Life: '.var_export($productShelfLife, true).' sku: '.$item->getProduct()->getSku());
            if (! empty($productShelfLife) and $productShelfLife <= $perishableThreshold) {
                $order->setContainsPerishableItem(1);
                $order->addStatusHistoryComment('Order contains perishable item(s).', true); // comment is visible to customer
                $order->save();
                Mage::helper('perishableproducts')->logDebug(__METHOD__.' Order contains perishable item(s).');
                break;
            }
        }
    }
    /**
    * This function adds shelf_life_days to the product grid.
    * 
    * @param Varien_Event_Observer $observer
    * @event core_block_abstract_prepare_layout_before
    * @return void
    */
    public function addColumnsToProductGrid($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (! $block)
            return;
        
        if ($block->getType() == 'adminhtml/catalog_product_grid') {
            Mage::helper('perishableproducts')->logDebug(__METHOD__);
            $block->addColumnAfter('shelf_life_days', array(
                'header'    => Mage::helper('catalog')->__('Shelf Life'),
                'width'     => '50px',
                'type'      => 'number',
                'index'     => 'shelf_life_days',
            ), 'status');
            Mage::helper('perishableproducts')->logDebug(__METHOD__.' Shelf Life added to product grid.');
        }
    }
    /**
    * This function adds shelf_life_days to the product collection used by the grid.
    * 
    * @param Varien_Event_Observer $observer
    * @event eav_collection_abstract_load_before
    * @return void
    */
    public function addFieldsToProductCollection($observer)
    {
        $collection = $observer->getCollection();
        if (! isset($collection))
            return;

        if (is_a($collection, 'Mage_Catalog_Model_Resource_Product_Collection')) {
            $collection->addAttributeToSelect('shelf_life_days');
            Mage::helper('perishableproducts')->logDebug(__METHOD__.' Attribute shelf_life_days added to product grid collection.');
        }
    }
    /**
    * This function displays a message on each item in the cart if the item is perishable.
    * 
    * @param Varien_Event_Observer $observer
    * @event sales_quote_item_set_product
    * @return void
    */
    public function postCartMessage($observer)
    {
        Mage::helper('perishableproducts')->logDebug(__METHOD__);
        
        $itemMessage = Mage::helper('core')->__('This product is perishable.');
        
        // Confirm module is enabled
        if (! Mage::helper('core')->isModuleOutputEnabled('Harmony_PerishableProducts'))
            return;
        
        // Get module settings
        $perishableThreshold = $this->settings['threshold'];
        
        // Get product
        $cartItemId = $observer->getEvent()->getQuoteItem()->getId();
        $eventProduct = $observer->getEvent()->getProduct();
        if (! $eventProduct) {
            Mage::helper('perishableproducts')->logError(__METHOD__.' Observer event missing product object.');
            return;
        }
        $product = Mage::getModel('catalog/product')->load($eventProduct->getId()); // parent product or standard product
        $productShelfLife = $product->getShelfLifeDays();

        // Get child product
        if ($product->getTypeId() == 'configurable' or $product->getTypeId() == 'grouped') {
            $quoteItem = $observer->getEvent()->getQuoteItem();
            if (! $quoteItem) {
                Mage::helper('perishableproducts')->logError(__METHOD__.' Observer event missing quote item object.');
                return;
            }
            $item = Mage::getModel('catalog/product')->loadByAttribute('sku', $quoteItem->getSku()); // child product
            $itemShelfLife = $item->getShelfLifeDays();
        } else
            $itemShelfLife = 0;
        
        // Use child product half_life_seconds if it is set and less than parent, otherwise use parent half_life_seconds
        $shelfLife = (! empty($itemShelfLife) and $itemShelfLife < $productShelfLife)? $itemShelfLife : $productShelfLife;
        Mage::helper('perishableproducts')->logDebug(__METHOD__.' Processing product: '.$product->getSku()." cart_item_id: $cartItemId".
            " parent shelf_life_days: $productShelfLife child shelf_life_days: $itemShelfLife perishable threshold to compare: $shelfLife");
        
        // Add message
        if (! empty($shelfLife) and $shelfLife <= $perishableThreshold) {
            // Confirm message has not already been added
            $messages = Mage::getSingleton('checkout/session')->getQuoteItemMessages($cartItemId); // Mage_Core_Model_Message_Collection
            if ($messages) {
                foreach ($messages->getItems('notice') as $m) {
                    if (strtolower($m->getText()) == strtolower($itemMessage)) {
                        return;
                    }
                }
            }
            $message = Mage::getSingleton('core/message')->notice($itemMessage);
            Mage::getSingleton('checkout/session')->addQuoteItemMessage($cartItemId, $message);
            Mage::helper('perishableproducts')->logDebug(__METHOD__.' Item: '.$product->getSku().' is perishable');
        }
    }
    /** This function is used as needed for testing event processing. */
    public function whoCalled(Varien_Event_Observer $observer)
    {
        Mage::helper('perishableproducts')->logDebug(__METHOD__);
        $eName = $observer->getEvent()->getName();
        // Observer params
        $params = '';
        foreach ($observer->getData() as $k=>$obj)
            $params .= (empty($params))? $k : ", $k";
        Mage::helper('perishableproducts')->logError(__METHOD__." Event=$eName Observer_Data=$params");
        // Event params
        $params = '';
        foreach ($observer->getEvent()->getData() as $k=>$obj)
            $params .= (empty($params))? $k : ", $k";
        Mage::helper('perishableproducts')->logError(__METHOD__." Event=$eName Event_Data=$params");
        
        if ($observer->getProduct())
            Mage::helper('perishableproducts')->logError(__METHOD__.' Product_Debug: '.var_export($observer->getProduct()->debug(), true));

        if ($observer->getQuoteItem()) {
            $params = '';
            foreach ($observer->getQuoteItem()->getData() as $k=>$obj)
                $params .= (empty($params))? $k : ", $k";
            Mage::helper('perishableproducts')->logError(__METHOD__." QuoteItem_Data: $params");
        }

        if ($observer->getItem()) {
            $params = '';
            foreach ($observer->getItem()->getData() as $k=>$obj)
                $params .= (empty($params))? $k : ", $k";
            Mage::helper('perishableproducts')->logError(__METHOD__." Item_Data: $params");
        }
    }
}
