<?php
class Rivhit_Invoice_Model_Observer {
    public function customerInvoiceButton($observer) {
       
       $block = $observer->getBlock();
       if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View && $block->getRequest()->getControllerName() == 'sales_order') {
        //echo $block->getUrl('*/invoice/sales_order/rivhit');
           //$orderId = Mage::app()>getRequest()>getParam('order_id');
           $block->addButton('custominvoice', array(
               'label' => Mage::helper('sales')->__('Custom Invoice'),
               'onclick' => 'setLocation(\'' . $block->getUrl('orderinvoice/adminhtml_index/index') . '\')',
               'class' => 'go'
           ));

       }

	}
}
?>