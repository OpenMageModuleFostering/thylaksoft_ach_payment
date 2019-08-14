<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Inmedias
 * @package    Inmedias_Wirecard
 * @copyright  Copyright (c) 2009 Andreas von Studnitz, team in medias GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Thylak_ACH_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Save processing data to database log
     *
     * @param   string $orderId
     * @param   string $requestXml
     * @param   string $responseXml
     * @param   string $function
     * @return  void
     */
    public function saveLog($orderId, $requestXml, $responseXml, $function = '') {
    
        $connection = Mage::getSingleton('core/resource')
            ->getConnection('core_write');
        $connection->beginTransaction();
        
        $fields = array(
            'order_id' => $orderId,
            'request_xml' => $requestXml,
            'response_xml' => $responseXml,
            'function' => $function,
        );
        
        try 
        {
            $connection->insert('log_wirecard', $fields);
            
            $connection->commit();
        }
        catch (Exception $e) 
        {
            $connection->rollBack();
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            throw $e;
        }
    }
}