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
            ],
        ]);
        // Last name
        $this->add([
            'name'    => 'lastName',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_lastName',
                'required' => true,
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
        // Is company
        $this->add([
            'name'    => 'isCompany',
            'type'    => Checkbox::class,
            'options' => [
                'label' => 'label_isCompany',
            ],
            'attributes' => [
                'data-toggle' => 'collapse',
                'data-target' => '#collapseIsCompany',
            ],
        ]);
        // Ico
        $this->add([
            'name'    => 'cin',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_cin',
            ],
        ]);
        // Dic
        $this->add([
            'name'    => 'tin',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_tin',
            ],
        ]);
        // Email
        $this->add([
            'name'    => 'email',
            'type'    => Email::class,
            'options' => [
                'label' => 'label_email',
            ],
        ]);
        // Phone number
        $this->add([
            'name'    => 'phone',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_phone',
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
                    'OTHER' => $this->translator->translate('option_identificationType_other'),
                ],
            ],
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
            'options' => [
                'label' => 'label_birth',
                'required' => true,
            ],
        ]);
        // Member
        $this->add([
            'id'      => 'member',
            'name'    => 'member',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_member',
                'value_options' => $this->getDiscounts(),
            ],
       ]);
        // Study
        $this->add([
            'id'      => 'study',
            'name'    => 'study',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_study',
                'value_options' => [
                    'OTHER' => $this->translator->translate('option_study_other'),
                    'HS' => $this->translator->translate('option_study_hs'),
                    'UN' => $this->translator->translate('option_study_un'),
                ],
            ],
        ]);
        $this->add([
            'name'    => 'highSchool',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_highSchool',
                'value_options' => $this->codeBook->getHighSchools(),
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
                ],
            ],
        ];
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
