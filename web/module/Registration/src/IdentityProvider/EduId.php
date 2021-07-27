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

    const REQUIRED_AFFILIATIONS = [
        'employee',
        'faculty',
        'student',
    ];

    const STUDENT_AFFILIATION = 'student';

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
        $birth = $this->get($request, 'schacDateOfBirth');
        if ($birth != null) {
            $result['user']['birth'] = $this->parseDate($birth);
        }
        // verification
        $result['verified'] = $this->hasAllRequiredAttributes($request) ? 1 : 0;
        // student
        $student = in_array(self::STUDENT_AFFILIATION,
            $this->getAffiliations($request));
        $result['discountEntitlement'] = ($student) ? 'student' : 'none';
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
        return $request->getServer($variable, null);
    }

    protected function hasAllRequiredAttributes(Request $request)
    {
        $valid = false;
        foreach ($this->getAffiliations($request) as $affiliation) {
            if (in_array($affiliation, self::REQUIRED_AFFILIATIONS)) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            return false;
        }
        foreach (self::REQUIRED_ATTRIBUTES as $attr) {
            if (empty($this->get($request, $attr))) {
                return false;
            }
        }
        return true;
    }

    protected function getAffiliations($request)
    {
        $affiliations = $this->get($request, 'eduPersonScopedAffiliation');
        if ($affiliations == null || empty($affiliations)) {
            return [];
        }
        $result = [];
        $affiliations = explode(',', $affiliations);
        foreach ($affiliations as $affiliation) {
            [$relation, $inst] = explode('@', $affiliation);
            $result[] = $relation;
        }
        return $result;
    }

}