<?php

namespace Registration\Service;

use Registration\Model\User;

class MailServiceDemo implements MailServiceInterface
{

    public function sendRegistrationInfo(User $user)
    {
        return;
    }

    public function sendPaymentInfo(User $user, $amount)
    {
        return;
    }

}