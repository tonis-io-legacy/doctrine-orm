<?php
namespace Tonis\DoctrineORM;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Tonis\Container;

/**
 * @covers \Tonis\DoctrineORM\DoctrineProvider
 */
class DoctrineProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container */
    private $container;
    /** @var DoctrineProvider */
    private $provider;

    protected function prepare(array $config = [])
    {
        $provider  = new DoctrineProvider($config);
        $container = new Container();

        $container->addServiceProvider($provider);

        $this->provider  = $provider;
        $this->container = $container;
    }

    /**
     * @return EntityManager
     */
    protected function em()
    {
        return $this->container->get(EntityManager::class);
    }

    public function testAlias()
    {
        $this->prepare(['alias' => 'foo']);
        $this->assertTrue($this->container->has('foo'));
    }

    public function testEntityManagerCreated()
    {
        $this->prepare();
        $this->assertInstanceOf(EntityManager::class, $this->em());
    }

    public function testDriverChainIsDefaultDriver()
    {
        $this->prepare();
        $this->assertInstanceOf(MappingDriverChain::class, $this->em()->getConfiguration()->getMetadataDriverImpl());
    }

    public function testDefaultIsAnnotation()
    {
        $this->prepare();

        $driver = $this->em()->getConfiguration()->getMetadataDriverImpl()->getDefaultDriver();

        $this->assertInstanceOf(AnnotationDriver::class, $driver);
    }

    public function testXmlDriver()
    {
        $this->prepare(['driver' => ['type' => 'xml']]);

        $driver = $this->em()->getConfiguration()->getMetadataDriverImpl()->getDefaultDriver();

        $this->assertInstanceOf(XmlDriver::class, $driver);
    }

    public function testYamlDriver()
    {
        $this->prepare(['driver' => ['type' => 'yaml']]);

        $driver = $this->em()->getConfiguration()->getMetadataDriverImpl()->getDefaultDriver();

        $this->assertInstanceOf(YamlDriver::class, $driver);
    }

    public function testCacheIsUsed()
    {
        $this->prepare(['cache' => 'cache']);

        $cache = new ArrayCache();

        $this->container->add('cache', function () use ($cache) {
            return $cache;
        });

        $this->assertSame($cache, $this->em()->getConfiguration()->getMetadataCacheImpl());
    }

    /**
     * @covers \Tonis\DoctrineORM\Exception\InvalidDriver
     * @expectedException \Tonis\DoctrineORM\Exception\InvalidDriver
     * @expectedExceptionMessage Valid driver types are: annotation, yaml, xml
     */
    public function testInvalidDriverThrowsException()
    {
        $this->prepare(['driver' => ['type' => 'asdf']]);
        $this->em();
    }
}
