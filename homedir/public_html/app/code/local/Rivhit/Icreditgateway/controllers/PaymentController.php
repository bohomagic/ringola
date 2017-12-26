<?php
class Rivhit_Icreditgateway_PaymentController extends Mage_Core_Controller_Front_Action {
	// The redirect action is triggered when someone places an order
	
public function redirectAction() {
       $myurl = Mage::helper('icreditgateway')->myData();
       //Mage::register('urlforPayment',$myurl);
       Mage::register('mygateway_ul', $myurl);
       $windowType = Mage::getStoreConfig('payment/icreditgateway/window_type');
       if($windowType=='redirect'){
         $this->_redirectUrl($myurl);  
       }
       if($windowType=='popup'){
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','icreditgateway',array('template' => 'icreditgateway/icreditgateway.phtml'));
		    $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout(); 
       }
       if($windowType=='iframe'){
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','icreditgateway',array('template' => 'icreditgateway/icreditgateway.phtml'));
	      $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
       }    
                                       
 }
 
	
	// The response action is triggered when your gateway sends back a response after processing the customer's payment
	
public function responseAction() {
	   $testMode=Mage::getStoreConfig('payment/icreditgateway/test_mode');

	   if($testMode==0){
	   		$groupToken=Mage::getStoreConfig('payment/icreditgateway/prod_group_token');
   		}else{
   		    $groupToken=Mage::getStoreConfig('payment/icreditgateway/test_group_token');
   		}
   		$windowType = Mage::getStoreConfig('payment/icreditgateway/window_type');
       

		$raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $IPNPost = array();
        foreach ($raw_post_array as $keyval){
        $keyval = explode ('=', $keyval);
        if (count($keyval) == 2){
            $IPNPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }      
           $orderId = $IPNPost['Order'];
           $postData = array('GroupPrivateToken' => $groupToken,
                         'SaleId'=> $IPNPost['SaleId'],
                         'TotalAmount'=> $IPNPost['TransactionAmount']
                        );     
            if($testMode==0){
            	$url = "https://icredit.rivhit.co.il/API/PaymentPageRequest.svc/Verify";
            }else{
            	$url = "https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/Verify";
            } 
            //$url = "https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/Verify";
		    $jsonData = json_encode($postData); 
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result     = curl_exec($curl);
			$response   = json_decode($result);
			$Status = $response->Status;
			curl_close($curl);
           
           $successStatus=Mage::getStoreConfig('payment/icreditgateway/order_success_status');
           $successState=Mage::getStoreConfig('payment/icreditgateway/order_success_status');
           $failedStatus=Mage::getStoreConfig('payment/icreditgateway/order_failed_status');
           $failedState=Mage::getStoreConfig('payment/icreditgateway/order_failed_status');
           if($successStatus=='pending'){
           	 $successState='new';
           }
           if($failedStatus=='pending'){
           	 $failedState='new';
           }
			 
			    if ($Status == 'VERIFIED'){
			       	$order = Mage::getModel('sales/order')->load($orderId, 'increment_id'); 
					$order->setData('state',$successState);
					$order->setStatus($successStatus);
					$order->sendNewOrderEmail();
					$order->save();
					$history = $order->addStatusHistoryComment('Manually order to Complete VERIFIED.', false);
					$history->setIsCustomerNotified(false);

			   }else{
			   	   $order = Mage::getModel('sales/order')->load($orderId, 'increment_id'); 
					$order->setData('state',$failedState);
					$order->setStatus($failedStatus);
					$order->save();
					$history = $order->addStatusHistoryComment('Manually order to On Hold not VERIFIED.', false);
					$history->setIsCustomerNotified(false);
				}
	            // $redirecturl = Mage::getStoreConfig('payment/mygateway/redirect_url');
	            //  if($windowType=='popup' || $windowType=='iframe'){
             //        $redirecturl= Mage::getBaseUrl().'mygateway/payment/redirectpop';
	            //  	$this->_redirectUrl($redirecturl);
	             	//$this->redirectpop();
					// if($redirecturl == ''){
					// 		//$redirectTo = Mage::getBaseUrl().'checkout/onepage/success/';
			  //               $this->_redirectUrl($redirectTo);
		   //           }else{
		   //           	$redirectTo = $redirecturl;
			  //           $this->_redirectUrl($redirectTo);
		   //           }
	            }
   

   public function redirectpopAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','icreditgateway',array('template' => 'icreditgateway/redirectpop.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout(); 
   }
}
