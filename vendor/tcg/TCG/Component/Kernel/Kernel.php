<?php

namespace TCG\Component\Kernel;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Yaml\Yaml;

abstract class Kernel
{
    /**
     * @var ClassLoader
     */
    protected $autoLoader;

    /**
     * @var string[]
     */
    protected $configFiles = [];

    /**
     * @var array|Config
     */
    protected $config;

    /**
     * @var Bundle[]
     */
    protected $bundles = [];

    /**
     * @var Module[]
     */
    protected $modules = [];

    /**
     * @var Container
     */
    protected $container;



    /**
     * @var string
     */
    protected $ns;

    /**
     * Kernel constructor.
     * @param ClassLoader $autoLoader
     * @param array $config
     * @param string $ns
     */
    public function __construct(ClassLoader $autoLoader, array $config = [], $ns = '')
    {
        $this->autoLoader = $autoLoader;
        $this->config = $config;
        $this->ns = $ns;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->ns;
    }

    /**
     * @param array $filePaths
     * @return $this
     */
    public function addConfigFiles(array $filePaths)
    {
        foreach ($filePaths as $filePath) {
            $this->addConfigFile($filePath);
        }
        return $this;
    }

    /**
     * @param $filePath
     * @return $this
     */
    public function addConfigFile($filePath)
    {
        $this->configFiles[] = $filePath;
        return $this;
    }

    /**
     * @param array $bundles
     * @return $this
     */
    public function addBundles(array $bundles)
    {
        foreach ($bundles as $bundle) {
            $this->addBundle($bundle);
        }
        return $this;
    }

    /**
     * @param Bundle $bundle
     * @throws \Exception
     * @return $this
     */
    public function addBundle(Bundle $bundle)
    {
        $bundleName = $bundle->getName();
        $parentBundles = $bundle->getParent();
        foreach ($parentBundles as $parentBundle) {
            if (!isset($this->bundles[$parentBundle])) {
                throw new \Exception("{$bundleName} require {$parentBundle}");
            }
        }
        if (!isset($this->bundles[$bundleName])) {
            $this->bundles[$bundleName] = $bundle;
            // config
            $bundleConfigFiles = $bundle->getConfigFiles();
            $this->addConfigFiles($bundleConfigFiles);
        }
        return $this;
    }


    /**
     * @param array $modules
     * @return $this
     */
    public function addModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }
        return $this;
    }

    /**
     * @param Module $module
     * @throws \Exception
     * @return $this
     */
    public function addModule(Module $module)
    {
        $moduleName = $module->getName();
        $parentModules = $module->getParent();
        foreach ($parentModules as $parentModule) {
            if (!isset($this->modules[$parentModule])) {
                throw new \Exception("{$moduleName} require {$parentModule}");
            }
        }
        $this->modules[$moduleName] = $module;
        // add bundle
        $moduleBundles = $module->getBundles();
        $this->addBundles($moduleBundles);
        // config
        $moduleConfigFiles = $module->getConfigFiles();
        $this->addConfigFiles($moduleConfigFiles);
        return $this;
    }

    /**
     * @param $name
     * @return Bundle
     * @throws \Exception
     */
    public function getBundle($name)
    {
        if (isset($this->bundles[$name])) {
            return $this->bundles[$name];
        }
        throw new \Exception($name . ' bundle not exists');
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasBundle($name)
    {
        return isset($this->bundles[$name]) ? true : false;
    }


    /**
     * @param $name
     * @return Module
     * @throws \Exception
     */
    public function getModule($name)
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }
        throw new \Exception($name . ' module not exists');
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasModule($name)
    {
        return isset($this->modules[$name]) ? true : false;
    }


    /**
     * @throws \Exception
     */
    protected function initContainer()
    {
        $builder = new ContainerBuilder($this->ns);
        $containerPath = $this->ns ? $this->ns . '/' : '';
        $configCacheFilename = CACHE_ROOT . '/' . $containerPath . 'Config.php';
        $containerClass = $this->ns . 'ServiceContainer';
        $containerFile = CACHE_ROOT . '/' . $containerPath . $containerClass . '.php';
        $needRebuild = false;
        if (ENV != 'online' || !file_exists($containerFile)) {
            $needRebuild = true;
        }
        if ($needRebuild) {
            $baseConfig = array(
                'parameters' => array(), 'services' => array(), 'events' => array(), 'routing' => array(),
            );
            $configuration = new Config($baseConfig, true);
            foreach ($this->configFiles as $configFile) {
                if (!$configFile) {
                    continue;
                }
                $pathInfo = pathinfo($configFile);
                $extension = $pathInfo['extension'];

                $parser = $this->getFileParser($extension);
                if (!$parser) {
                    throw new \Exception('文件类型 ' . $extension . ' 的配置解析器找不到！' . $configFile);
                }
                $config = $parser($configFile);
                if (!$config) {
                    $config = $baseConfig;
                }
                $config = new Config($config);
                $configuration->merge($config);
            }
            $configuration->merge(new Config(array('parameters' => $this->config), true));
            $parameters = $configuration['parameters']->toArray();
            $parameters = $builder->resolveValue($parameters, array('parameters' => $parameters));
            $configuration['parameters'] = new Config($parameters);
            $builder->dump($configuration);
        }
        $this->config = require_once $configCacheFilename;
        require_once $containerFile;
        $this->container = new $containerClass();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->initContainer();
        }
        return $this->container;
    }

    /**
     * @param $extension
     * @return \Closure
     */
    public function getFileParser($extension)
    {
        if ($extension == 'yml') {
            return function ($path) {
                if ($path) {
                    return Yaml::parse(file_get_contents($path));
                }
                return array();
            };
        }
    }
}