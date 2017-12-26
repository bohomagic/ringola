<?php
class Rivhit_Invoice_Block_Adminhtml_Custominvoice extends Mage_Core_Block_Template {
  public function invoicefile(){
  	  $order_id = $this->getRequest()->getParam('order_id');
      $collection = Mage::getModel('invoice/invoice')->getCollection()
                   ->addFieldToSelect('*')
                   ->addFieldToFilter('order_id',$order_id)
                   ->getLastItem()
		           ->load();

      return  $collection;
  }
}	