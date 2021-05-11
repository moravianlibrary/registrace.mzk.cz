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
use Registration\Service\DiscountService;

class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var CodeBook */
    protected $codeBook;

    /** @var Translator */
    protected $translator;

    /** @var DiscountService */
    protected $discountService;

    public function __construct(CodeBook $codeBook, Translator $translator,
        DiscountService $discountService)
    {
        parent::__construct();
        $this->codeBook = $codeBook;
        $this->translator = $translator;
        $this->discountService = $discountService;
    }

    public function init()
    {
        parent::init();
        // First name
        $this->add([
            'name'    => 'firstName',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_firstName',
                'required' => true,
                'protected' => true,
            ],
        ]);
        // Last name
        $this->add([
            'name'    => 'lastName',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_lastName',
                'required' => true,
                'protected' => true,
            ],
        ]);
        // Degree
        $this->add([
            'name'    => 'degree',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_degree',
            ],
        ]);
        // Email
        $this->add([
            'name'    => 'email',
            'type'    => Email::class,
            'options' => [
                'label' => 'label_email',
                'protected' => true,
            ],
            'attributes' => [
                'data-help-icon' => 'help_email',
            ]
        ]);
        // Phone number
        $this->add([
            'name'    => 'phone',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_phone',
                'protected' => true,
            ],
            'attributes' => [
                'value' => '+420 ',
                'data-help-icon' => 'help_phone',
            ],
        ]);
        // Is contact address
        $this->add([
           'name'    => 'isContactAddress',
           'type'    => Checkbox::class,
           'options' => [
               'label' => 'label_isContactAddress',
           ],
           'attributes' => [
               'data-toggle' => 'collapse',
               'data-target' => '#collapseisContactAddress',
           ],
       ]);
        // Proof of identity
        $this->add([
            'name'    => 'identificationType',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_identificationType',
                'value_options' => [
                    'IC' => $this->translator->translate('option_identificationType_ic'),
                    'PAS' => $this->translator->translate('option_identificationType_pas'),
                ],
            ],
            'attributes' => [
                'data-help-icon' => 'help_id',
            ]
        ]);
        $this->add([
            'name'    => 'identification',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_identification',
                'required' => true,
            ],
        ]);
        // Date of birth
        $this->add([
            'name'    => 'birth',
            'type'    => DateSelect::class,
            'attributes' => [
                'value' => (date("Y") - 26) . '-01-01',
                'data-help-icon' => 'help_birth',
            ],
            'options' => [
                'label' => 'label_birth',
                'required' => true,
                'min_year'  => '1900',
                'max_year'  => date("Y") - 15,
                'protected' => true,
            ],
        ]);
        // Discount
        $this->add([
            'id'      => 'discount',
            'name'    => 'discount',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_discount',
                'value_options' => $this->getDiscounts(),
            ],
        ]);
        // IDS JMK
        $this->add([
            'id'      => 'idsJmk',
            'name'    => 'idsJmk',
            'type'    => Checkbox::class,
            'options' => [
                'label' => 'label_idsJmk',
            ],
        ]);
        $this->add([
            'name'    => 'university',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_university',
                'value_options' => $this->codeBook->getUniversities(),
            ],
        ]);
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
                    [
                        'name' => Validator\Callback::class,
                        'options' => [
                            'message' => 'userForm_ageNotValid',
                            'callback' => function($value, $context=[]) {
                                $now = new \DateTime(date("d-m-Y"));
                                $birth = new \DateTime($value['day'] . '-' . $value['month'] . '-' . $value['year']);
                                $age = $birth->diff($now)->y;
                                return ($age >= 15);
                            },
                        ]
                    ],
                ],
            ],
        ];
    }

    public function getDiscount()
    {
        return $this->discountService->getByCode(
            $this->get('discount')->getValue());
    }

    protected function getDiscounts()
    {
        $discounts = [];
        foreach ($this->discountService->getAll() as $code => $discount) {
            $discounts[$code] = $discount['label'];
        }
        return $discounts;
    }

}
