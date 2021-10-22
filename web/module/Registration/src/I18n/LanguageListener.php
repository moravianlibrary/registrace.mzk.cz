<?php

namespace Registration\I18n;

use \Laminas\EventManager\EventManagerInterface;
use \Laminas\EventManager\ListenerAggregateInterface;
use \Laminas\Mvc\MvcEvent;
use \Laminas\Session\Container;

class LanguageListener implements ListenerAggregateInterface
{

    public const DEFAULT_LOCALE = 'cs_CZ';

    protected const LANGUAGES = [
        'cs' => [
            'locale' => 'cs_CZ',
            'aleph_code' => 'CZE',
        ],
        'en' => [
            'locale' => 'en_GB',
            'aleph_code' => 'ENG',
        ],
    ];

    protected const LANGUAGE_ALEPH_CODES = [
        'cs' => 'CZE',
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
            if (!isset(LanguageListener::LANGUAGES[$lang])) {
                $lang = self::DEFAULT_LANGUAGE;
            }
            $language = LanguageListener::LANGUAGES[$lang];
            $locale = $language['locale'];
            $this->translator->setLocale($locale);
            $this->translator->setFallbackLocale($locale . '.UTF-8');
            $this->session->locale = $locale;
            $this->session->language = $lang;
            $this->session->language_aleph_code = $language['aleph_code'];
        });
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener);
        }
    }

}