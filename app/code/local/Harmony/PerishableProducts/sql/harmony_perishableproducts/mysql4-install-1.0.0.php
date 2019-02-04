<?php
/**
* Harmony Perishable Products Installer
* 
* This script modifies the database as needed for this module.
* 
* @package    Harmony\PerishableProducts
* @author     Harmony Reppert
* @version    1.0.0
* @link       https://github.com/HarmonyR/PerishableProducts
*/
$installer = $this;
$installer->startSetup();

/*********************** CUSTOM PRODUCT ATTRIBUTES ***********************/
// Add shelf_life_days (eav_attribute)
$installer->addAttribute('catalog_product', 'shelf_life_days', array(
    'backend'           => '',        // backend_model
    'type'              => 'int',     // backend_type
    'table'             => '',        // backend_table
    'frontend'          => '',        // frontend_model
    'input'             => 'text',    // frontend_input
    'label'             => 'Shelf Life (days)', // frontend_label
    'frontend_class'    => 'validate-digits',     // frontend_class
    'source'            => '',        // source_model
    'required'          => false,     // is_required
    'user_defined'      => true,      // is_user_defined
    'default'           => '0',       // default_value
    'unique'            => false,     // is_unique
    'note'              => 'Enter the amount of days a product will perish, if applicable.', // note
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // is_global
    'group'             => 'General'  // adds to attribute sets in General group
));
// Frontend properties (catalog_eav_attribute)
$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'shelf_life_days');
$attribute
    ->setData('is_visible', true)
    ->setData('is_searchable', false)                 // Use in Quick Search
    ->setData('is_visible_in_advanced_search', true)  // Use in Advanced Search
    ->setData('is_comparable', true)                  // Comparable on Front-end
    ->setData('is_filterable', false)                 // Use In Layered Navigation
    ->setData('is_filterable_in_search', false)       // Use In Search Results Layered Navigation
    ->setData('is_used_for_promo_rules', false)       // Use for Promo Rule Conditions
    ->setData('is_html_allowed_on_front', false)      // Allow HTML Tags on Frontend
    ->setData('is_visible_on_front', true)            // Visible on Product View Page on Front-end
    ->setData('used_in_product_listing', true)        // Used in Product Listing
    ->setData('used_for_sort_by', false)              // Used for Sorting in Product Listing
    ->setData('is_configurable', false)               // Use To Create Configurable Product
;
$attribute->save();

/*********************** CUSTOM ORDER FIELDS ***********************/
// Add contains_perishable_item (table column)
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'contains_perishable_item', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 1,
        'default'   => '0',
        'after'     => null, // column name to insert new column after
        'comment'   => 'Contains Perishable Item(s)'
    ));

/*********************** CUSTOM ORDER GRID FIELDS ***********************/
// Add contains_perishable_item (table column)
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_grid'), 'contains_perishable_item', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 1,
        'default'   => '0',
        'after'     => null, // column name to insert new column after
        'comment'   => 'Contains Perishable Item(s)'
    ));
// Add table key this field for improved speed or searching/sorting
$installer->getConnection()->addKey($this->getTable('sales/order_grid'), 'contains_perishable_item', 'contains_perishable_item');

$installer->endSetup();
