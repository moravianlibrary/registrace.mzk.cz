<?php
namespace Registration\Utils;

class HmacCalculator
{

    private $hash;

    // TODO: read from configuration
    private $key = 'topsecret';

    public function add($field, $value)
    {
        $this->hash .= $field . '=' . $value . ';';
    }

    public function toHash()
    {
        return hash_hmac('ripemd160', $this->hash, $this->key);
    }

}