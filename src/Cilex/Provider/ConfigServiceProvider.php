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

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Symfony\Component\Yaml;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['config'] = $app->share(function () use ($app) {
            switch (strtolower(end(explode('.', $app['config.path'])))) {
                case 'yml':
                    $parser = new Yaml\Parser();
                    $result = new \ArrayObject(
                        $parser->parse($app['config.path'])
                    );
                    break;
                case 'xml':
                    $result = simplexml_load_file($app['config.path']);
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Unable to load configuration; the provided file '
                        .'extension was not recognized. Only yml or xml allowed'
                    );
            }
            return $result;
        });
    }
}