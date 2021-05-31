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
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;
use Registration\Log\LoggerAwareTrait;
use Registration\Utils\HmacCalculator;

class UserForm extends Form implements InputFilterProviderInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        parent::__construct('userForm', []);
    }

    public function init(): void
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
            'name' => 'isSendNews',
            'type' => Checkbox::class,
            'options' => [
                'label' => 'label_isSendNews',
                'checked_value' => 'true',
                'unchecked_value' => 'false',
            ],
            'attributes' => [
                'value' => 'true',
                'data-help' => 'help_isSendNews',
            ],
        ]);
        $this->add([
            'name' => 'isGdpr',
            'type' => Checkbox::class,
            'options' => [
                'label' => 'label_isGdpr',
                'checked_value' => 'true',
                'unchecked_value' => 'false',
                'required' => true,
            ],
            'attributes' => [
                'data-help' => 'help_isGdpr',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Submit registration',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    public function isValid()
    {
        $isContactAddress = $this->getFieldsets()['user']
            ->getElements()['isContactAddress']->getValue();
        if ($isContactAddress) {
            $contactAddress = $this->getFieldsets()['contactAddress']
                ->setOptions(['required' => true]);
        }
        $this->get('user')->updateDiscount($this);
        return parent::isValid();
    }

    public function getInputFilterSpecification() : array
    {
        return [
            'isGdpr' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Identical::class,
                        'options' => [
                            'message' => 'userForm_isGdpr_required',
                            'token'   => 'true',
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

    public function setProtectedData($data)
    {
        foreach ($data as $group => $values) {
            if (!$this->has($group)) {
                continue;
            }
            $fieldSet = $this->get($group);
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    if (!$fieldSet->has($key)) {
                        continue;
                    }
                    $element = $fieldSet->get($key);
                    $element->setAttribute('readonly', true);
                }
            } else if (is_scalar($values)) {
                if (!$fieldSet->has($group)) {
                    continue;
                }
                $element = $this->get($group);
                $element->setAttribute('readonly', true);
            }
        }
        return $this;
    }

}
