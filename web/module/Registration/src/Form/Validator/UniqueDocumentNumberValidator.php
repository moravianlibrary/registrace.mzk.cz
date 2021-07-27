<?php

namespace Registration\Form\Validator;

use Laminas\Http\Client;

class UniqueDocumentNumberValidator
{

    const ALEPH_URL = "https://aleph.mzk.cz/cgi-bin/doklad-val.pl";

    const IDENTITY_CARD_NOT_UNIQUE = "userForm_identity_card_not_unique";

    protected $lastMessages = [];

    public function isValid(string $value)
    {
        $this->lastMessages = [];
        $valid = false;
        $client = new Client(self::ALEPH_URL);
        $client->setMethod('GET');
        $client->setParameterGet([
            'doklad' => $value,
        ]);
        $response = $client->send();
        if ($response->getStatusCode() == '200') {
            $result = json_decode($response->getBody());
            $valid = ($result->Result == 'NOT FOUND');
        }
        if (!$valid) {
            $this->lastMessages[] = self::IDENTITY_CARD_NOT_UNIQUE;
        }
        return $valid;
    }

}