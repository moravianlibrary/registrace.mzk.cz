<?php

namespace Registration\Service;

use Registration\Model\User;

interface RegistrationServiceInterface
{

    public function register(User $user);

    public function updateExpiration($patronId, $newExpiration);

}