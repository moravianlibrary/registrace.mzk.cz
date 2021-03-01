<?php
namespace Registration\Form;

use Laminas\Form\Element\Password;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

class PasswordFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name'    => 'password',
            'type'    => Password::class,
            'options' => [
                'label' => 'Password',
                'required' => true,
            ],
        ]);
        $this->add([
            'name'    => 'passwordConfirm',
            'type'    => Password::class,
            'options' => [
                'label' => 'Password confirm',
                'required' => true,
            ],
        ]);
    }

    public function getInputFilterSpecification() : array
    {
        return [
            'password' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_password',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'passwordConfirm' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_passwordConfirm',
                            'type' => 'string',
                        ],
                    ],
                ],
            ]
        ];
    }
}