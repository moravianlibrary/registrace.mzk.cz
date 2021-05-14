<?php

namespace Registration\Service;

use Laminas\Mvc\I18n\Translator;

use Registration\Form\UserForm;

class DiscountService
{

    protected const IDS_JMK_NO_DISCOUNT = 1;

    protected const IDS_JMK_DISCOUNT = 2;

    protected const IDS_JMK_NOT_APPLICABLE = 3;

    protected const DEFAULT_AGE = 26;

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
        $discounts = [];
        foreach ($this->discounts as $code => $discount) {
            $discount['label'] = str_replace('$price',
                $discount['price'], $discount['label']);
            $discounts[$code] = $discount;
        }
        return $discounts;
    }

    public function getAvailable(?UserForm $user)
    {
        $age = ($user != null)? $user->getAge() : self::DEFAULT_AGE;
        if ($age < self::MIN_AGE) {
            return [];
        }
        // try to find discount by age
        $preferred = $this->findDiscountByAge($user);
        // only add cheaper discounts
        $discounts = [];
        $idsJmk = $user->get('user')->get('idsJmk')->getValue();
        foreach ($this->discounts as $code => $discount) {
            if ($this->validate($discount, $user) &&
                ($preferred == null
                    || $preferred['price'] > $discount['price']
                    || $preferred == $discount)) {
                $discounts[$code] = $discount;
            }
        }
        return $discounts;
    }

    public function getByCode($code)
    {
        return $this->discounts[$code];
    }

    protected function findDiscountByAge(?UserForm $user)
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
                'label'          => $this->translator->translate('discount_none'),
                'price'          => 200,
                'online'         => true,
                'ids_jmk'        => self::IDS_JMK_NO_DISCOUNT,
                'group'          => 'NONE',
                'payment_number' => '0902',
                'payment_name'   => 'B Online registrace roční'
            ],
            'NONE_IDS_JMK' => [
                'label'          => $this->translator->translate('discount_none_ids_jmk'),
                'price'          => 180,
                'online'         => false,
                'ids_jmk'        => self::IDS_JMK_DISCOUNT,
                'group'          => 'NONE',
                'payment_number' => '0902',
                'payment_name'   => 'B Online registrace roční'
            ],
            // by age limits
            'TEENAGER' => [
                'label'          => $this->translator->translate('discount_teenager'),
                'price'          => 0,
                'online'         => false,
                'min_age'        => self::MIN_AGE,
                'max_age'        => 19,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'TEENAGER',
                'payment_number' => '0906',
                'payment_name'   => 'B Online registrace roční - zdarma (12-19 let)'
            ],
            'UNIVERSITY_STUDENT' => [
                'label'          => $this->translator->translate('discount_university_student'),
                'price'          => 100,
                'online'         => false,
                'min_age'        => 19,
                'max_age'        => 25,
                'ids_jmk'        => self::IDS_JMK_NO_DISCOUNT,
                'group'          => 'UNIVERSITY_STUDENT',
                'payment_number' => '0901',
                'payment_name'   => 'B Online registrace roční - student'
            ],
            'UNIVERSITY_STUDENT_IDS_JMK' => [
                'label'          => $this->translator->translate('discount_university_student_ids_jmk'),
                'price'          => 90,
                'online'         => false,
                'min_age'        => 19,
                'max_age'        => 25,
                'ids_jmk'        => self::IDS_JMK_DISCOUNT,
                'group'          => 'UNIVERSITY_STUDENT',
                'payment_number' => '0901',
                'payment_name'   => 'B Online registrace roční - student'
            ],
            'SENIOR' => [
                'label'          => $this->translator->translate('discount_senior'),
                'price'          => 100,
                'online'         => false,
                'min_age'        => 65,
                'max_age'        => 69,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'SENIOR',
                'payment_number' => '0904',
                'payment_name'   => 'B Online registrace roční - senior'
            ],
            'OLD_SENIOR' => [
                'label'          => $this->translator->translate('discount_old_senior'),
                'price'          => 0,
                'online'         => false,
                'min_age'        => 70,
                'max_age'        => self::MAX_AGE,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'OLD_SENIOR',
                'payment_number' => '0907',
                'payment_name'   => 'B Online registrace roční - zdarma (nad 70 let)'
            ],
            // free
            'UNOB' => [
                'label'          => $this->translator->translate('discount_unob'),
                'price'          => 0,
                'online'         => false,
                'min_age'        => 19,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'UNOB',
            ],
            'ZTP' => [
                'label'          => $this->translator->translate('discount_ztp'),
                'price'          => 0,
                'online'         => false,
                'min_age'        => 19,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'ZTP',
            ],
        ];
    }

    protected function validate($discount, ?UserForm $user)
    {
        $idsJmk = $user->get('user')->get('idsJmk')->getValue();
        if (($idsJmk && $discount['ids_jmk'] == self::IDS_JMK_NO_DISCOUNT) ||
            (!$idsJmk && $discount['ids_jmk'] == self::IDS_JMK_DISCOUNT)) {
            return false;
        }
        $age = self::DEFAULT_AGE;
        if ($user != null) {
            $age = $user->getAge() ?? self::DEFAULT_AGE;
        }
        $minAge = $discount['min_age'] ?? 0;
        $maxAge = $discount['max_age'] ?? self::MAX_AGE;
        return ($age >= $minAge && $age < ($maxAge + 1));
    }

}
