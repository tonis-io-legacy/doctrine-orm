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
    public function __construct(array $config)
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

        if (!$container instanceof Container) {
            throw new Exception\InvalidContainer(
                'This package only works with the default Container'
            );
        }

        $container->addServiceProvider(new DoctrineProvider($this->config));
    }
}
