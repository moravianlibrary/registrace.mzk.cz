<?php

namespace Registration\Form;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Registration\Form\CodeBook;
use Registration\Service\DiscountService;

class UserFieldsetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new UserFieldset(
            $container->get(CodeBook::class),
            $container->get(Translator::class),
            $container->get(DiscountService::class)
        );
    }

}