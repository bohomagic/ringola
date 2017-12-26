<?php

class Godogi_Override_Block_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
    public function getItemsRtl()
    {
		$products = Mage::getModel('catalog/product')
		->getCollection()
		->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter(array(array('attribute'=>'category_id','finset'=>'47')))
        ->addStoreFilter();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		$products->getSelect()->order('RAND()');
		$products->getSelect()->limit(10);
		return $products;
    }
    public function getItemsDefault()
    {
		$products = Mage::getModel('catalog/product')
		->getCollection()
		->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter(array(array('attribute'=>'category_id','finset'=>'2')))
        ->addStoreFilter();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		$products->getSelect()->order('RAND()');
		$products->getSelect()->limit(10);
		return $products;
    }
}