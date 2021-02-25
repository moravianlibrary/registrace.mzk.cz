<?php
namespace Registration\Form;

use Laminas\Form\Element\Password;
use Laminas\Form\Fieldset;

class PasswordFieldset extends Fieldset
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
}