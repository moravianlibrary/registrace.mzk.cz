<?php

namespace Registration\Controller;

use Laminas\View\Model\ViewModel;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\PaymentService;

class PaymentController extends AbstractController
{
    use LoggerAwareTrait;

    private $url;

    /** @var PaymentService */
    private $paymentService;

    public function __construct(array $config, PaymentService $paymentService)
    {
        parent::__construct();
        $this->url = $config['payment']['url'];
        $this->paymentService = $paymentService;
    }

    public function initAction()
    {
        $registration = $this->session->registration ?? null;
        if ($registration == null) {
            //
        }
        $paymentUrl = $this->paymentService->prepareAndReturnPaymentUrl($registration);
        return $this->redirect()->toUrl($paymentUrl);
    }

    public function finishedAction()
    {
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
