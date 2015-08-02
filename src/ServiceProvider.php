<?php
namespace Tonis\DoctrineORM;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\Setup;
use League\Container\ServiceProvider;

class Doctrine extends ServiceProvider
{
    const DRIVER_ANNOTATION = 'annotation';
    const DRIVER_XML        = 'xml';
    const DRIVER_YAML       = 'yaml';

    /** @var array */
    protected $config;
    /** @var array */
    protected $provides = [
        EntityManager::class
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $defaults = [
            'debug'     => true,
            'proxy_dir' => null,
            'paths'     => [],
            'driver'    => self::DRIVER_ANNOTATION,
            'params'   => [],
        ];
        $this->config = array_merge($defaults, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $container = $this->getContainer();

        $container->singleton(EntityManager::class, function () {
            $config = $this->createConfiguration();
            $driver = $this->createDriverChain($config);

            $config->setMetadataDriverImpl($driver);

            return EntityManager::create($this->config['params'], $config);
        });
    }

    private function createConfiguration()
    {
        $cache     = isset($this->config['cache']) ? $this->config['cache'] : null;
        $container = $this->getContainer();

        if (is_string($cache) && isset($container[$cache])) {
            $cache = $container->get($cache);
        }

        return Setup::createConfiguration($this->config['debug'], $this->config['proxy_dir'], $cache);
    }

    private function createDriverChain(Configuration $config)
    {
        switch ($this->config['driver']) {
            case self::DRIVER_ANNOTATION:
                $driver = $config->newDefaultAnnotationDriver($this->config['paths'], true);
                break;
            case self::DRIVER_XML:
                $driver = new XmlDriver($this->config['paths']);
                break;
            case self::DRIVER_YAML:
                $driver = new YamlDriver($this->config['paths']);
                break;
            default:
                throw new Exception\InvalidDriverException('Valid driver types are: annotation, yaml, xml');
        }

        $chain = new MappingDriverChain();
        $chain->setDefaultDriver($driver);

        return $chain;
    }
}
