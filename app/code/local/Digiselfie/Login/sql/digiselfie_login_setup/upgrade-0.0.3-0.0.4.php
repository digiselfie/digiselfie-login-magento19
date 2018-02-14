<?php

$installer = $this;

$installer->startSetup();

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'digiselfie_login_id')
    ->setData('used_in_forms', array('adminhtml_customer'))
    ->save();

$installer->endSetup();