<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

class IdentityProviderFactory
{

    /** @var bool */
    protected $test = false;

    protected $eduid = false;

    public function __construct($config)
    {
        $this->test = (bool) $config['mojeid']['test'] ?? false;
        $this->eduid = (bool) $config['eduid']['enabled'] ?? false;
    }

    public function get(string $type)
    {
        if ($type == 'test' && $this->test) {
            return new Test();
        } else if ($type == 'mojeid') {
            return new MojeId($this->test);
        } else if ($type == 'eduid' && $this->eduid) {
            return new EduId();
        }
        return null;
    }

}
