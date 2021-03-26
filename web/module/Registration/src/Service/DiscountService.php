<?php

namespace Registration\Service;

use Laminas\Mvc\I18n\Translator;

use Registration\Form\UserForm;

class DiscountService
{

    protected const MIN_AGE = 15;

    protected const MAX_AGE = 200;

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

    public function getAvailable(UserForm $user)
    {
        $age = $user->getAge();
        if ($age < self::MIN_AGE) {
            return [];
        }
        // try to find discount by age
        $preferred = $this->findDiscountByAge($user);
        // only add cheaper discounts
        $discounts = [];
        foreach ($this->discounts as $code => $discount) {
            if ($this->validate($discount, $user) &&
                ($preferred == null || $preferred['price'] >= $discount['price'])) {
                $discounts[$code] = $discount;
            }
        }
        return $discounts;
    }

    protected function findDiscountByAge(UserForm $user)
    {
        foreach ($this->discounts as $code => $discount) {
            $onlyAge = $discount['only_age'] || false;
            if ($onlyAge && $this->validate($discount, $user)) {
                return $discount;
            }
        }
        return null;
    }

    protected function init()
    {
        $this->discounts = [
            // default - no discount
            'NONE' => [
                'label' => $this->translator->translate('discount_none'),
                'price' => 200,
            ],
            // by age limits
            'TEENAGER' => [
                'label'    => $this->translator->translate('discount_teenager'),
                'price'    => 0,
                'min_age'  => self::MIN_AGE,
                'max_age'  => 19,
                'only_age' => true,
            ],
            'UNIVERSITY_STUDENT' => [
                'label'    => $this->translator->translate('discount_university_student'),
                'price'    => 100,
                'min_age'  => 19,
                'max_age'  => 25,
            ],
            'SENIOR' => [
                'label'    => $this->translator->translate('discount_senior'),
                'price'    => 100,
                'min_age'  => 65,
                'max_age'  => 69,
                'only_age' => true,
            ],
            'OLD_SENIOR' => [
                'label'    => $this->translator->translate('discount_old_senior'),
                'price'    => 0,
                'min_age'  => 70,
                'max_age'  => self::MAX_AGE,
                'only_age' => true,
            ],
            // free
            'UNOB' => [
                'label'    => $this->translator->translate('discount_unob'),
                'price'    => 0,
                'min_age'  => 19,
            ],
            'ZTP' => [
                'label'    => $this->translator->translate('discount_ztp'),
                'price'    => 0,
                'min_age'  => 19,
            ],
        ];
    }

    protected function validate($discount, $user)
    {
        $age = $user->getAge();
        $minAge = $discount['min_age'] ?? 0;
        $maxAge = $discount['max_age'] ?? self::MAX_AGE;
        return ($age >= $minAge && $age < ($maxAge + 1));
    }

}
