<?php

namespace Registration\Service;

use Laminas\Http\Client;
use Registration\Log\LoggerAwareTrait;

class PaymentService
{
    use LoggerAwareTrait;

    /** @var string */
    protected $restUrl;

    /** @var string */
    protected $createPaymentUrl;

    /** @var string */
    protected $redirectPaymentUrl;

    public function __construct($config)
    {
        $this->restUrl = $config['alephRestServer']['url'];
        $this->createPaymentUrl = $config['payment']['create_url'];
        $this->redirectPaymentUrl = $config['payment']['redirect_url'];
    }

    public function prepareAndReturnPaymentUrl($registration)
    {
        $login = $registration['id'];
        $discount = $registration['discount'];
        if (!$this->hasRegistrationPayment($login)) {
            $this->createPayment($login, $discount);
        }
        return $this->getPaymentUrl($login, $discount);
    }

    protected function hasRegistrationPayment($login)
    {
        $url = $this->restUrl . '/rest-dlf/patron/' . $login . '/circulationActions/cash?view=full';
        $this->getLogger()->info("URL: " . $url);
        $client = new Client();
        $client->setUri($url);
        $response = $client->send();
        if ($response->getStatusCode() != '200') {
            throw new \Exception("Operation get fines failed");
        }
        $this->getLogger()->info("Response body: " . $response);
        $xml = simplexml_load_string($response->getBody());
        $charges = $xml->charges->institution->cash->z31 ?? [];
        foreach ($charges as $z31) {
            $type = $z31->{'z31-type'};
            if (strpos($type, 'B Online registrace') === 0) {
                return true;
            }
        }
        return false;
    }

    protected function createPayment($login, $discount)
    {
        $client = new Client();
        $client->setUri($this->createPaymentUrl);
        $client->setMethod("GET");
        $client->setParameterGet([
            'id'           => $login,
            'cislo_platby' => $discount['payment_number'],
            'nazev_platby' => $discount['payment_name'],
            'castka'       => str_pad((string) $discount['price'] * 100, 14, '0', STR_PAD_LEFT),
        ]);
        $response = $client->send();
        $result = json_decode($response->getBody());
    }

    protected function getPaymentUrl($login, $discount)
    {
        $params = [
            'id' => $login,
            'adm' => 'MZK50',
            'amount' => $discount['price'] * 100,
            'time' => time(),
        ];
        return $this->redirectPaymentUrl . '?' . http_build_query($params);
    }

}