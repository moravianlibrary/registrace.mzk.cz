<?php

namespace Registration\Form\Validator;

use Laminas\Http\Client;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Registration\Log\LoggerAwareTrait;

class IdentityCardNumberValidator extends AbstractValidator
{
    use LoggerAwareTrait;

    const IDENTITY_CARD_INVALID = "userForm_identity_card_invalid";

    const BASE_URL = "https://aplikace.mvcr.cz/neplatne-doklady/doklady.aspx";

    const TIMEOUT = 5;

    protected $lastMessages = [];

    public function isValid($value)
    {
        $this->lastMessages = [];
        $client = new Client(self::BASE_URL, [ 'timeout' => self::TIMEOUT ]);
        $client->setMethod('GET');
        $client->setParameterGet([
            'dotaz' => $value,
            'doklad' => 0,
        ]);
        $response = $client->send();
        // Do not prevent user from registration when service is not available
        if ($response->getStatusCode() != '200') {
            return true;
        }
        $body = $response->getBody();
        $this->getLogger()->info("Response body: " . $body);
        $xml = @simplexml_load_string($body);
        // Invalid XML, assume that the service is not available
        if (!$xml) {
            return true;
        }
        $badNumber = ($xml->chyba['spatny_dotaz'] ?? 'ne') == 'ano';
        $valid = !$badNumber && ($xml->odpoved['evidovano'] ?? 'ne') == 'ne';
        if (!$valid) {
            $this->lastMessages[] = self::IDENTITY_CARD_INVALID;
        }
        return $valid;
    }

    public function getMessages()
    {
        $messages = $this->lastMessages;
        $result = [];
        foreach ($messages as $message) {
            $result[] = $this->getTranslator()->translate($message);
        }
        return $result;
    }

}