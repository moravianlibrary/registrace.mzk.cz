<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

class MojeId implements IdentityProviderInterface
{

    public function identify(Request $request)
    {
        return [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
                'phone' => str_replace('.', ' ', $this->get($request, 'phone')),
                'birth' => $this->get($request, 'birth'),
                'identificationType' => 'IC',
                'identification' => $this->get($request, 'mojeIdIdentityCardNumber'),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'mojeIdStreet'),
                'city' => $this->get($request, 'mojeIdCity'),
                'postcode' => $this->get($request, 'mojeIdPostcode'),
            ],
            'verified' => true,
        ];
    }

    public function get(Request $request, $variable)
    {
        return $request->getServer($variable);
    }

}