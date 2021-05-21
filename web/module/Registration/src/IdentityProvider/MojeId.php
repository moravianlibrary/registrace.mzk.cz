<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

class MojeId implements IdentityProviderInterface
{

    public function identify(Request $request)
    {
        $birth = explode('-', $this->get($request, 'birth'));
        $birth = [
            'day' => $birth[0],
            'month' => $birth[1],
            'year' => $birth[2]
        ];
        return [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
                'phone' => str_replace('.', ' ', $this->get($request, 'phone')),
                'birth' => $birth,
                'identificationType' => 'IC',
                'identification' => $this->get($request, 'mojeIdIdentityCardNumber'),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'mojeIdStreet'),
                'city' => $this->get($request, 'mojeIdCity'),
                'postcode' => $this->get($request, 'mojeIdPostcode'),
            ],
        ];
    }

    public function get(Request $request, $variable)
    {
        return $request->getServer($variable);
    }

}