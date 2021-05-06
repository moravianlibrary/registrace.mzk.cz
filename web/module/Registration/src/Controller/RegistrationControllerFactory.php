<?php
namespace Registration\Controller;

use Laminas\ServiceManager\PluginManagerInterface;
use Psr\Container\ContainerInterface;
use Registration\Form\UserForm;

class RegistrationControllerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationController
    {
        /** @var PluginManagerInterface $formElementManager */
        $formElementManager = $container->get('FormElementManager');
        /** @var UserForm */
        $form = $formElementManager->get(UserForm::class);
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $identityProvider = $container->get(\Registration\IdentityProvider\IdentityProviderFactory::class);
        $registrationService = $container->get(\Registration\Service\RegistrationService::class);
        return new RegistrationController($form, $configReader->getConfig('config/config.ini'),
            $identityProvider, $registrationService);
    }

}