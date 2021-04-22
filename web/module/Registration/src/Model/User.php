<?php

namespace Registration\Model;

class User
{

    protected $firstName = null;

    protected $lastName = null;

    protected $title = null;

    protected $birth = null;

    protected $university = null;

    protected $email = null;

    protected $phone = null;

    protected $identificationType = null;

    protected $identification = null;

    protected $password = null;

    // var FullAddress
    protected $permanentAddress = null;

    // var Address
    protected $contactAddress = null;

    // @var bool
    protected $sendNewsLetter = false;

    public function __construct($data)
    {
        $user = $data['user'];
        $this->firstName = $user['firstName'];
        $this->lastName = $user['lastName'];
        $year = $user['birth']['year'];
        $month = $user['birth']['month'];
        $day = $user['birth']['day'];
        $this->birth = new \DateTime("$year-$month-$day");
        $this->university = $user['birth']['university'];
        $this->email = $user['mail'];
        $this->phone = $user['phone'];
        $this->identificationType = $user['identificationType'];
        $this->identification = $user['identification'];
        $this->password = $data['password']['password'];
        $this->permanentAddress = new FullAddress($data['permanentAddress']);
        if ($user['isContactAddress'] == 1) {
            $this->contactAddress = new Address($data['contactAddress']);
        }
        $this->sendNewsLetter = $data['isSendNews'] == 1;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return \DateTime
     */
    public function getBirth(): \DateTime
    {
        return $this->birth;
    }

    /**
     * @param \DateTime $birth
     */
    public function setBirth(\DateTime $birth): void
    {
        $this->birth = $birth;
    }

    /**
     * @return string
     */
    public function getUniversity(): ?string
    {
        return $this->university;
    }

    /**
     * @param mixed $university
     */
    public function setUniversity($university): void
    {
        $this->university = $university;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $mail
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getIdentificationType(): ?string
    {
        return $this->identificationType;
    }

    /**
     * @param string $identificationType
     */
    public function setIdentificationType($identificationType): void
    {
        $this->identificationType = $identificationType;
    }

    /**
     * @return string
     */
    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    /**
     * @param string $identification
     */
    public function setIdentification($identification): void
    {
        $this->identification = $identification;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return FullAddress
     */
    public function getPermanentAddress(): FullAddress
    {
        return $this->permanentAddress;
    }

    /**
     * @param FullAddress $permanentAddress
     */
    public function setPermanentAddress(FullAddress $permanentAddress): void
    {
        $this->permanentAddress = $permanentAddress;
    }

    /**
     * @return Address
     */
    public function getContactAddress(): ?Address
    {
        return $this->contactAddress;
    }

    /**
     * @param Address $contactAddress
     */
    public function setContactAddress(Address $contactAddress): void
    {
        $this->contactAddress = $contactAddress;
    }

    /**
     * @return bool
     */
    public function isSendNewsLetter(): bool
    {
        return $this->sendNewsLetter;
    }

    /**
     * @param bool $sendNewsLetter
     */
    public function setSendNewsLetter(bool $sendNewsLetter): void
    {
        $this->sendNewsLetter = $sendNewsLetter;
    }

}