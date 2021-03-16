<?php

namespace Registration\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;

class UserForm extends Form
{
    public function init() : void
    {
        parent::init();
        $this->add([
            'name' => 'user',
            'type' => UserFieldset::class,
        ]);
        $this->add([
            'name' => 'permanentAddress',
            'type' => AddressFieldset::class,
            'options' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'name' => 'contactAddress',
            'type' => AddressFieldset::class,
            'options' => [
                'required' => false,
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => PasswordFieldset::class,
        ]);
        $this->add([
            'name'    => 'isSendNews',
            'type'    => Checkbox::class,
            'options' => [
                'label' => 'label_isSendNews',
            ],
       ]);
        $this->add([
            'name'    => 'isGdpr',
            'type'    => Checkbox::class,
            'options' => [
                'label' => 'label_isGdpr',
            ],
       ]);
        $this->add([
            'name' => 'submit',
            'type'  => 'Submit',
            'attributes' => [
                'value' => 'Submit registration',
                'class' => 'btn btn-primary'
            ],
        ]);
    }

}
