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


class Thylak_ACH_Block_Form_Cc extends Mage_Payment_Block_Form_Cc
{

    protected function _construct()
    {
	     
        parent::_construct();
        $this->setTemplate('Ach_View/form_cc.phtml');
    }
}
