<?php

/**
 * Digiselfie Login  register block
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Block_Register extends Mage_Core_Block_Template
{
    protected $clientDigiselfie = null;

    protected $numEnabled = 0;
    protected $numShown = 0;

    protected function _construct() {
        parent::_construct();

        $this->clientDigiselfie = Mage::getSingleton('digiselfie_login/digiselfie_client');

        if( !$this->_digiselfieEnabled() )
            return;

        if($this->_digiselfieEnabled()) {
            $this->numEnabled++;
        }

        Mage::register('digiselfie_login_button_text', $this->__('Register'));

        $this->setTemplate('digiselfie/login/register.phtml');
    }

    protected function _getColSet()
    {
        return 'col'.$this->numEnabled.'-set';
    }

    protected function _getCol()
    {
        return 'col-'.++$this->numShown;
    }

    protected function _digiselfieEnabled()
    {
        return (bool) $this->clientDigiselfie->isEnabled();
    }

}
