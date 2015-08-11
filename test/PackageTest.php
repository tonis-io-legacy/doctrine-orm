<?php
namespace Tonis\DoctrineORM;

use Tonis\App;

/**
 * @covers \Tonis\DoctrineORM\Package
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app       = new App();
        $container = $app->getContainer();
        $package   = new Package(['alias' => 'foo']);

        $package->register($app);

        $this->assertTrue($container->has('foo'));
    }
}
