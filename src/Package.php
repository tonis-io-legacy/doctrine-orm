<?php
namespace Tonis\DoctrineORM;

use Tonis\App;
use Tonis\Container;
use Tonis\PackageInterface;

class Package implements PackageInterface
{
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param App $app
     * @return void
     */
    public function register(App $app)
    {
        $container = $app->getContainer();

        if (method_exists($container, 'addServiceProvider')) {
            $container->addServiceProvider(new DoctrineProvider($this->config));
        }
    }
}
