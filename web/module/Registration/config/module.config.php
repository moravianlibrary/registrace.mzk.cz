<?php

declare(strict_types=1);

namespace Registration;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Session;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Registration\Controller\PaymentController;

$config = [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\RegistrationController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'registration' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/registration[/:action]',
                    'defaults' => [
                        'controller' => Controller\RegistrationController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'payment' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/payment[/:action]',
                    'defaults' => [
                        'controller' => Controller\PaymentController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'ajax' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/ajax[/:action]',
                    'defaults' => [
                        'controller' => Controller\AjaxController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\RegistrationController::class => Controller\RegistrationControllerFactory::class,
            Controller\PaymentController::class => Controller\PaymentControllerFactory::class,
            Controller\AjaxController::class => Controller\AjaxControllerFactory::class,
        ],
        'aliases' => [
            'Registration' => 'Registration\Controller\RegistrationController',
            'Ajax' => 'Registration\Controller\AjaxController',
            'Payment' => 'Registration\Controller\PaymentController'
        ],
    ],
    'service_manager' => [
        'allow_override' => true,
        'factories' => [
            \Registration\Config\ConfigReader::class => InvokableFactory::class,
            \Registration\Form\CodeBook::class => \Registration\Form\CodeBookFactory::class,
            \Laminas\Mvc\I18n\Translator::class => \Laminas\Mvc\I18n\TranslatorFactory::class,
            \Registration\Service\DiscountService::class => \Registration\Service\DiscountServiceFactory::class,
            \Registration\Service\RegistrationServiceInterface::class => \Registration\Service\RegistrationServiceFactory::class,
            \Registration\Service\PaymentServiceInterface::class => \Registration\Service\PaymentServiceFactory::class,
            \Registration\Form\Validator\PasswordValidator::class => \Registration\Form\Validator\PasswordValidatorFactory::class,
            \Registration\Service\MailServiceInterface::class => \Registration\Service\MailServiceFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            \Registration\Form\UserFieldset::class => \Registration\Form\UserFieldsetFactory::class,
            \Registration\Form\FullAddressFieldset::class => \Registration\Form\FullAddressFieldsetFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'                   => __DIR__ . '/../view/layout/layout.phtml',
            'registration/registration/index' => __DIR__ . '/../view/registration/index/userForm.phtml',
            'error/404'                       => __DIR__ . '/../view/error/404.phtml',
            'error/index'                     => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'translator' => [
        'locale' => 'cs_CZ',
        'translation_file_patterns' => [
            [
                'type'     => \Registration\I18n\Translator\Loader\Ini::class,
                'base_dir' => __DIR__ . '/../../../data/language/',
                'pattern'  => '%s.ini',
            ],
            [
                'type'        => \Registration\I18n\Translator\Loader\Ini::class,
                'base_dir'    => __DIR__ . '/../../../data/language/',
                'pattern'     => 'base.ini',
            ],
            [
                'type'        => \Registration\I18n\Translator\Loader\Ini::class,
                'base_dir'    => __DIR__ . '/../../../data/language/',
                'pattern'     => 'countries.ini',
                'text_domain' => 'country',
            ],
        ],
    ],
    'session_manager' => [
        'config' => [
            'class' => Session\Config\SessionConfig::class,
            'options' => [
                'name' => 'registration',
            ],
        ],
        'storage' => Session\Storage\SessionArrayStorage::class,
        'validators' => [
            Session\Validator\RemoteAddr::class,
            Session\Validator\HttpUserAgent::class,
        ],
    ],
];

$routes = [
    'Registration/index',
    'Registration/userForm',
    'Registration/finished',
    'Registration/logout',
    'Payment/init',
    'Payment/finished',
    'Payment/finishedCash',
    'Payment/finishedOnlineVerified',
    'Payment/finishedOnlineVerifiedNot',
    'Payment/error'
];

foreach ($routes as $route) {
    list($controller, $action) = explode('/', $route);
    $routeName = str_replace('/', '-', strtolower($route));
    $config['router']['routes'][$routeName] = [
        'type' => 'Laminas\Router\Http\Literal',
        'options' => [
            'route' => '/' . $route,
            'defaults' => [
                'controller' => $controller,
                'action' => $action,
            ]
        ]
    ];
}

return $config;
