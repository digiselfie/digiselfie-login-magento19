<?php

/**
 * Digiselfie Login   digiselfie/Userdetails Model
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Model_Digiselfie_Userdetails
{
    protected $client = null;
    protected $userInfo = null;

    public function __construct() {
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
            return;
        $this->client = Mage::getSingleton('digiselfie_login/digiselfie_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if(($digiselfieLoginId = $customer->getDigiselfieLoginId()) &&
                ($digiselfieLoginToken = $customer->getDigiselfieLoginToken())) {
            $helper = Mage::helper('digiselfie_login/digiselfie');
            try{
                $this->client->setAccessToken($digiselfieLoginToken);
                $this->userInfo = $this->client->api('/userinfo');
                /* The access token may have been updated automatically due to
                 * access type 'offline' */
                $customer->setDigiselfieLoginToken($this->client->getAccessToken());
                $customer->save();
            } catch(Digiselfie_Login_OAuthException $e) {
                $helper->disconnect($customer);
                Mage::getSingleton('core/session')->addNotice($e->getMessage());
            } catch(Exception $e) {
                $helper->disconnect($customer);
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
    }

    public function getUserDetails()
    {
        return $this->userInfo;
    }
}
