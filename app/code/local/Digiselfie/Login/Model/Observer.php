<?php

class Digiselfie_Login_Model_Observer
{
    public function customerLogin(Varien_Event_Observer $observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getCustomer();
        $digiselfieLoginId = (Mage::getModel('core/cookie')->get('digiselfie_login_id')) ?: null;
        Mage::getModel('core/cookie')->delete('digiselfie_login_id');

        if (!empty($digiselfieLoginId)) {
            $digiselfieCustomerId = (Mage::getModel('core/cookie')->get('ds_customer_id')) ?: null;
            Mage::getModel('core/cookie')->delete('ds_customer_id');

            if ($customer->getId() == $digiselfieCustomerId) {
                $customer->setDigiselfieLoginId($digiselfieLoginId)
                    ->save();
            }
        }

        //force to delete DigiSelfie cookie variables if exists
        unset($_COOKIE['digiselfie_login_id']);
        unset($_COOKIE['ds_customer_id']);

    }
}