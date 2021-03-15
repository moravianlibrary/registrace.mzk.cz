<?php

declare(strict_types=1);

namespace Registration;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'registration' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/registration[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
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
            'password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/password',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'password',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\IndexControllerFactory::class,
            Controller\AjaxController::class => Controller\AjaxControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'allow_override' => true,
        'factories' => [
            \Registration\Config\ConfigReader::class => InvokableFactory::class,
            \Registration\Form\CodeBook::class => \Registration\Form\CodeBookFactory::class,
            \Laminas\Mvc\I18n\Translator::class => \Laminas\Mvc\I18n\TranslatorFactory::class,
        ]
    ],
    'form_elements' => [
        'factories' => [
            \Registration\Form\UserFieldset::class => \Registration\Form\UserFieldsetFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'registration/index/index' => __DIR__ . '/../view/registration/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
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
        ],
    ],
];
