<?php

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

class MojeId implements IdentityProviderInterface
{

    const REQUIRED_ATTRIBUTES = [
        'firstName',
        'lastName',
        'birth',
        'mojeIdIdentityCardNumber',
        'street',
        'city',
        'postcode',
        'country',
    ];

    /** @var array */
    protected $idPs = [
        'https://mojeid.cz/saml/idp.xml'
    ];

    public function __construct(bool $test)
    {
        if ($test) {
            $this->idPs[] = 'https://mojeid.regtest.nic.cz/saml/idp.xml';
        }
    }

    public function identify(Request $request)
    {
        $entityId = $this->get($request, 'Shib-Identity-Provider');
        if (!in_array($entityId, $this->idPs)) {
            return null;
        }
        $result = [
            'user' => [
                'firstName' => $this->get($request, 'firstName'),
                'lastName' => $this->get($request, 'lastName'),
                'email' => $this->get($request, 'mail'),
                'phone' => str_replace('.', ' ', $this->get($request, 'phone')),
                'birth' => $this->get($request, 'birth'),
                'identificationType' => 'OP',
                'identification' => $this->get($request, 'mojeIdIdentityCardNumber'),
            ],
            'permanentAddress' => [
                'street' => $this->get($request, 'street'),
                'city' => $this->get($request, 'city'),
                'postcode' => $this->get($request, 'postcode'),
                'country' => $this->get($request, 'country'),
            ],
        ];
        // verification
        $verified = strtolower($this->get($request, 'mojeIdValid')) == 'true';
        $result['verified'] = $verified &&
            $this->hasAllRequiredAttributes($request) ? 1 : 0;
        // student
        $student = strtolower($this->get($request, 'mojeIdStudent')) == 'true';
        $result['discountEntitlement'] = ($student) ? 'student' : 'none';
        return $result;
    }

    public function get(Request $request, string $variable)
    {
        return $request->getServer($variable);
    }

    public function hasAllRequiredAttributes(Request $request)
    {
        foreach (self::REQUIRED_ATTRIBUTES as $attr) {
            if (empty($this->get($request, $attr))) {
                return false;
            }
        }
        return true;
    }

}