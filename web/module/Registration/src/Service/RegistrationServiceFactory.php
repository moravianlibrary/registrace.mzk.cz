<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\I18n\Translator;

class RegistrationServiceFactory
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $config = $configReader->getConfig('config/config.ini');
        $demo = $config['demo']['enabled'] ?? false;
        if ($demo) {
            return new RegistrationServiceDemo();
        }
        $codeBook = $container->get(\Registration\Form\CodeBook::class);
        $translator = $container->get(\Laminas\Mvc\I18n\Translator::class);
        return new RegistrationService($config, $codeBook, $translator);
    }

}