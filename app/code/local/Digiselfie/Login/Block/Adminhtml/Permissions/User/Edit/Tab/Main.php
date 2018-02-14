<?php

class Digiselfie_Login_Block_Adminhtml_Permissions_User_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $model = Mage::registry('permissions_user');
        $form = $this->getForm();

        $fieldset = $form->addFieldset('digiselfie_fieldset', array('legend'=>Mage::helper('adminhtml')->__('DigiSelfie Information')));

        $fieldset->addField('digiselfie_login_id', 'text', array(
            'label'    => Mage::helper('adminhtml')->__('DigiSelfie ID'),
            'name'     => 'digiselfie_login_id',
            'required' => false,
            'value'    => '',
            'disabled' => false
        ));

        $data = $model->getData();
        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return $this;
    }
}