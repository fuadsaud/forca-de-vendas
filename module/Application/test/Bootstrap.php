<?php
namespace ApplicationTest;//Change this namespace for your test

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{
    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;

    public static function init()
    {
        system('../../../bin/phinx rollback -t0 -q');
        system('../../../bin/phinx migrate -q');
        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/TestConfig.php')) {
            $testConfig = include __DIR__ . '/TestConfig.php';
        } else {
            $testConfig = include __DIR__ . '/TestConfig.php.dist';
        }

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath)) ) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths  = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?: (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig(self::getServiceConfig()));
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $adapter->setProfiler($serviceManager->get('Application\Db\Adapter\Profiler\Profiler'));

        static::$serviceManager = $serviceManager;
        static::$config = $config;

    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';

        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }


    public static function getServiceConfig()
    {
        return array(
            'factories' => array(
                'FixturesRunner' => function($sm) {
                    $runner = new Fixture\Runner();
                    $runner->setServiceLocator($sm);
                    return $runner;
                },
                'ApplicationTest\Fixture\Users' => function($sm) {
                    $users = new Fixture\Users();
                    $users->setTable($sm->get('Application\Model\UsersTable'));
                    return $users;
                },
                'ApplicationTest\Fixture\Groups' => function($sm) {
                    $groups = new Fixture\Groups();
                    $groups->setTable($sm->get('Application\Model\GroupsTable'));
                    return $groups;
                },
                'ApplicationTest\Fixture\Categories' => function($sm) {
                    $categories = new Fixture\Categories();
                    $categories->setTable($sm->get('Application\Model\CategoriesTable'));
                    return $categories;
                },
                'ApplicationTest\Fixture\Products' => function($sm) {
                    $products = new Fixture\Products();
                    $products->setTable($sm->get('Application\Model\ProductsTable'));
                    return $products;
                },
            ),
            'allow_override' => true,
        );
    }
}

Bootstrap::init();
