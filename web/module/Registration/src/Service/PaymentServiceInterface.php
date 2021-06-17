<?php

namespace Registration\Service;

interface PaymentServiceInterface
{

    public function prepareAndReturnPaymentUrl($registration);

    public function hasRegistrationPayment($login);

}