<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request;

class EduId implements IdentityProviderInterface
{

    const REQUIRED_ATTRIBUTES = [
        'firstName',
        'lastName',
        'street',
        'city',
        'postcode',
        'country',
    ];

    public function identify(Request $request)
    {
        $result = [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
                'phone' => $this->get($request, 'phone'),
                'birth' => $this->parseDate($this->get($request, 'schacDateOfBirth')),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'street'),
                'city' => $this->get($request, 'city'),
                'postcode' => $this->get($request, 'postcode'),
                'country' => $this->get($request, 'country'),
            ],
        ];
        $result['verified'] = $this->hasAllRequiredAttributes($request);
        return $result;
    }

    protected function parseDate($date)
    {
        return substr($date, 0, 4)
            . '-' .substr($date, 4, 2)
            . '-' .substr($date, 6, 2);
    }

    protected function get(Request $request, string $variable)
    {
        return $request->getServer($variable);
    }

    protected function hasAllRequiredAttributes(Request $request)
    {
        foreach (self::REQUIRED_ATTRIBUTES as $attr) {
            if (empty($this->get($request, $attr))) {
                return false;
            }
        }
        return true;
    }

}