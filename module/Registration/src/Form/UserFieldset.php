<?php
namespace Registration\Form;

use Laminas\Filter;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Mvc\I18n\Translator;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var CodeBook */
    protected $codeBook;

    /** @var Translator */
    protected $translator;

    public function __construct(CodeBook $codeBook, Translator $translator)
    {
        parent::__construct();
        $this->codeBook = $codeBook;
        $this->translator = $translator;
    }

    public function init()
    {
        parent::init();
        // First name
        $this->add([
            'name'    => 'firstName',
            'type'    => Text::class,
            'options' => [
                'label' => 'First name',
                'required' => true,
            ],
        ]);
        // Last name
        $this->add([
            'name'    => 'lastName',
            'type'    => Text::class,
            'options' => [
                'label' => 'Last name',
                'required' => true,
            ],
        ]);
        // Degree
        $this->add([
            'name'    => 'degree',
            'type'    => Text::class,
            'options' => [
                'label' => 'Degree',
            ],
        ]);
        // Is company
        $this->add([
            'name'    => 'isCompany',
            'type'    => Checkbox::class,
            'options' => [
                'label' => 'Is Company',
            ],
        ]);
        // Ico
        $this->add([
            'name'    => 'cin',
            'type'    => Text::class,
            'options' => [
                'label' => 'Company identification number',
            ],
        ]);
        // Dic
        $this->add([
            'name'    => 'tin',
            'type'    => Text::class,
            'options' => [
                'label' => 'Tax identification number',
            ],
        ]);
        // Email
        $this->add([
            'name'    => 'email',
            'type'    => Email::class,
            'options' => [
                'label' => 'Email',
            ],
        ]);
        // Phone number
        $this->add([
            'name'    => 'phone',
            'type'    => Text::class,
            'options' => [
                'label' => 'Phone',
            ],
        ]);
        // Proof of identity
        $this->add([
            'name'    => 'identificationType',
            'type'    => Select::class,
            'options' => [
                'label' => 'Identification type',
                'value_options' => [
                    'IC' => $this->translator->translate('Identity card'),
                    'PAS' => $this->translator->translate('Passport'),
                    'OTHER' => $this->translator->translate('Other identification'),
                ],
            ],
        ]);
        $this->add([
            'name'    => 'identification',
            'type'    => Text::class,
            'options' => [
                'label' => 'Identification number',
                'required' => true,
            ],
        ]);
        // Date of birth
        $this->add([
            'name'    => 'birth',
            'type'    => DateSelect::class,
            'options' => [
                'label' => 'Date of birth',
                'required' => true,
            ],
        ]);
        // Study
        $this->add([
            'id'      => 'study',
            'name'    => 'study',
            'type'    => Select::class,
            'options' => [
                'label' => 'Study',
                'value_options' => [
                    'OTHER' => $this->translator->translate('Other'),
                    'HS' => $this->translator->translate('High school'),
                    'UN' => $this->translator->translate('University'),
                ],
            ],
        ]);
        $this->add([
            'name'    => 'highSchool',
            'type'    => Select::class,
            'options' => [
                'label' => 'High school',
                'value_options' => $this->codeBook->getHighSchools(),
            ],
        ]);
        $this->add([
            'name'    => 'university',
            'type'    => Select::class,
            'options' => [
                'label' => 'University',
                'value_options' => $this->codeBook->getUniversities(),
            ],
        ]);
//        $this->add([
//            'name'    => 'discount',
//            'type'    => Select::class,
//            'options' => [
//                'label' => 'Discount',
//                'value_options' => [
//                    '100' => 'Bez slevy',
//                    '50' => 'Student do 26 let',
//                ],
//            ],
//        ]);
    }

    public function getInputFilterSpecification() : array
    {
        return [
            'firstName' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_firstName',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'lastName' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_lastName',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'email' => [
                'required' => false,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\EmailAddress::class,
                        'options' => [
                            'message' => 'userForm_missing_lastName',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'identification' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_identification',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'birth' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\NotEmpty::class,
                        'options' => [
                            'message' => 'userForm_missing_birth',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];
    }

}