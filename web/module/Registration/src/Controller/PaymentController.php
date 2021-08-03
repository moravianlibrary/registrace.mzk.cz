<?php

namespace Registration\Controller;

use Laminas\View\Model\ViewModel;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\PaymentServiceInterface;
use Registration\Service\RegistrationServiceInterface;
use Registration\Service\MailServiceInterface;

class PaymentController extends AbstractController
{
    use LoggerAwareTrait;

    private $url;

    /** @var bool */
    private $demo = false;

    /** @var PaymentServiceInterface */
    private $paymentService;

    /** @var RegistrationServiceInterface */
    private $registrationService;

    /** @var @var MailServiceInterface */
    private $mailService;

    public function __construct(array $config, PaymentServiceInterface $paymentService,
        RegistrationServiceInterface $registrationService, MailServiceInterface $mailService)
    {
        parent::__construct();
        $this->url = $config['payment']['url'];
        $this->demo = $config['payment']['demo'] ?? false;
        $this->paymentService = $paymentService;
        $this->registrationService = $registrationService;
        $this->mailService = $mailService;
    }

    public function initAction()
    {
        $registration = &$this->session->registration;
        if ($registration == null) {
            $this->flashMessenger()->addMessage('You are not registered.');
            return $this->redirect()->toRoute('registration-index');
        }
        if ($registration['finished']) {
            return $this->redirect()->toRoute('payment-finished');
        }
        // create payment
        $paymentUrl = $this->paymentService
            ->prepareAndReturnPaymentUrl($registration);
        // and prolong registration
        $login = $registration['id'];
        $expiry = $registration['expiry'];
        $this->registrationService->updateExpiration($login, $expiry);
        return $this->redirect()->toUrl($paymentUrl);
    }

    public function finishedAction()
    {
        $registration = &$this->session->registration;
        if ($registration == null) {
            $this->flashMessenger()->addMessage('You are not registered.');
            return $this->redirect()->toRoute('registration-index');
        }
        $this->getLogger()->info($registration);
        $login = $registration['id'];
        $expiry = $registration['expiry'];
        $finished = $registration['finished'];
        if (!$finished) {
            if ($this->paymentService->hasRegistrationPayment($login)) {
                return $this->redirect()->toRoute('registration-finished');
            }
            $registration['finished'] = true;
            $user = $registration['user'];
            $discount = $registration['discount'];
            $this->mailService->sendPaymentInfo($user, $discount['price']);
            // TODO: update user's blocks or wait for background job in Aleph?
        }
        $view = new ViewModel();
        $view->setTemplate('payment/finished');
        return $view;
    }

    public function finishedCashAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/finishedCash');
        return $view;
    }

    public function finishedOnlineVerifiedAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/finishedOnlineVerified');
        return $view;
    }

    public function finishedOnlineVerifiedNotAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/finishedOnlineVerifiedNot');
        return $view;
    }

    public function errorAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/error');
        return $view;
    }

    public function refusedAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/refused');
        return $view;
    }

    public function demoGatewayAction()
    {
        if (!$this->demo) {
            $this->flashMessenger()->addMessage('Demo gateway is disabled.');
            return $this->redirect()->toRoute('registration-index');
        }
        $view = new ViewModel();
        $view->setTemplate('payment/gateway');
        return $view;
    }

    public function demoGatewayFinishAction()
    {
        if (!$this->demo) {
            $this->flashMessenger()->addMessage('Demo gateway is disabled.');
            return $this->redirect()->toRoute('registration-index');
        }
        $registration = &$this->session->registration;
        if ($registration == null) {
            $this->flashMessenger()->addMessage('You are not registered.');
            return $this->redirect()->toRoute('registration-index');
        }
        return $this->redirect()->toRoute('payment-finished');
    }

}
