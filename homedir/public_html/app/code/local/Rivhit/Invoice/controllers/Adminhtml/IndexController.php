<?php

class Rivhit_Invoice_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
 
	public function invoiceAction() {
		$this->_initAction()
			 ->renderLayout();
	}
	public function IndexAction() {
		$order_id = $this->getRequest()->getParam('order_id');
		 
		$url = Mage::helper('rivhit_invoice')->myData($order_id);      
	
		$date = date("Y-m-dTh:i:sa");
		$order = Mage::getModel('sales/order')->load($order_id);

        $Incrementid = $order->getIncrementId();
        $Incrementid = 'RINV'.$Incrementid;

        
         // Add the comment and save the order (last parameter will determine if comment will be sent to customer) 
                $order->addStatusHistoryComment( '<a href='."$url".' target="_blank">Link to invoice</a>');
                $order->save(); 
		$this->getResponse()->setRedirect($url);                        
        
		return;		
	}

}

