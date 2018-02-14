<?php

/**
 * Digiselfie Login 	login block
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Block_Login extends Digiselfie_Login_Block_Login_Provider
{
    protected function _construct() {
        parent::_construct();

        Mage::register('digiselfie_login_button_text', $this->__('Login'));
        $this->setTemplate('digiselfie/login/login.phtml');
    }
}
