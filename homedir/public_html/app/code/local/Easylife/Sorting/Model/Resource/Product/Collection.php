<?php
class Easylife_Sorting_Model_Resource_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Collection {
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC){
        //don't screw up the admin sorting
        if (Mage::app()->getStore()->getId() == Mage_Core_Model_App::ADMIN_STORE_ID){
            return parent::addAttributeToSort($attribute, $dir);
        }
        //the stock sorting should apply only when sorting by position. Leave the price and name alone
        if ($attribute == 'position1') {
            //join the stock status index table for the current website and check if the product is saleable and the qtu.
            $select = $this->getSelect();
            $select->joinLeft(
                array('stock_qty' => $this->getTable('cataloginventory/stock_status')),
                'e.entity_id = stock_qty.product_id AND stock_qty.website_id='.Mage::app()->getWebsite()->getId(),
                array(
                    'salable' => 'stock_qty.stock_status',
                    'product_id' => 'stock_qty.product_id'
                )
            );
            //get the reversed sorting
            //if by position ascending, then you need to sort by qty descending and the other way around
            $reverseDir = ($dir == 'ASC') ? 'DESC' : 'ASC';
            //this is optional - it shows the in stock products above the out of stock ones independent if sorting by position ascending or descending
            $this->getSelect()->order('salable '.$reverseDir);
            //sort by qty
            $this->getSelect()->order('product_id '.$reverseDir);
            return $this;
        }
        //if not sorting by position, let magento do its magic.
        return parent::addAttributeToSort($attribute, $dir);
    }
}