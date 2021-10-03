<?php
namespace Registration\Service;

use Laminas\Http\Client;
use \Laminas\Session\Container;
use Registration\Log\LoggerAwareTrait;
use Registration\Model\User;


class MailServiceAleph implements MailServiceInterface
{
    use LoggerAwareTrait;

    const REGISTRATION_URL = "https://aleph.mzk.cz/cgi-bin/mail-vitejte.pl";

    const PAYMENT_URL = "https://aleph.mzk.cz/cgi-bin/mail-platba.pl";

    /**
     * @var Container
     */
    protected $session;

    public function __construct()
    {
        $this->session = new Container('registration');
    }

    public function sendRegistrationInfo(User $user)
    {
        if ($user->getEmail() == null || empty($user->getEmail())) {
            return;
        }
        $client = new Client(self::REGISTRATION_URL);
        $client->setMethod('GET');
        $client->setParameterGet([
            'id'   => $user->getLogin(),
            'mail' => $user->getEmail(),
            'lang' => $this->session->language ?? 'cs',
        ]);
        try {
            $response = $client->send();
        } catch (Exception $ex) {
            $this->getLogger()->err($ex->getMessage());
        }
    }

    public function sendPaymentInfo(User $user, $amount)
    {
        if ($user->getEmail() == null || empty($user->getEmail())) {
            return;
        }
        $client = new Client(self::PAYMENT_URL);
        $client->setMethod('GET');
        $client->setParameterGet([
            'id'     => $user->getLogin(),
            'mail'   => $user->getEmail(),
            'jmeno'  => $user->getFirstName() . ' ' . $user->getLastName(),
            'castka' => $amount,
            'lang' => $this->session->language ?? 'cs',
        ]);
        try {
            $response = $client->send();
        } catch (Exception $ex) {
            $this->getLogger()->err($ex->getMessage());
        }
    }

}
