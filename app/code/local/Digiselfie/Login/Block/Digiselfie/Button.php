<?php

/**
 * Digiselfie Login  Digiselfie/Button block
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */


class Digiselfie_Login_Block_Digiselfie_Button extends Mage_Core_Block_Template
{
    public function __construct() {

      parent::_construct();

      $this->client = Mage::getSingleton('digiselfie_login/digiselfie_client');

      if(!($this->client->isEnabled())) {
          return;
      }

      // $this->userInfo = Mage::registry('digiselfie_login_userdetails');

      // CSRF protection
      if(!Mage::getSingleton('core/session')->getDigiselfieCsrf() || Mage::getSingleton('core/session')->getDigiselfieCsrf()=='') {
          $csrf = md5(uniqid(rand(), TRUE));
          Mage::getSingleton('core/session')->setDigiselfieCsrf($csrf);
      } else {
          $csrf = Mage::getSingleton('core/session')->getDigiselfieCsrf();
      }

      $this->client->setState($csrf);

      if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {
          $redirect = Mage::helper('core/url')->getCurrentUrl();
      }

      // Redirect uri


      Mage::getSingleton('core/session')->setDigiselfieRedirect($redirect);
    }

    public function getDigiselfieApiKey()
    {
        return Mage::getStoreConfig('digiselfie/digiselfie_login/digiselfie_api_key', Mage::app()->getStore()->getId());
    }
}

