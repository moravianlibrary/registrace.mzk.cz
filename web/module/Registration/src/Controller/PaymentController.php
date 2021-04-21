<?php

namespace Registration\Controller;

use Laminas\View\Model\ViewModel;

class PaymentController extends AbstractController
{

    private $url;

    public function __construct(array $config)
    {
        parent::__construct();
        $this->url = $config['payment']['url'];
    }

    public function initAction()
    {
        $params = [
            'id' => $this->session->id,
            'adm' => 'MZK50',
            'amount' => 200,
            'time' => time(),
        ];
        $paymentUrl = $this->url . '?' . http_build_query($params);
        return $this->redirect()->toUrl($paymentUrl);
    }

    public function finishedAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/finished');
        return $view;
    }

    public function errorAction()
    {
        $view = new ViewModel();
        $view->setTemplate('payment/error');
        return $view;
    }

}