<?php
class Rivhit_Icreditgateway_Block_Redirect extends Mage_Core_Block_Template
{
     public function getSubmitUrl()
     {
        return Mage::getBaseUrl().'icreditgateway/payment/submit';
      
      }
     public function getIPNUrl()
     {
        return Mage::getStoreConfig('payment/icreditgateway/ipn_url');
      }
}

?>


