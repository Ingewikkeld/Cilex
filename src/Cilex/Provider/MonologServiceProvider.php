<?php

/*
 * This file is part of the Cilex framework.
 *
 * (c) Mike van Riel <mike.vanriel@naenius.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cilex\Provider;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Cilex\Application;
use Cilex\ServiceProviderInterface;

/**
 * Monolog Provider.
 *
 * This class is an adaptation of the Silex MonologServiceProvider written by
 * Fabien Potencier.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Mike van Riel <mike.vanvriel@naenius.com>
 */
class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['monolog'] = $app->share(function () use ($app)
        {
            $log = new Logger(isset($app['monolog.name']) ? $app['monolog.name'] : 'myapp');

            $app['monolog.configure']($log);

            return $log;
        });

        $app['monolog.configure'] = $app->protect(function ($log) use ($app)
        {
            $log->pushHandler($app['monolog.handler']);
        });

        $app['monolog.handler'] = function () use ($app)
        {
            return new StreamHandler($app['monolog.logfile'], $app['monolog.level']);
        };

        if (!isset($app['monolog.level']))
        {
            $app['monolog.level'] = function ()
            {
                return Logger::DEBUG;
            };
        }

        if (isset($app['monolog.class_path']))
        {
            $app['autoloader']->registerNamespace('Monolog', $app['monolog.class_path']);
        }

        $app->error(function (\Exception $e) use ($app)
        {
            $app['monolog']->addError($e->getMessage());
        });
    }
}