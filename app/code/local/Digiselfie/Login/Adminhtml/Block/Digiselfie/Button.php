<?php

/**
 * Digiselfie Login  Digiselfie/Button block
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */


class Digiselfie_Login_Adminhtml_Block_Digiselfie_Button extends Mage_Adminhtml_Block_Template
{
    public function getDigiselfieApiKey()
    {
        return Mage::getStoreConfig('digiselfie/digiselfie_login/digiselfie_api_key', Mage::app()->getStore()->getId());
    }
}

