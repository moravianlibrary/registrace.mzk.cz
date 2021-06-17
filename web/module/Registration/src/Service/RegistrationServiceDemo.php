<?php

namespace Registration\Service;

use Registration\Model\User;

class RegistrationServiceDemo implements RegistrationServiceInterface
{

    public function register(User $user)
    {
        return "DEMOUSER";
    }

    public function updateExpiration($patronId, $newExpiration)
    {
        return true;
    }

}