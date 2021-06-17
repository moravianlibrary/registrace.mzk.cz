<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request;

class EduId implements IdentityProviderInterface
{

    public function identify(Request $request)
    {
        $result = [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
                'phone' => $this->get($request, 'phone'),
                'birth' => $this->get($request, 'birth'),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'street'),
                'city' => $this->get($request, 'city'),
                'postcode' => $this->get($request, 'postcode'),
            ],
            'verified' => false,
        ];
        return $result;
    }

    public function get(Request $request, string $variable)
    {
        return $request->getServer($variable);
    }

}