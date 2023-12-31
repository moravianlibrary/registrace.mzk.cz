<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

/**
 * List of enabled modules for this registration.
 *
 * This should be an array of module namespaces used in the registration.
 */
return [
    'Laminas\Log',
    'Laminas\Session',
    'Laminas\Mvc\I18n',
    'Laminas\I18n',
    'Laminas\Form',
    'Laminas\Hydrator',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\Router',
    'Laminas\Validator',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Registration',
];
