<?php

namespace Registration\Model;

class FullAddress extends Address
{

    protected $country;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->country = $data['country'];
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $state
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

}