<?php
/**
 * Overcoming Magento's Full Page Cache
 * http://www.pixafy.com/blog
 *
 * @category    Pixafy
 * @package     Pixafy_ExampleFPC
 * @copyright   Copyright (c) 2013 Pixafy (http://www.pixafy.com)
 * @author      Thomas Lackemann
 */

class Pixafy_ExampleFPC_Block_Message extends Mage_Core_Block_Template
{
    protected $message;
    
    protected function _construct()
    {
        $this->setTemplate('pixafy/examplefpc/message.phtml');
        
        // Generate some random numbers
        $_luckyNumbers = array(rand(0,99),rand(0,99),rand(0,99),rand(0,99),rand(0,99));
        
        // Grab the current time
        $_time = Mage::getSingleton('core/date')->timestamp(time());
        
        // Set our message
        $this->message = "Hi! Your lucky numbers are ".implode(', ',$_luckyNumbers).". <small>Last updated: ".date('F d Y, h:i:s a',$_time);
    }
    
    protected function _toHtml()
    {
        return $this->message;
    }
    
}