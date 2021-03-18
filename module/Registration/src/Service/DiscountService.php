<?php

namespace Registration\Service;

use Laminas\Mvc\I18n\Translator;

use Registration\Form\UserForm;

class DiscountService
{

    protected $translator;

    protected $discounts;

    protected $validators;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->init();
    }

    public function getAll()
    {
        return $this->discounts;
    }

    public function getAvailable(UserForm $userForm)
    {
        $discounts = [];
        foreach ($this->discounts as $code => $discount) {
            $validator = $this->validators[$code];
            if ($validator == null || $validator($userForm)) {
                $discounts[$code] = $discount;
            }
        }
        return $discounts;
    }

    protected function init()
    {
        $this->discounts = [
            'hss' => [
                'label' => $this->translator->translate('option_member_hss'),
                'price' => '100',
            ],
            'us' => [
                'label' => $this->translator->translate('option_member_us'),
                'price' => '100',
            ],
            'none' => [
                'label' => $this->translator->translate('option_member_none'),
                'price' => '200',
            ],
            'ztp' => [
                'label' => $this->translator->translate('option_member_ztp'),
                'price' => '0',
            ],
            'uod' => [
                'label' => $this->translator->translate('option_member_uod'),
                'price' => '0',
            ],
            'itic' => [
                'label' => $this->translator->translate('option_member_itic'),
                'price' => '300',
            ]
        ];
        $this->validators = [
            'hss' => function(UserForm $user) {
                return $user->getAge() <= 19;
            },
            'us' => function(UserForm $user) {
                return $user->getAge() >= 19 && $user->getAge() <= 26;
            }
        ];
    }

}
