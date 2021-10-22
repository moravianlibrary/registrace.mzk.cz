<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Registration;

class Module
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param  \Laminas\Mvc\MvcEvent $event event
     * @return void
     */
    public function onBootstrap($event)
    {
        $application = $event->getApplication();
        $translator = $application->getServiceManager()->get('Laminas\I18n\Translator\TranslatorInterface');
        $listener = new \Registration\I18n\LanguageListener($translator);
        $listener->attach($application->getEventManager());
    }

}
