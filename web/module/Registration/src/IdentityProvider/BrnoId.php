<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request;

class BrnoId implements IdentityProviderInterface
{
    use IdentityProviderTrait;

    const REQUIRED_ATTRIBUTES = [
        'firstName',
        'lastName',
        'birth',
        'street',
        'city',
        'postcode',
        'country',
    ];

    public function identify(Request $request)
    {
        // required attributes
        $result = [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'street'),
                'city' => $this->get($request, 'city'),
                'postcode' => $this->get($request, 'postcode'),
                'country' => $this->get($request, 'country'),
            ],
        ];
        // optional attributes
        $phone = $this->get($request, 'phone');
        if ($phone != null) {
            $result['user']['phone'] = $phone;
        }
        $birth = $this->get($request, 'birth');
        if ($birth != null) {
            $result['user']['birth'] = $birth;
        }
        // verification
        $verified = strtolower($this->get($request, 'mojeIdValid')) == '1';
        $result['verified'] = $verified &&
        $this->hasAllRequiredAttributes($request) ? 1 : 0;
        // student
        $student = strtolower($this->get($request, 'mojeIdStudent')) == '1';
        $result['discountEntitlement'] = ($student) ? 'student' : 'none';
        return $result;
    }

    public function hasAllRequiredAttributes(Request $request)
    {
        if (!$this->checkNames($request)) {
            return false;
        }
        foreach (self::REQUIRED_ATTRIBUTES as $attr) {
            if (empty($this->get($request, $attr))) {
                return false;
            }
        }
        return true;
    }

}