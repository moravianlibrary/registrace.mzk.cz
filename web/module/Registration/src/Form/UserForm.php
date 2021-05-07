<?php

namespace Registration\Form;

use DateTime;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Csfr;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;
use Registration\Utils\HmacCalculator;

class UserForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('userForm', []);
    }

    public function init() : void
    {
        parent::init();
        $this->add([
            'name' => 'user',
            'type' => UserFieldset::class,
        ]);
        $this->add([
            'name' => 'permanentAddress',
            'type' => FullAddressFieldset::class,
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
                'checked_value'   => 'true',
                'unchecked_value' => '',
            ],
            'attributes' => [
                'value' => 'true',
            ],
        ]);
        $this->add([
            'name'    => 'isGdpr',
            'type'    => Checkbox::class,
            'options' => [
                'label'           => 'label_isGdpr',
                'checked_value'   => 'true',
                'unchecked_value' => '',
                'required'        =>  true,
            ],
       ]);
        $this->add([
            'name' => 'submit',
            'type'  => 'Submit',
            'attributes' => [
                'value' => 'Submit registration',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    public function isValid() {
        $isContactAddress = $this->getFieldsets()['user']
            ->getElements()['isContactAddress']->getValue();
        if ($isContactAddress) {
            $contactAddress = $this->getFieldsets()['contactAddress']
                ->setOptions(['required' => true]);
        }
        return parent::isValid();
    }

    public function getInputFilterSpecification() : array
    {
        return [
            'isGdpr' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_isGdpr_required',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getAge()
    {
        $birth = $this->get('user')->get('birth')->getValue();
        return DateTime::createFromFormat('Y-m-d', $birth)->diff(new DateTime('now'))->y;
    }

    public function protect()
    {
        foreach ($this->getProtectedElements() as $name => $element) {
            $element->setAttribute('readonly', true);
        }
        $hmac = new Hidden('hmac');
        $hmac->setValue($this->getHmacHash());
        $this->add($hmac);
    }

    public function isProtected()
    {
        return $this->has('hmac');
    }

    public function getHmacHash()
    {
        $hash = new HmacCalculator();
        foreach ($this->getProtectedElements() as $name => $element) {
            $hash->add($name, $element->getValue());
        }
        return $hash->toHash();
    }

    public function setData($data)
    {
        parent::setData($data);
        if (isset($data['hmac'])) {
            $this->protect();
        }
        return $this;
    }

    private function getProtectedElements()
    {
        $elements = [];
        foreach ($this->get('user')->getElements() as $element) {
            if ($element->getOption('protected')) {
                $elements['user[' . $element->getName() . ']'] = $element;
            }
        }
        foreach ($this->get('permanentAddress')->getElements() as $element) {
            $elements['user[' . $element->getName() . ']'] = $element;
        }
        return $elements;
    }

}
