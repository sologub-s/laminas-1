<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Laminas\Navigation',
    'Laminas\Session',
    'Laminas\Log',
    'Laminas\Db',
    'Laminas\Form',
    'Laminas\Hydrator',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\I18n',
    'Laminas\Router',
    'Laminas\Validator',

    'App', // put $serviceContainer into \App, so has to be the first in the list
    'Session',
    //'Application',
    //'Album',
    //'Blog',
    'Backend',
    'Frontend',
];
