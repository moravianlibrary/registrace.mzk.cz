<?php

namespace Registration\Service;

class PaymentServiceDemo implements PaymentServiceInterface
{

    public function prepareAndReturnPaymentUrl($registration)
    {
        return "/payment/demoGateway";
    }

    public function hasRegistrationPayment($login)
    {
        return true;
    }

}