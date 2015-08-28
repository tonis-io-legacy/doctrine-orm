<?php
namespace Tonis\DoctrineORM;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\Setup;
use Interop\Container\ContainerInterface;

final class EntityManagerFactory
{
    const DRIVER_ANNOTATION = 'annotation';
    const DRIVER_XML        = 'xml';
    const DRIVER_YAML       = 'yaml';

    /**
     * @param array              $config
     * @param ContainerInterface $container
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public static function create(array $config = [], ContainerInterface $container = null)
    {
        $defaults = [
            'debug'      => true,
            'cache'      => null,
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

        $config    = array_replace_recursive($defaults, $config);
        $ormConfig = self::createConfiguration($config, $container);
        $driver    = self::createDriverChain($ormConfig, $config);

        $ormConfig->setMetadataDriverImpl($driver);

        return EntityManager::create($config['connection'], $ormConfig);
    }

    /**
     * Creates the ORM configuration.
     *
     * @param array              $config
     * @param ContainerInterface $container
     * @return Configuration
     */
    private static function createConfiguration(array $config, ContainerInterface $container = null)
    {
        $cache = $config['cache'];

        if ($container && is_string($config['cache']) && $container->has($config['cache'])) {
            $cache = $container->get($config['cache']);
        }

        return Setup::createConfiguration($config['debug'], $config['proxy_dir'], $cache);
    }

    /**
     * Creates the driver chain based on the default driver type. The chain can be retrieved
     * later and added to (for example, in other Tonis packages).
     *
     * @param Configuration       $ormConfig
     * @param array|Configuration $config
     * @return MappingDriverChain
     */
    private static function createDriverChain(Configuration $ormConfig, array $config)
    {
        $chain = new MappingDriverChain();
        $paths = $config['driver']['paths'];

        if (empty($paths)) {
            return $chain;
        }

        switch ($config['driver']['type']) {
            case self::DRIVER_ANNOTATION:
                $driver = $ormConfig->newDefaultAnnotationDriver($paths, $config['driver']['simple']);
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

        $chain->setDefaultDriver($driver);
        return $chain;
    }

    /**
     * Disallow construction.
     */
    private function __construct()
    {
    }
}
