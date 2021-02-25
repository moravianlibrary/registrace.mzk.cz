<?php

namespace Registration\Form;

use \Interop\Container\ContainerInterface;
use \Laminas\ServiceManager\Factory\FactoryInterface;
use \Laminas\ServiceManager\ServiceLocatorInterface;
use \Registration\Form\CodeBook;

class UserFieldsetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new UserFieldset(
            $container->get(CodeBook::class),
        );
    }

}