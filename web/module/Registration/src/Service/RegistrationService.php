<?php

namespace Registration\Service;

use Laminas\Http\Client;
use Registration\Log\LoggerAwareTrait;
use Registration\Model\User;

class RegistrationService
{
    use LoggerAwareTrait;

    const XML_TEMPLATE = __DIR__. '/../../resources/registration.xml';

    const CHARACTERS = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    const PROTECTED_PARAMETERS = [
        'xml_full_req',
        'user_name',
        'user_password',
    ];

    /** @var string */
    protected $xServerUrl;

    /** @var string */
    protected $xServerUser;

    /** @var string */
    protected $xServerPassword;

    /** @var string */
    protected $library;

    /** @var boolean */
    protected $demo;

    /** @var boolean */
    protected $test;

    public function __construct($config)
    {
        $this->xServerUrl = $config['alephXServer']['url'];
        $this->xServerUser = $config['alephXServer']['user'] ?? null;
        $this->xServerPassword = $config['alephXServer']['password'] ?? null;
        $this->library = $config['aleph']['library'] ?? 'MZK50';
        $this->demo = $config['aleph']['demo'] ?? false;
        $this->test = $config['aleph']['test'] ?? false;
    }

    public function register(User $user)
    {
        $id = $this->getId($user);
        $now = date('Ymd');
        $expiry = date('Ymd', strtotime('+1 year'));
        $xml = simplexml_load_file(self::XML_TEMPLATE);
        $patron = $xml->{'patron-record'}[0];
        $recordAction = ($this->demo) ? 'U' : 'I';
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
        if ($this->demo || $this->test) {
            $z303->{'z303-delinq-2'} = '88';
            $z303->{'z303-delinq-n-2'} = 'Testovací registrace';
            $z303->{'z303-delinq-2-update-date'} = $now;
        }
        // z304
        $index = 0;
        foreach ($patron->{'z304'} as $z304) {
            $address = ($index == 0) ? $user->getPermanentAddress() :
                $user->getContactAddress();
            if ($index == 1 && $user->getContactAddress() == null) {
                unset($patron->{'z304'}[1]);
                break;
            }
            $z304->{'record-action'} = $recordAction;
            $z304->{'z304-id'} = $id;
            $z304->{'z304-date-from'} = $now;
            $z304->{'email-address'} = $user->getEmail();
            $z304->{'z304-email-address'} = $user->getEmail();
            $z304->{'z304-telephone'} = $user->getPhone();
            $z304->{'z304-address-1'} = $cn;
            $z304->{'z304-address-2'} = $address->getStreet();
            $z304->{'z304-address-3'} = $address->getPostcode()
                . ' ' . $address->getCity();
            $z304->{'z304-telephone-2'} = $user->getIdentificationType()
                . ' ' . $user->getIdentification();
            $z304->{'z304-telephone-3'} = $user->isSendNewsLetter() ? '' : 'NE';
            $z304->{'z304-telephone-4'} = $user->isSendNewsLetter() ? '' : 'NE';
            $index++;
        }
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
        $z303->{'record-action'} = $recordAction;
        $z305->{'record-action'} = $recordAction;
        $request = $xml->asXML();
        $this->getLogger()->info("XML for registration:\n" . $request);
        $response = $this->updateUser($request);
        $patronId = (string) $response->{'patron-id'} ?? null;
        if ($patronId == null) {
            throw new \Exception("Operation update_bor returned invalid XML");
        }
        return $id;
    }

    public function updateUserAfterRegistration($patronId)
    {
        $patron = $this->findUser($patronId);
        $z303 = $patron->{'z303'};
        $update = new \SimpleXMLElement("<p-file-20><patron-record></patron-record></p-file-20>");
        $z303Update = $update->{'patron-record'}->addChild('z303');
        $z303Update->addChild('match-id-type', '00');
        $z303Update->addChild('match-id', $patronId);
        $z303Update->addChild('record-action', 'U');
        foreach ($z303->children() as $child) {
            $z303Update->addChild($child->getName(), (string) $child);
        }
        $xml = $update->asXml();
        $this->getLogger()->info("XML after update:\n" . $xml);
        return $this->updateUser($xml);
    }

    protected function findUser($patronId)
    {
        return $this->callXServer([
            'op' => 'bor_info',
            'bor_id' => $patronId,
            'library' => 'MZK50',
        ]);
    }

    protected function updateUser($xml)
    {
        $parameters = [
            'op' => 'update_bor',
            'library' => 'MZK50',
            'update-flag' => 'Y',
            'xml_full_req' => $xml,
        ];
        if ($this->user && $this->password) {
            $parameters['user_name'] = $this->xServerUser;
            $parameters['user_password'] = $this->xServerPassword;
        }
        $xml = $this->callXServer($parameters, 'POST');
        return $xml;
    }

    protected function callXServer($parameters, $method='GET') {
        $operation = $parameters['op'];
        $this->logUrl($parameters);
        $client = new Client();
        $client->setUri($this->xServerUrl);
        if ($method == 'POST') {
            $client->setParameterPost($parameters);
        } else {
            $client->setParameterGet($parameters);
        }
        $client->setMethod($method);
        $response = $client->send();
        if ($response->getStatusCode() != '200') {
            throw new \Exception("Operation $operation failed");
        }
        $this->getLogger()->info("Response body: " . $response);
        $xml = simplexml_load_string($response->getBody());
        if (!$xml) {
            throw new \Exception("Operation $operation returned invalid XML");
        }
        // special case: update_bor
        // <error>Succeeded to WRITE table z303. cur-id XXXXXXXXXXXX.</error>
        // <error>Succeeded to REWRITE table z303. cur-id XXXXXXXXXXXX.</error>
        if ($operation == 'update_bor') {
            foreach ($xml->error as $error) {
                if (!(strpos($error, 'Succeeded to') === 0)) {
                    throw new \Exception($error);
                }
            }
            return $xml;
        }
        if (isset($xml->error)) {
            throw new \Exception($xml->error);
        }
        return $xml;
    }

    protected function logUrl($parameters)
    {
        foreach (self::PROTECTED_PARAMETERS as $protected) {
            if (isset($parameters[$protected])) {
                $parameters[$protected] = "***";
            }
        }
        $params = http_build_query($parameters);
        $this->getLogger()->info("About to call URL: " .
            $this->url . '?' . $params);
    }

    protected function getId(User $user)
    {
        if ($this->demo) {
            return 'MZKTEST';
        }
        $count = strlen(self::CHARACTERS);
        while (true) {
            $suffix = '';
            for ($i = 1; $i <= 4; $i++) {
                $suffix .= self::CHARACTERS[mt_rand(0, $count - 1)];
            }
            $userId = $user->getBirth()->format("Ymd") . $suffix;
            try {
                $this->findUser($userId);
            } catch (\Exception $ex) {
                if ($ex->getMessage() == 'Error retrieving Patron System Key') {
                    return $userId;
                }
            }
        }
    }

}