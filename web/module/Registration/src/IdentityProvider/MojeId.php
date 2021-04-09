<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

class MojeId implements IdentityProviderInterface
{

    public function identify(Request $request)
    {
        return [
            'user' => [
                'firstName' => $request->getEnv('firstName'),
                'lastName' => $request->getEnv('lastName'),
                'email' => $request->getEnv('mail'),
                'phone' => $request->getEnv('phone'),
            ]
        ];
    }

}