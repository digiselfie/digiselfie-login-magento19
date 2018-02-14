<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('admin/user'), 'digiselfie_login_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 256,
    'nullable' => true,
    'default' => null,
    'comment' => 'Digiselfie login'
));

$installer->endSetup();