<?php
# 30.3.17 - PriceIncludeVAT":true, by omer avhar 
class Rivhit_Icreditgateway_Helper_Data extends Mage_Core_Helper_Abstract{
   public function myData(){
   $windowType = Mage::getStoreConfig('payment/icreditgateway/window_type');
   $redirectUrl=Mage::getStoreConfig('payment/icreditgateway/redirect_url');
   
   if($redirectUrl == ''){
      if($windowType=='redirect'){
         $redirectUrl= Mage::getBaseUrl().'checkout/onepage/success/';
      }
      else{   
         $redirectUrl= Mage::getBaseUrl().'icreditgateway/payment/redirectpop';
      }
   }
  
   $ipnUrl=Mage::getBaseUrl().'icreditgateway/payment/response';
   $testMode=Mage::getStoreConfig('payment/icreditgateway/test_mode');
   if($testMode==0){$groupToken=Mage::getStoreConfig('payment/icreditgateway/prod_group_token');
   }else{$groupToken=Mage::getStoreConfig('payment/icreditgateway/test_group_token');}
   $maxPayment=Mage::getStoreConfig('payment/icreditgateway/max_payment');
   if($maxPayment==''|| (!is_numeric($maxPayment))){$maxPayment=1;}
   $creditFromPayment=Mage::getStoreConfig('payment/icreditgateway/credit_from_payment');
   if($creditFromPayment==''|| (!is_numeric($creditFromPayment))){$creditFromPayment=0;}
   $hideItemList=Mage::getStoreConfig('payment/icreditgateway/hide_item_list');
   if($hideItemList==1){$hideItemListVal='true';
   }else{$hideItemListVal='false';}
   $invoiceLanguage=Mage::getStoreConfig('payment/icreditgateway/invoice_lang');
   $createToken=Mage::getStoreConfig('payment/icreditgateway/create_token');
   if($createToken==1){$createTokenVal='true';
   }else{$createTokenVal='false';}
   $exemptVat=Mage::getStoreConfig('payment/icreditgateway/exempt_vat');
  
   $_order = new Mage_Sales_Model_Order();
   $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
   $_order->loadByIncrementId($orderId);

   //Check For shipping & Billing country and select language and Vat value//
   $shippingaddress = $_order->getShippingAddress();   
   $billingaddress = $_order->getBillingAddress();   
   $addr1='';
   Foreach($billingaddress->getStreet() as $addr)
   {
   	 $addr1=$addr1."".$addr;
   }
   //get document language//
   $shipCountId=$shippingaddress->getCountryId();
   $billCountId=$billingaddress->getCountryId();   
    switch ($invoiceLanguage){
      case "hebrew":
        $invoiceLanguageVal="he";
        break;
      case "en":
        $invoiceLanguageVal="en";
        break;
      case "billing":
        if($billCountId=='IL'){
          $invoiceLanguageVal="he";
        }else{$invoiceLanguageVal="en";}
        break;
      case "shipping":
        if($shipCountId=='IL'){
          $invoiceLanguageVal="he";
        }else{$invoiceLanguageVal="en";}
        break;
      case "billingorshipping":
      if($shipCountId=='IL'OR $billCountId=='IL'){
        $invoiceLanguageVal="he";
      }else{$invoiceLanguageVal="en";}
      break;
      default:
        $invoiceLanguageVal="en";
      }

   //get Exempt Vat value//
   switch ($exemptVat){
      case "chargevat":
        $exemptVatVal="false";
        break;
      case "exempt":
         $exemptVatVal="true";
        break;
      case "billing":
        if($billCountId=='IL'){
          $exemptVatVal="false";
        }else{$exemptVatVal="true";}
        break;
      case "shipping":
        if($shipCountId=='IL'){
          $exemptVatVal="false";
        }else{$exemptVatVal="true";}
        break;
      case "billingorshipping":
      if($shipCountId=='IL'OR $billCountId=='IL'){
        $exemptVatVal="false";
      }else{$exemptVatVal="true";}
      break;
      default:
        $exemptVatVal="true";
      }
   //get Country Name from Code
      $countryCode = $billingaddress->getCountryId();
      $countryName = Mage::getModel('directory/country')->load($countryCode)->getName();

   //get all cart Items//

   (float) $totalamt = 0;
   $ordered_items = $_order->getAllVisibleItems();
   Foreach($ordered_items as $item){   	
   $items[]=array(
    	    'Id'=>0,
    	    'CatalogNumber' => $item->getSku(),
          'Quantity' => $item->getQtyOrdered(),
          'UnitPrice' => $item->getPrice(),
          'Description' => $item->getName()
    	 ); 
   $totalamt = (float)$totalamt +($item->getQtyOrdered()*$item->getPrice());
   $totalamt = number_format((float)$totalamt, 4, '.', '');
   }
   $items[]=array(
           'Id' => 0,
           'CatalogNumber' => '0',
           'Quantity' => 1,
           'UnitPrice' => $_order->getShippingAmount(),
           'Description' => $_order->getShippingDescription()
   );
   if($_order->getTaxAmount()!=0.0000){
   $items[]=array(
           'Id' => 0,
           'CatalogNumber' => '0',
           'Quantity' => 1,
           'UnitPrice' => $_order->getTaxAmount(),
           'Description' => 'Tax'
   );}
   //Get Discount//
   
   $cartTotal = ($totalamt + $_order->getShippingAmount() + $_order->getTaxAmount());
   $totalAmt = $_order->getGrandTotal();
   $discount = $cartTotal - $totalAmt;

    switch ($_order->getOrderCurrencyCode()){
      case "ILS":
        $currency = 1;
        break;
      case "USD":
        $currency = 2;
        break;
      case "EUR":
        $currency = 3;
        break;
      case "GBP":
        $currency = 4;
        break;
      case "AUD":
        $currency = 5;
        break;
      case "CAD":
        $currency = 6;
        break;
      case "CHF":
        $currency = 7;
        break;
      default:
        $currency = 0;
      } 
    
       
    $data = array(
		       'GroupPrivateToken' =>$groupToken,
		       'RedirectURL'=>$redirectUrl,
		       'IPNURL'=>$ipnUrl,
		       'ExemptVAT'=>$exemptVatVal,
		       'MaxPayments'=>$maxPayment,
		       'CreditFromPayment'=>$creditFromPayment,
		       'EmailAddress'=>$_order->getCustomerEmail(),
		       'CustomerLastName'=>$_order->getCustomerLastname(),
		       'CustomerFirstName' =>$_order->getCustomerFirstname(),   
		       'Address'=>$addr1,
		       'City'=>$billingaddress->getCity().','.$countryName,
		       'PhoneNumber'=>$billingaddress->getTelephone(),
		       'FaxNumber'=>$billingaddress->getFax(),
		       'VatNumber' =>$billingaddress->getVatId(),
		       'Comments'=>$_order->getCustomerNote(),
		       //'CustomerId'=>$_order->getCustomerId(),
		       'Order' => $_order->getIncrementId(),
		       'Currency' => $currency,  
		       'HideItemList'=>$hideItemListVal,
		       'DocumentLanguage'=>$invoiceLanguageVal,
		       'CreateToken'=>$createTokenVal,
		       'Discount'=>$discount,
                       "PriceIncludeVAT"=>true,
	               'Items' => $items
     );
     if(is_numeric($billingaddress->getPostcode())){
           $data['Zipcode']= $billingaddress->getPostcode();
      }
      if($testMode==0){$url = "https://icredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";}
    else{$url = "https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";}
		    //$url = "https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";
		    $content = json_encode($data); 
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //curl error SSL certificate problem, verify that the CA cert is OK
			 
			$result     = curl_exec($curl);
			$response   = json_decode($result);
			$url_to_view = $response->URL;
			//var_dump($response);
			curl_close($curl);
			return $url_to_view;
    }
}
