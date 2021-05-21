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
                'firstName' => 'Tester',
                'lastName' => 'Testovič',
                'email' => 'tester@mzk.cz',
                'phone' => '+420 800 123 456',
                'birth' => [
                    'day' => 26,
                    'month' => 04,
                    'year' => 1986
                ],
                'identificationType' => 'IC',
                'identification' => '123456789',
            ],
            'permanentAddress' => [
                'street' => 'Kounicova 65a',
                'city' => 'Brno',
                'postcode' => '602 00',
                'country' => 'CZ',
            ],
            'valid' => true,
        ];
    }

}