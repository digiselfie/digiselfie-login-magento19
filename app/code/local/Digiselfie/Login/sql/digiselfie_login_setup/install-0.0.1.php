<?php

/**
 * Digiselfie Login installer 0.0.1
 *
 * @category   Digiselfie
 * @package    Digiselfie_Login
 * @author     Digiselfie Inc.
 */


$installer = $this;

$installer->startSetup();

$installer->setSocialCustomerAttributes(

    array(

        'digiselfie_login_id' => array(
            "type"     => "text",
            "backend"  => "",
            "label"    => "DigiSelfie Login ID",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""
        ),

        'digiselfie_login_token' => array(

            "type"     => "text",
            "backend"  => "",
            "label"    => "DigiSelfie Login Token",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""
        ),

    )

);

// Install our custom attributes

$installer->installSocialCustomerAttributes();

//$installer->removeSocialCustomerAttributes();

$installer->endSetup();
