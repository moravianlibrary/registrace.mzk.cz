<?php

namespace Registration\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class AbstractController extends AbstractActionController
{

    // @var Container
    protected $session;

    public function __construct()
    {
        $this->session = new Container('registration');
    }

}