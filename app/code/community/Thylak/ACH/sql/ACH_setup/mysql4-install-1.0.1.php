<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Inmedias
 * @package    Inmedias_Wirecard
 * @copyright  Copyright (c) 2009 Andreas von Studnitz, team in medias GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$read = Mage::getSingleton('core/resource')
    ->getConnection('core_read');
    
$eid = $read->fetchRow("
    SELECT entity_type_id
    FROM eav_entity_type
    WHERE entity_type_code = 'order_payment';
");
$payment_type_id = $eid['entity_type_id'];

$eid = $read->fetchRow("
    SELECT entity_type_id
    FROM eav_entity_type
    WHERE entity_type_code = 'quote_payment';
");
$quote_type_id = $eid['entity_type_id'];

$installer = $this;
$installer->startSetup();
 
// create new payment attribute GuWID
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'wirecard_guwid',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
    
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'wirecard_transaction_type',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();

// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'account_owner',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'account_number',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'bank_number',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $payment_type_id,
    'attribute_code'  => 'bank_name',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();

 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $quote_type_id,
    'attribute_code'  => 'account_owner',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $quote_type_id,
    'attribute_code'  => 'account_number',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $quote_type_id,
    'attribute_code'  => 'bank_number',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
 
// create new payment attribute Transaction Type    
$attributeData = array(
    'entity_type_id'  => $quote_type_id,
    'attribute_code'  => 'bank_name',
    'backend_type'    => 'varchar',
    'frontend_input'  => 'text',
    'is_global'       => 1,
    'is_visible'      => 0,
    'is_required'     => 0,
    'is_user_defined' => 1,
);

$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($attributeData['entity_type_id'], $attributeData['attribute_code'])
    ->setStoreId(0)
    ->addData($attributeData)
    ->save();
   
// create database table 
$installer->run("
CREATE TABLE {$this->getTable('log_wirecard')} (
  `log_wirecard_id` int(10) unsigned NOT NULL auto_increment,
  `request_date` timestamp default NOW(),
  `order_id` int(10) unsigned NOT NULL default 0,
  `function` VARCHAR(32) default NULL,
  `request_xml` TEXT default NULL,
  `response_xml` TEXT default NULL,
  PRIMARY KEY  (`log_wirecard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('sales_flat_quote_payment')} 
  ADD `account_owner` VARCHAR( 255 ) NULL ,
  ADD `account_number` VARCHAR( 255 ) NULL ,
  ADD `bank_number` VARCHAR( 255 ) NULL ,
  ADD `bank_name` VARCHAR( 255 ) NULL 
");
 
$installer->endSetup();