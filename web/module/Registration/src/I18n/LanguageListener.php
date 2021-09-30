<?php

namespace Registration\I18n;

use \Laminas\EventManager\EventManagerInterface;
use \Laminas\EventManager\ListenerAggregateInterface;
use \Laminas\Mvc\MvcEvent;
use \Laminas\Session\Container;

class LanguageListener implements ListenerAggregateInterface
{

    protected const LANGUAGES = [
        'cs' => 'cs_CZ',
        'en' => 'en_GB',
    ];

    protected const DEFAULT_LANGUAGE = 'cs';

    /**
     * @var \Laminas\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @var Container
     */
    protected $session;

    protected $listeners = [];

    public function __construct(\Laminas\I18n\Translator\Translator $translator)
    {
        $this->translator = $translator;
        $this->session = new Container('registration');
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, function($event) {
            /**
            * @var \Laminas\Http\PhpEnvironment\Request $request
            */
            $request = $event->getRequest();
            $lang = $request->getQuery('lang')
                ?? $this->session->language
                ?? self::DEFAULT_LANGUAGE;
            if (isset(LanguageListener::LANGUAGES[$lang])) {
                $locale = LanguageListener::LANGUAGES[$lang];
                $this->translator->setLocale($locale);
            } else {
                $lang = self::DEFAULT_LANGUAGE;
            }
            $this->session->language = $lang;
        });
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener);
        }
    }

}