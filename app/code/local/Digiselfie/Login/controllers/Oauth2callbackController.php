<?php

/**
 * Digiselfie Login Digiselfie Controller
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Oauth2callbackController extends Mage_Core_Controller_Front_Action
{

protected $referer = null;

    public function indexAction()
    {
        try {
            $this->_connectCallback();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        Mage::log($this->referer, null, 'system.log', true);
        if(!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            $this->_redirect('customer/account');
        }
    }

    public function disconnectAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        try {
            $this->_disconnectCallback($customer);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        if(!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            $this->_redirect('customer/account');;
        }
    }

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        $this->referer = Mage::getUrl('digiselfielogin/account/digiselfie');
        Mage::helper('digiselfie_login/digiselfie')->disconnect($customer);
        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your Digiselfie account from our store account.')
            );
    }

    protected function _connectCallback() {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        $userModel = 'customer';

        if(!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return;
        }
        $this->referer = Mage::getSingleton('core/session')->getDigiselfieRedirect();


        Mage::getSingleton('core/session')->getDigiselfieCsrf('');
        if($errorCode) {
            // Digiselfie API read light - abort
            if($errorCode === 'access_denied') {
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Digiselfie Connect process aborted.')
                    );

                return;
            }
            throw new Exception(
                sprintf(
                    $this->__('Sorry, "%s" error occured. Please try again.'),
                    $errorCode
                )
            );
            return;
        }


        if ($code) {
            // Digiselfie API green light - proceed
            $client = Mage::getSingleton('digiselfie_login/digiselfie_client');

            $userInfo = $client->api();

            $customersByDigiselfieId = Mage::helper('digiselfie_login/digiselfie')
                ->getCustomersByDigiselfieId($userInfo->ID);

            if ($customersByDigiselfieId->getFirstItem() instanceof Mage_Admin_Model_User)
            {
                $userModel = 'admin';

                if (Mage::getSingleton('admin/session')->isLoggedIn()){
                    Mage::getSingleton('adminhtml/session')
                        ->addNotice(
                            $this->__('Your Digiselfie account is already connected to admin panel.')
                        );

                    $user = Mage::getSingleton('admin/session')->getUser();

                    Mage::helper('digiselfie_login/digiselfie')->connectByAdmin($user);

                    $this->referer = Mage::getSingleton('adminhtml/url')->getUrl(Mage::getModel('admin/user')->getStartupPageUrl(), array('_current' => false));
                    $this->referer .= '?SID=' . Mage::getSingleton('admin/session')->getEncryptedSessionId();

                    return;
                }

                if ($customersByDigiselfieId->getFirstItem()->getUserId()) {
                    $user = $customersByDigiselfieId->getFirstItem();

                    $loginStatus = Mage::helper('digiselfie_login/digiselfie')->connectByAdmin($user);
                    if (!$loginStatus) {
                        Mage::getSingleton('core/session')->addError(
                            $this->__('Sorry, but we could not connect to admin panel using your Digiselfie account.')
                        );
                        return;
                    }

                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('You have successfully logged in to admin panel using your Digiselfie account.')
                    );

                    $this->referer = Mage::getSingleton('adminhtml/url')->getUrl(Mage::getModel('admin/user')->getStartupPageUrl(), array('_current' => false));
                    $this->referer .= '?SID=' . Mage::getSingleton('admin/session')->getEncryptedSessionId();

                    return;
                }
            }

            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if($customersByDigiselfieId->count()) {
                    // Digiselfie account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Your Digiselfie account is already connected to one of our store accounts.')
                        );
                    return;
                }
                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                Mage::helper('digiselfie_login/digiselfie')->connectByDigiselfieId(
                    $customer,
                    $userInfo->ID
                );
                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Your Digiselfie account is now connected to your new user accout at our store. You can login next time by the Digiselfie app or Store user account. Account confirmation mail has been sent to your email.')
                );
                return;
            }

            if($customersByDigiselfieId->count()) {
                // Existing connected user - login
                $customer = $customersByDigiselfieId->getFirstItem();
                Mage::helper('digiselfie_login/digiselfie')->loginByCustomer($customer);
                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('You have successfully logged in using your Digiselfie account.')
                    );
                return;
            }

            $email = $userInfo->ID . '@' . 'digiselfie.com';
            if(!empty($userInfo->Emails[0]->Address)) {
                $email = $userInfo->Emails[0]->Address;
            }

            $customersByEmail = Mage::helper('digiselfie_login/digiselfie')
                ->getCustomersByEmail($email);

            if($customersByEmail->count())  {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();
                $customerDigiselfieId = $customer->getDigiselfieLoginId();

                if (empty($customerDigiselfieId)) {
                    $this->referer = Mage::getBaseUrl() . 'customer/account/login';
                    //force to delete DigiSelfie cookie variables before install
                    unset($_COOKIE['digiselfie_login_id']);
                    unset($_COOKIE['ds_customer_id']);

                    //Cookie lifetime 5 min
                    Mage::getModel('core/cookie')->set('digiselfie_login_id', $userInfo->ID, 60*5);
                    Mage::getModel('core/cookie')->set('ds_customer_id', $customer->getId(), 60*5);

                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('We found you already have an account in our system. Please enter your username and password to link your account with your digiselfie.')
                        );
                    
                    return;
                }

                if (!empty($customerDigiselfieId) && $customerDigiselfieId != $userInfo->ID) {
                    Mage::getSingleton('core/session')->addError(
                        __('We found your account, but you need to login with your other digiselfie.')
                    );

                    return;
                }

                Mage::helper('digiselfie_login/digiselfie')->connectByDigiselfieId(
                    $customer,
                    $userInfo->ID
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('We find you already have an account at our store. Your Digiselfie account is now connected to your store account. Account confirmation mail has been sent to your email.')
                );
                return;
            }

            // New connection - create, attach, login
            if(empty($userInfo->FirstName)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Digiselfie first name. Please try again.')
                );
            }

            Mage::helper('digiselfie_login/digiselfie')->connectByCreatingAccount(
                $email,
                $userInfo->FirstName,
                $userInfo->LastName,
                $userInfo->ID
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your Digiselfie account is now connected to your new user accout at our store. You can login next time by the Digiselfie app or Store user account. Account confirmation mail has been sent to your email.')
            );
        }
    }


}
