<?php


namespace Registration\Form\Validator;

use Laminas\Mvc\I18n\Translator;
use Psr\Container\ContainerInterface;

class PasswordValidatorFactory
{

    public function __invoke(ContainerInterface $container) : PasswordValidator
    {
        $translator = $container->get(Translator::class);
        $passwordValidator = new PasswordValidator();
        $passwordValidator->setTranslator($translator);
        return $passwordValidator;
    }

}