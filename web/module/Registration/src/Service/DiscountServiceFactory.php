<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Mvc\I18n\Translator;

class DiscountServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new DiscountService(
            $container->get(Translator::class)
        );
    }

}