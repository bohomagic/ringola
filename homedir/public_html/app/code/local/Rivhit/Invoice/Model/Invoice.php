<?php
  
class Rivhit_Invoice_Model_Invoice extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('invoice/invoice');
    }
} 