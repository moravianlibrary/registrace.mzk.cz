<?php

namespace Registration\Model;

class FullAddress extends Address
{

    protected $state;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->street = $data['state'];
    }

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

}