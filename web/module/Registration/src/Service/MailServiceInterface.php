<?php

namespace Registration\Service;

use Registration\Model\User;

interface MailServiceInterface
{

    public function sendRegistrationInfo(User $user);

    public function sendPaymentInfo(User $user, $amount);

}