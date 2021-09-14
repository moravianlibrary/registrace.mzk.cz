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
        $student = ($user != null)? $user->get('discountEntitlement')
                ->getValue() == 'student' : false;
        if ($age < self::MIN_AGE) {
            return [];
        }
        // try to find discount by age
        $preferred = $this->findDiscountByAge($user);
        $verified = ($user == null) ? false : $user->get('verified')
            ->getValue() == '1';
        // only add cheaper discounts
        $discounts = [];
        $idsJmk = $user->get('user')->get('idsJmk')->getValue();
        foreach ($this->discounts as $code => $discount) {
            if ($this->validate($discount, $user) &&
                ($preferred == null
                    || $preferred['price'] > $discount['price']
                    || $preferred == $discount)) {
                if ($discount['student'] ?? false) {
                    $online = $student && $verified;
                    $discount['online'] = $student && $verified;
                } else {
                    $discount['online'] = $discount['online'] && $verified;
                }
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
            'UNIVERSITY_STUDENT' => [
                'label'          => $this->translator->translate('discount_university_student'),
                'price'          => 100,
                'online'         => false,
                'proof'          => true,
                'student'        => true,
                'min_age'        => 19,
                'max_age'        => 25,
                'ids_jmk'        => self::IDS_JMK_NO_DISCOUNT,
                'group'          => 'UNIVERSITY_STUDENT',
                'payment_number' => '0901',
                'payment_name'   => 'B Online registrace roční - student',
                'bor-status'     => '04',
            ],
            'UNIVERSITY_STUDENT_IDS_JMK' => [
                'label'          => $this->translator->translate('discount_university_student_ids_jmk'),
                'price'          => 90,
                'online'         => false,
                'proof'          => true,
                'student'        => true,
                'min_age'        => 19,
                'max_age'        => 25,
                'ids_jmk'        => self::IDS_JMK_DISCOUNT,
                'group'          => 'UNIVERSITY_STUDENT',
                'payment_number' => '0901',
                'payment_name'   => 'B Online registrace roční - student',
                'bor-status'     => '04',
            ],
            // default - no discount
            'NONE' => [
                'label'          => $this->translator->translate('discount_none'),
                'price'          => 200,
                'online'         => true,
                'proof'          => false,
                'ids_jmk'        => self::IDS_JMK_NO_DISCOUNT,
                'group'          => 'NONE',
                'payment_number' => '0902',
                'payment_name'   => 'B Online registrace roční',
                'bor-status'     => '03',
            ],
            'NONE_IDS_JMK' => [
                'label'          => $this->translator->translate('discount_none_ids_jmk'),
                'price'          => 180,
                'online'         => false,
                'proof'          => true,
                'ids_jmk'        => self::IDS_JMK_DISCOUNT,
                'group'          => 'NONE',
                'payment_number' => '0902',
                'payment_name'   => 'B Online registrace roční',
                'bor-status'     => '03',
            ],
            // by age limits
            'TEENAGER' => [
                'label'          => $this->translator->translate('discount_teenager'),
                'price'          => 0,
                'online'         => true,
                'proof'          => true,
                'min_age'        => self::MIN_AGE,
                'max_age'        => 20,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'TEENAGER',
                'bor-status'     => '04',
            ],
            'SENIOR' => [
                'label'          => $this->translator->translate('discount_senior'),
                'price'          => 100,
                'online'         => false,
                'proof'          => true,
                'min_age'        => 65,
                'max_age'        => 69,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'SENIOR',
                'payment_number' => '0904',
                'payment_name'   => 'B Online registrace roční - senior',
                'bor-status'     => '03',
            ],
            'OLD_SENIOR' => [
                'label'          => $this->translator->translate('discount_old_senior'),
                'price'          => 0,
                'online'         => false,
                'proof'          => true,
                'min_age'        => 70,
                'max_age'        => self::MAX_AGE,
                'only_age'       => true,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'OLD_SENIOR',
                'payment_number' => '0907',
                'payment_name'   => 'B Online registrace roční - zdarma (nad 70 let)',
                'bor-status'     => '03',
            ],
            // free
            'UNOB_EMPLOYEE' => [
                'label'          => $this->translator->translate('discount_unob_employee'),
                'price'          => 0,
                'online'         => false,
                'proof'          => true,
                'min_age'        => 19,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'UNOB_EMPLOYEE',
                'bor-status'     => '03',
            ],
            'UNOB_STUDENT' => [
                'label'          => $this->translator->translate('discount_unob_student'),
                'price'          => 0,
                'online'         => false,
                'proof'          => true,
                'min_age'        => 19,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'UNOB_STUDENT',
                'bor-status'     => '04',
            ],
            'ZTP' => [
                'label'          => $this->translator->translate('discount_ztp'),
                'price'          => 0,
                'online'         => false,
                'proof'          => true,
                'min_age'        => 19,
                'ids_jmk'        => self::IDS_JMK_NOT_APPLICABLE,
                'group'          => 'ZTP',
                'bor-status'     => '03',
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
