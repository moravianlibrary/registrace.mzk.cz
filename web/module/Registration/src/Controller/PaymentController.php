<?php

namespace Registration\Controller;

use Laminas\View\Model\ViewModel;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\PaymentService;
use Registration\Service\RegistrationService;

class PaymentController extends AbstractController
{
    use LoggerAwareTrait;

    private $url;

    /** @var PaymentService */
    private $paymentService;

    /** @var RegistrationService */
    private $registrationService;

    public function __construct(array $config, PaymentService $paymentService,
        RegistrationService $registrationService)
    {
        parent::__construct();
        $this->url = $config['payment']['url'];
        $this->paymentService = $paymentService;
        $this->registrationService = $registrationService;
    }

    public function initAction()
    {
        $registration = &$this->session->registration;
        if ($registration == null) {
            // error handling
        }
        if ($registration['finished']) {
            return $this->redirect()->toRoute('payment-finished');
        }
        $paymentUrl = $this->paymentService->prepareAndReturnPaymentUrl($registration);
        $registration['payment'] = true;
        return $this->redirect()->toUrl($paymentUrl);
    }

    public function finishedAction()
    {
        $registration = &$this->session->registration;
        $this->getLogger()->info($registration);
        $login = $registration['id'];
        $expiry = $registration['expiry'];
        $finished = $registration['finished'];
        if (!$finished && !$this->paymentService->hasRegistrationPayment($login)) {
            $this->registrationService->updateExpiration($login, $expiry);
        }
        $registration['finished'] = true;
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

}
