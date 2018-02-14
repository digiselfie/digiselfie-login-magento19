<?php

/**
 * Digiselfie Login Account Controller
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function digiselfieAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}
