<?php
/**
 * Digiselfie Login Digiselfie Helper
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Helper_Digiselfie extends Mage_Core_Helper_Abstract
{

    public function connectByDigiselfieId(
            Mage_Customer_Model_Customer $customer,
            $digiselfieId
        )
    {
        $customer->setDigiselfieLoginId($digiselfieId)
                ->save();
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

    public function connectByCreatingAccount(
            $email,
            $firstName,
            $lastName,
            $digiselfieId
        )
    {
        $customer = Mage::getModel('customer/customer');

        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->setEmail($email)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setDigiselfieLoginId($digiselfieId)
                ->setPassword($customer->generatePassword(10))
                ->save();

        $customer->setConfirmation(null);
        $customer->save();
        $customer->sendNewAccountEmail('confirmed', '', Mage::app()->getStore()->getId());
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

    public function loginByCustomer(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getConfirmation()) {
            $customer->setConfirmation(null);
            $customer->save();
        }
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

    public function getCustomersByDigiselfieId($digiselfieId)
    {
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
            ->addAttributeToFilter('digiselfie_login_id', $digiselfieId)
            ->setPageSize(1);

        if($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter(
                'website_id',
                Mage::app()->getWebsite()->getId()
            );
        }

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $collection->addFieldToFilter(
                    'entity_id',
                    array('neq' => Mage::getSingleton('customer/session')->getCustomerId())
                );
            }
        }

        // If no one customer found - we need to check if user is admin
        if (!$collection->count()) {
            $adminUsers = Mage::getModel('admin/user');

            $collection = $adminUsers->getCollection()
                ->addFieldToFilter('digiselfie_login_id', $digiselfieId)
                ->setPageSize(1);

            if(Mage::getSingleton('admin/session')->isLoggedIn()) {
                $collection->addFieldToFilter(
                    'user_id',
                    array('neq' => Mage::getSingleton('admin/session')->getUser()->getId())
                );
            }
        }

        return $collection;
    }

    public function getCustomersByEmail($email)
    {
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
                ->addFieldToFilter('email', $email)
                ->addAttributeToSelect('digiselfie_login_id')
                ->setPageSize(1);

        if($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter(
                'website_id',
                Mage::app()->getWebsite()->getId()
            );
        }

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection->addFieldToFilter(
                'entity_id',
                array('neq' => Mage::getSingleton('customer/session')->getCustomerId())
            );
        }

        return $collection;
    }

    public function getProperDimensionsPictureUrl($digiselfieId, $pictureUrl)
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                .'digiselfie'
                .'/'
                .'DigiselfieLogin'
                .'/'
                .'digiselfie'
                .'/'
                .$digiselfieId;

        $filename = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                .DS
                .'digiselfie'
                .DS
                .'digiselfielogin'
                .DS
                .'digiselfie'
                .DS
                .$digiselfieId;

        $directory = dirname($filename);

        if (!file_exists($directory) || !is_dir($directory)) {
            if (!@mkdir($directory, 0777, true))
                return null;
        }

        if(!file_exists($filename) ||
                (file_exists($filename) && (time() - filemtime($filename) >= 3600))){
            $client = new Zend_Http_Client($pictureUrl);
            $client->setStream();
            $response = $client->request('GET');
            stream_copy_to_stream($response->getStream(), fopen($filename, 'w'));

            $imageObj = new Varien_Image($filename);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(150, 150);
            $imageObj->save($filename);
        }
        return $url;
    }

    public function connectByAdmin(Mage_Admin_Model_User $user)
    {
        Mage::app('default');
        Mage::getSingleton('core/session', array('name' => 'adminhtml'));

        if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            Mage::getSingleton('adminhtml/url')->renewSecretUrls();
        }

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        $session->setIsFirstVisit(true);
        //$session->setIsFirstPageAfterLogin(true);
        $session->setUser($user);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));

        return $session->isLoggedIn();
    }
}
