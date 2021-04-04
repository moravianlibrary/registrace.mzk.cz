<?php

namespace RegistrationApi\User;

class User implements InputFilterAwareInterface {

    protected $firstName;

    protected $lastName;

    protected $degree;

    protected $email;

    protected $phone;

    protected $identificationType;

    protected $identification;

    protected $birth;

    protected $studyType;

    protected $school;

    protected $discount;

    protected $password;

    protected $contactAddress;

    protected $permanentAddress;

}