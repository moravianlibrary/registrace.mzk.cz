<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

class Test implements IdentityProviderInterface
{

    public function identify(Request $request)
    {
        return [
            'user' => [
                'firstName' => 'test',
            ]
        ];
    }

}