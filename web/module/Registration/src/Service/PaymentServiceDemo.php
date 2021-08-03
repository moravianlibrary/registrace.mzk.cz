<?php

namespace Registration\Service;

class PaymentServiceDemo implements PaymentServiceInterface
{

    public function prepareAndReturnPaymentUrl(&$registration)
    {
        $registration['payment'] = true;
        return "/payment/demoGateway";
    }

    public function hasRegistrationPayment($login)
    {
        return false;
    }

}