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

class Thylak_ACH_Model_Eft extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    */
    protected $_code                    = 'ACH_eft';

    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;
 
    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;
 
    /**
     * Can capture funds online?
     */
    protected $_canCapture              = true;
 
    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;
 
    /**
     * Can refund online?
     */
    protected $_canRefund               = false;
 
    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;
 
    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;
 
    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;
 
    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = false;
 
    /**
     * Can save credit card information for future processing?
     */
 
    protected $_formBlockType = 'ACH/form_eft';
    protected $_infoBlockType = 'ACH/info_eft';


    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
	
	     
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info
            ->setAccountOwner($data->getAccountOwner())
            ->setAccountNumber($data->getAccountNumber())
            ->setBankName($data->getBankName())
            ->setBankNumber($data->getBankNumber());
	   
		
        return $this;
    }

    /**
     * Send authorize request to gateway
     *
     * @param   Varien_Object $payment
     * @param   decimal $amount
     * @return  Inmedias_Wirecard_Model_Eft
     */
    public function authorize(Varien_Object $payment, $amount)
    {
    
	
        $error = false;

        if ($amount > 0) {
        
            $payment->setAmount($amount);
             
            $result = Mage::getModel('ACH/process')
                ->setPayment($payment)
                ->process();

        } else {
            $error = mage::helper('ACH')->__('Invalid amount for authorization.');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }
    
    /**
     * Send capture request to gateway
     *
     * @param   Varien_Object $payment
     * @param   decimal $amount
     * @return  Inmedias_Wirecard_Model_CreditCard
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $error = false;
		
	 
        if ($amount > 0) {
        
            $payment->setAmount($amount);

            $result = Mage::getModel('ACH/process')
                ->setPayment($payment)
                ->setIsCapture()
                ->process();

        } else {
            $error = mage::helper('ACH')->__('Invalid amount for authorization.');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function getAccountOwner()
    {
		$info = $this->getInfoInstance();
		return $info->getAccountOwner();
    }

}
