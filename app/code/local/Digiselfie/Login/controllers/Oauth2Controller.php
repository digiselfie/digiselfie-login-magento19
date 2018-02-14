<?php

/**
 * Digiselfie Login Digiselfie Controller
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Oauth2Controller extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->client = Mage::getSingleton('digiselfie_login/digiselfie_client');
       // Mage::log($redirect, null, 'system.log', true);

        $this->_redirectUrl( $this->client->createAuthUrl());
    }

}
