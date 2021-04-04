<?php
namespace Registration\Form;

use Laminas\Filter;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

class AddressFieldset extends Fieldset implements InputFilterProviderInterface
{

    protected $required = false;

    public function init()
    {
        parent::init();
        // Street
        $this->add([
            'name'    => 'street',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_street',
            ],
        ]);
        // City
        $this->add([
            'name'    => 'city',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_city',
            ],
        ]);
        // Postcode
        $this->add([
            'name'    => 'postcode',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_postcode',
            ],
        ]);
    }

    public function setOptions($options)
    {
        parent::setOptions($options);
        $this->required = $options['required'] ?? false;
        // Because laminas-forms is worthless shit, we have to set element
        // options here and not in init method (because init is called
        // before setOptions!!!)
        foreach ($this->getElements() as $element) {
            $element->setOption('required', $this->required);
        }
        return $this;
    }

    public function getInputFilterSpecification() : array
    {
        if (!$this->required) {
            return [];
        }
        return [
            'street' => [
                'required' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_street',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'city' => [
                'required' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_city',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'postcode' => [
                'required' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_postcode',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];
    }

}
