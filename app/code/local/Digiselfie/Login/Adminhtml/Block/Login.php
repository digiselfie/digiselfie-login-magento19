<?php

/**
 * Digiselfie Login   login block
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Adminhtml_Block_Login extends Mage_Adminhtml_Block_Template
{
    protected $clientDigiselfie = null;

    protected function _construct() {
        parent::_construct();

        $this->clientDigiselfie = Mage::getSingleton('digiselfie_login/digiselfie_client');

        Mage::log($this->clientDigiselfie, null, 'system.log');

        $this->setTemplate('digiselfie/login/login.phtml');
    }


}
