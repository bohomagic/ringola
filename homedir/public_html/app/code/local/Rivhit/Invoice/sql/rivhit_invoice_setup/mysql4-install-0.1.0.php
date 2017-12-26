<?php
  
$installer = $this;
  
$installer->startSetup();

$installer->run("
  
-- DROP TABLE IF EXISTS {$this->getTable(`rivhit_invoice`)};
CREATE TABLE {$this->getTable('rivhit_invoice')} (
  `invoice_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `link` text NOT NULL default '',
  `filename` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
    ");
  
$installer->endSetup(); 