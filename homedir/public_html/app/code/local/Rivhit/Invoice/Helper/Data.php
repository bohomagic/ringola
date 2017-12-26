<?php

class Rivhit_Invoice_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function myData($order_id){
            
		$api_token           = Mage::getStoreConfig('rivhit_invoice/general/apitoken');
		$document_type       = Mage::getStoreConfig('rivhit_invoice/general/doctype');
		$configcustomer_id   = Mage::getStoreConfig('rivhit_invoice/general/defaultcustomerid');
		$sort_code           = Mage::getStoreConfig('rivhit_invoice/general/sortcode');
                $sort_code_exampt    = Mage::getStoreConfig('rivhit_invoice/general/sortcodeexampt');
		$price_include_vat   = Mage::getStoreConfig('rivhit_invoice/general/examptvat');
		$language            = Mage::getStoreConfig('rivhit_invoice/general/invoicelanguage');
		$digital_signature   = Mage::getStoreConfig('rivhit_invoice/general/digitallysign');
		$signature_pin       = Mage::getStoreConfig('rivhit_invoice/general/digitalsignaturepin');
		$find_by_email       = Mage::getStoreConfig('rivhit_invoice/general/findbyemail');
		$create_items        = Mage::getStoreConfig('rivhit_invoice/general/createitem');
		$create_customer     = Mage::getStoreConfig('rivhit_invoice/general/createcustomer');
		$sendemail           = Mage::getStoreConfig('rivhit_invoice/general/sendemail');		
  
		

		if($find_by_email==1){
			$find_by_email ='true';
		}else{
			$find_by_email ='false';
		}

		if($create_items==1){
			$create_items ='true';
		}else{
			$create_items ='false';
		}

		if($digital_signature==1){
			$digital_signature ='true';
		}else{
			$digital_signature ='false';
		}
		if($create_customer==1){
			$create_customer ='true';
		}else{
			$create_customer ='false';
		}

		if($sendemail==1){
			$sendemail ='true';
		}else{
			$sendemail ='false';
		}

		
		$order = Mage::getModel('sales/order')->load($order_id);
		echo "<pre>";
		$shippingAddress = $order->getShippingAddress();
		$address         = $shippingAddress->getData('street');
		$city            = $shippingAddress->getData('city');
		$countryId       = $shippingAddress->getData('country_id');
		$state           = $shippingAddress->getData('region');
		$phone           = $shippingAddress->getData('telephone');
		$discount_amount = $order->getDiscountAmount();
                $billCountId     = $order->getBillingAddress()->getData('country_id');
		
