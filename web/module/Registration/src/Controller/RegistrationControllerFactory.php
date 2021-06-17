<?php
namespace Registration\Controller;

use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\PluginManagerInterface;
use Psr\Container\ContainerInterface;
use Registration\Form\UserForm;
use Registration\IdentityProvider\IdentityProviderFactory;

class RegistrationControllerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationController
    {
        /** @var PluginManagerInterface $formElementManager */
        $formElementManager = $container->get('FormElementManager');
        /** @var UserForm */
        $form = $formElementManager->get(UserForm::class);
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $config = $configReader->getConfig('config/config.ini');
        $identityProvider = new IdentityProviderFactory($config);
        $registrationService = $container->get(\Registration\Service\RegistrationServiceInterface::class);
        $translator = $container->get(Translator::class);
        return new RegistrationController($form, $config, $identityProvider, $registrationService, $translator);
    }

}