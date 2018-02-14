<?php

class Digiselfie_Login_Block_Login_Provider extends Mage_Core_Block_Template
{
    protected $clientDigiselfie = null;

    protected $numEnabled = 0;
    protected $numDescShown = 0;
    protected $numButtShown = 0;

    protected function _construct()
    {
        parent::_construct();

        $this->clientDigiselfie = Mage::getSingleton('digiselfie_login/digiselfie_client');

        if( !$this->_digiselfieEnabled() )
            return;

        if($this->_digiselfieEnabled()) {
            $this->numEnabled++;
        }
    }

    protected function _getColSet()
    {
        return 'col'.$this->numEnabled.'-set';
    }

    protected function _getDescCol()
    {
        return 'col-'.++$this->numDescShown;
    }

    protected function _getButtCol()
    {
        return 'col-'.++$this->numButtShown;
    }

    protected function _digiselfieEnabled()
    {
        return $this->clientDigiselfie->isEnabled();
    }

}