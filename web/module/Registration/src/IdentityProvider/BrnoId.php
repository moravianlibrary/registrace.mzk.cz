<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request;

class BrnoId implements IdentityProviderInterface
{

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
        return $result;
    }

    protected function get(Request $request, string $variable)
    {
        return $request->getServer($variable, null);
    }

}