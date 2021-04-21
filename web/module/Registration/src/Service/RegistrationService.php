<?php

namespace Registration\Service;

use Laminas\Http\Client;
use Registration\Model\User;

class RegistrationService
{

    const XML_TEMPLATE = __DIR__. '/../../resources/registration.xml';

    protected $url;

    protected $user;

    protected $password;

    public function __construct($config)
    {
        $this->url = $config['aleph']['url'];
        $this->user = $config['aleph']['user'] ?? null;
        $this->password = $config['aleph']['password'] ?? null;
    }

    public function register(User $user)
    {
        $id = $this->getId($user);
        $now = date('Ymd');
        $expiry = date('Ymd', strtotime('+1 year'));
        $xml = simplexml_load_file(self::XML_TEMPLATE);
        $patron = $xml->{'patron-record'}[0];
        // z303
        $z303 = $patron->{'z303'};
        $z303->{'match-id'} = $id;
        $z303->{'z303-id'} = $id;
        $cn = $user->getFirstName() . ' ' . $user->getLastName();
        $z303->{'z303-name'} = $cn;
        $z303->{'z303-open-date'} = $now;
        $z303->{'z303-title'} = $user->getTitle();
        $z303->{'z303-birth-date'} = $user->getBirth()->format("Ymd");
        // online registration
        $z303->{'z303-delinq-1'} = '50';
        $z303->{'z303-delinq-n-1'} = 'Online předregistrace';
        $z303->{'z303-delinq-1-update-date'} = $now;
        // test
        $z303->{'z303-delinq-2'} = '88';
        $z303->{'z303-delinq-n-2'} = 'Testovací registrace';
        $z303->{'z303-delinq-2-update-date'} = $now;
        // z304
        $z304 = $patron->{'z304'}[0];
        $z304->{'z304-id'} = $id;
        $z304->{'z304-date-from'} = $now;
        $z304->{'email-address'} = $user->getEmail();
        $z304->{'z304-email-address'} = $user->getEmail();
        $z304->{'z304-telephone'} = $user->getPhone();
        $z304->{'z304-address-1'} = $cn;
        $address = $user->getPermanentAddress();
        $z304->{'z304-address-2'} = $address->getStreet();
        $z304->{'z304-address-3'} = $address->getPostcode()
            . ' ' . $address->getCity();
        $z304->{'z304-telephone-2'}[1] = $user->getIdentificationType()
            . ' ' . $user->getIdentification();
        // z305
        $z305 = $patron->{'z305'};
        $z305->{'z305-id'} = $id;
        $z305->{'z305-open-date'} = $now;
        $z305->{'z305-registration-date'} = $now;
        $z305->{'z305-expiry-date'} = $expiry;
        $z305->{'z305-last-activity-date'} = $now;
        $university = $user->getUniversity();
        if (!empty($university)) {
            $z305->{'z305-bor-status'} = '04';
            $z305->{'z305-bor-type'} = $university;
        } else {
            $z305->{'z305-bor-status'} = '03';
            $z305->{'z305-bor-type'} = '';
        }
        // z308
        $z308 = $patron->{'z308'};
        foreach ($z308 as $entry) {
            $entry->{'z308-id'} = $id;
            $entry->{'z308-key-data'} = $id;
            $entry->{'z308-verification'} = $user->getPassword();
        }
        // for testing
        $z303->{'record-action'} = 'U';
        $z304->{'record-action'} = 'U';
        $z305->{'record-action'} = 'U';
        $result = $xml->asXML();
        $client = new Client();
        $client->setUri($this->url);
        $parameters = [
            'op' => 'update_bor',
            'library' => 'MZK50',
            'update-flag' => 'Y',
            'xml_full_req' => $result,
        ];
        if ($this->user && $this->password) {
            $parameters['user_name'] = $this->user;
            $parameters['user_password'] = $this->password;
        }
        $client->setParameterPost($parameters);
        $client->setMethod('POST');
        $response = $client->send();
        if ($response->getStatusCode() != '200') {
            throw new \Exception("Operation update_bor failed");
        }
        $xml = simplexml_load_string($response->getBody());
        if (!$xml) {
            throw new \Exception("Operation update_bor returned invalid XML: "
                . $response->getBody());
        }
        $patronId = $xml->{'patron-id'} ?? null;
        if ($patronId == null) {
            throw new \Exception("Operation update_bor returned invalid XML: "
                . $response->getBody());
        }
        return $id;
    }

    protected function getId(User $user)
    {
        return "MZKTEST";
    }

}