<?php

namespace Registration\Model;

class Address
{

    protected $street;

    protected $city;

    protected $postcode;

    public function __construct($data)
    {
        $this->street = $data['street'];
        $this->city = $data['city'];
        $this->postcode = $data['postcode'];
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode): void
    {
        $this->postcode = $postcode;
    }

}