		// get order item collection
		$orderItems = $order->getAllVisibleItems(); 
		$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
		$amount_nis = $order->getSubtotal();
		
                
                if($payment_method_code != 'icredit'){
                
		$customer_id = $order->getCustomerId(); 
		if(!$customer_id){
			$customer_id = $configcustomer_id;
		}
		
	    $last_name = $order->getCustomerLastname();
	    $first_name = $order->getCustomerFirstname();
	    $email_to = $order->getCustomerEmail();
		foreach ($orderItems as $item){
		 
		    $product_id = $item->product_id;
		    $product_sku = $item->sku;
		    $product_price = $item->getPrice();
		    $product_qty = number_format($item->getQtyOrdered());
		    $product_name = $item->getName();
		    $_product = Mage::getModel('catalog/product')->load($product_id);
		    $cats = $_product->getCategoryIds();
		    $category_id = $cats[0]; // just grab the first id
		    $category = Mage::getModel('catalog/category')->load($category_id);
		    $category_name = $category->getName();
		    $product_desc = $_product->getDescription();
   
    //get document language//  
    switch ($language){
      case "hebrew":
        $language="he";
        break;
      case "en":
        $language="en";
        break;
      case "billing":
        if($billCountId=='IL'){
          $language="he";
        }else{$language="en";}
        break;
      case "shipping":
        if($countryId=='IL'){
          $language="he";
        }else{$language="en";}
        break;
      case "billingorshipping":
      if($countryId=='IL'OR $billCountId=='IL'){
        $language="he";
      }else{$language="en";}
      break;
      default:
        $language="en";
      }                
                    
                    
   //get Exempt Vat value//
   switch ($price_include_vat){
      case "chargevat":
        $chargeVat=$sort_code;
        break;
      case "exempt":
         $chargeVat=$sort_code_exampt;
        break;
      case "billing":
        if($billCountId=='IL'){
          $chargeVat=$sort_code;
        }else{$chargeVat=$sort_code_exampt;}
        break;
      case "shipping":
        if($countryId=='IL'){
          $chargeVat=$sort_code;
        }else{$chargeVat=$sort_code_exampt;}
        break;
      case "billingorshipping":
      if($countryId=='IL'OR $billCountId=='IL'){
        $chargeVat=$sort_code;
      }else{$chargeVat=$sort_code_exampt;}
      break;
      default:
        $chargeVat=$sort_code;
      }
                    
          
                
                    
                    
		    
		    $items[] =  array(
					'catalog_number' => $product_sku,
					'quantity' =>$product_qty,
					'price_nis' =>$product_price,
					'description'=>$product_desc,
                            );
                    
		
		}




		$items[]=array(
                                        
                                        'catalog_number' => "",
                                        'quantity' => 1,
                                        'price_nis' => $order->getShippingAmount(),
                                        'description' => $order->getShippingDescription(),
                            );


                
                
       $currency = "1";            
$currency = $order->getOrderCurrencyCode();
switch ($currency){
        case "ILS":
          $currency="1";
          break;
        case "USD":
          $currency="2";
          break;
        case "EUR":
          $currency="3";
          break;
        case "GBP":
          $currency="4";
          break;
        case "AUD":
          $currency="5";
          break;
        case "CAD":
          $currency="6";
          break;
        }      
        
		$payments[] = array(
                          'payment_type' => 2,
			  'payment_type' => 10,
			  'amount_nis' => $amount_nis + $order->getShippingAmount() + $discount_amount,
                          'find_by_mail' => $find_by_email,
                          'create_items' => $create_items
			 			  );

		$data = array(
			       'api_token' => $api_token,
			       'document_type'=> $document_type,
			       'customer_id'=>$configcustomer_id,
			       'last_name'=>$last_name,
			       'first_name'=>$first_name,
			       'address'=> $address,
			       'city'=>$city,
			       'country'=> $countryId,
			       'state'=> $state,
			       'phone'=> $phone,
			       'order'=> $order->increment_id,
			       'sort_code'=>$chargeVat,
			       'discount_type'=> 2,
			       'discount_value'=> abs($discount_amount),
			       'price_include_vat'=>"true",
			       'language'=> $language,
			       'email_to'=> $email_to,
			       'digital_signature'=> $digital_signature,
			       'signature_pin' => $signature_pin,
			       'items'=>$items,
			       'payments' =>$payments,   
			       'create_customer'=>$create_customer,
			       'send_mail'=> $sendemail,
			       'find_by_mail'=>$find_by_email,
			       'create_items'=>$create_items,
                                //"request_reference"=>$order_id,
                                //"prevent_duplicates"=>true,
                                "Currency" => $currency, 
	     );
                          
		
		    $url = "https://api.rivhit.co.il/online/RivhitOnlineAPI.svc/Document.New";
		    $content = json_encode($data,JSON_NUMERIC_CHECK);
		    
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //curl error SSL certificate problem, verify that the CA cert is OK
			 
			$result     = curl_exec($curl);
			$response   = json_decode($result);
			$url_to_view = $response->data->document_link;
			
			curl_close($curl);
                        if(!$url_to_view){
                        Mage::getSingleton('core/session')->addError($response->client_message);
                        Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
                        Mage::app()->getResponse()->sendResponse();   
                        }
			return $url_to_view;
                }else{
                    Mage::getSingleton('core/session')->addError('Only paypal invoices');
                    Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
                    Mage::app()->getResponse()->sendResponse();
            exit;
                }
	}
}

