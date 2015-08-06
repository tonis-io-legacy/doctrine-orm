<?php
namespace Tonis\DoctrineORM;

use Psr\Http\Message\ResponseInterface;
use Tonis\App;
use Tonis\Container;
use Tonis\Http\Request;
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
     * @param Request           $request
     * @param ResponseInterface $response
     * @param callable          $next
     * @return ResponseInterface
     */
    public function __invoke(Request $request, ResponseInterface $response, callable $next)
    {
        $container = $request->app()->getContainer();

        if (!$container instanceof Container) {
            throw new Exception\InvalidContainer(
                'This package only works with the default Container'
            );
        }

        $container->addServiceProvider(new DoctrineProvider($this->config));

        return $next($request, $response);
    }

    /**
     * @param App $app
     * @return void
     */
    public function register(App $app)
    {
        $app->add($this);
    }
}
