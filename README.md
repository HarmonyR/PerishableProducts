# PerishableProducts
Perishable Products Module for Magento 1.9

This module allows Magento Administrators to identify products as being perishable according to a given threshold. If a product is perishable, a notice will display in the cart. If a perishable product is ordered, the order will be flagged and a comment will be added, visible on the frontend.

This module will provide the following:
* A custom product attribute, Shelf Life (days), is added to all attribute sets under the Default group.
* The custom product attribute is added to the Products grid and can be filtered/sorted.
* A custom field, contains_perishable_item, is added to the Magento Order to identify the order contains perishable items.
* A custom field, Contains Perishable Item, is added to the Order grid and can be filtered/sorted.
* If any product in the cart has a Shelf Life less than or equal to the perishable Threshold, a notice will display on the cart item.
* If an order contains any perishable products, the order will be flagged as containing perishable products.

## Installation Instructions
To install the Harmony Perishable Products module, copy the app folder to the root of the Magento install (should already be an app folder there).
After uploading the files, the next time Magento is accessed the module will be installed. This can be confirmed in the core_resource table.
```
    SELECT * FROM core_resource where code='harmony_perishable';
    
    Delete this record to force a reinstall.
    Update version to lower number to force upgrade.
```
**Note**: After the module has been installed clear the Magento cache under System->Cache Management->Flush Magento Cache and Flush Cache Storage.
Then log out of the admin and log back in.

The Harmony_PerishableProducts module can be enabled/disabled under System->Configuration->Advanced->Advanced. This might require the file permissions on
app/etc/modules/Harmony_PerishableProducts.xml to be changed so the web server can write to this file.

**Note**: Errors/debugging are written to the /var/log/perishableproducts.log file.

## Configuration Instructions
Navigate to Magento Admin > System > Configuration > Harmony > Perishable Products.
1. Threshold (days) - Enter a value in days to identify when a product is considered perishable. All products with a shelf life greater than zero and less than or equal to this value are considered perishable.
   - Defaults to 14 days
   - Numeric validation
2. Enable Debug Mode - Enable/disable debug mode. Messages logged to var/log/perishableproducts.log.
   - 0=No, 1=Yes

## Uninstall Instructions
Follow the steps below to uninstall this module.
1. Delete /app/etc/modules/Harmony_PerishableProducts.xml.
2. Delete source at /app/code/local/Harmony/PerishableProducts.
3. In Magento Admin > System > Cache Management > Flush Magento Cache.
4. In Magento DB execute the following.
```
    DELETE FROM core_resource where code='harmony_perishableproducts';
    DELETE FROM core_config_data WHERE path LIKE '%perishableproducts/settings%';
```
