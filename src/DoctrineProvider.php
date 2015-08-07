<?php
namespace Tonis\DoctrineORM;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\Setup;
use League\Container\ServiceProvider;

class DoctrineProvider extends ServiceProvider
{
    const DRIVER_ANNOTATION = 'annotation';
    const DRIVER_XML        = 'xml';
    const DRIVER_YAML       = 'yaml';

    /** @var array */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $defaults = [
            'alias'      => EntityManager::class,
            'debug'      => true,
            'proxy_dir'  => null,
            'driver'     => [
                'type'   => self::DRIVER_ANNOTATION,
                'simple' => true,
                'paths'  => [],
            ],
            'connection' => [
                'driver' => 'pdo_mysql'
            ],
        ];

        $this->config     = array_replace_recursive($defaults, $config);
        $this->provides[] = $this->config['alias'];
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

            return EntityManager::create($this->config['connection'], $config);
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
        $paths = $this->config['driver']['paths'];

        switch ($this->config['driver']['type']) {
            case self::DRIVER_ANNOTATION:
                $driver = $config->newDefaultAnnotationDriver($paths, $this->config['driver']['simple']);
                break;
            case self::DRIVER_XML:
                $driver = new XmlDriver($paths);
                break;
            case self::DRIVER_YAML:
                $driver = new YamlDriver($paths);
                break;
            default:
                throw new Exception\InvalidDriver('Valid driver types are: annotation, yaml, xml');
        }

        $chain = new MappingDriverChain();
        $chain->setDefaultDriver($driver);

        return $chain;
    }
}
