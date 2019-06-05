<?php namespace Theory\Di;

use ReflectionClass;
use Theory\Cache\CacheInterface;
use Theory\Exception\TheoryException;

/**
 * A simple Dependency Injection Container
 * 
 * Making heavy use of caching to avoid using reflection
 */
class Container implements ContainerInterface
{
    private $shared = [];
    private $cache;
    private $config;

    /**
     * Constructor
     *
     * @param CacheInterface - instance of CacheInterface
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * Get a shared instance or create a new instance
     * 
     * @param string $id - class/interface name
     * @param array $config - configuration
     */
    public function get(string $id, array $config = [])
    {
        // if a shared instance exists
        if (isset($this->shared[$id])) {
            // check if the shared object feature was overwritten
            if (!isset($config['share']) || $config['share'] === true) {
                return $this->shared[$id];
            }
        }
        
        // create new instance
        return $this->create($id, $config);
    }
    
    /**
     * Create a new instance
     * 
     * @param type $id - class/interface name
     * @param array $config - configuration
     */
    public function create($id, array $config = [])
    {
        // if there's no cached data
        if (!$this->cache->has($id)) {
            $this->cacheObjectData($id);
        }
        
        $cache = $this->cache->get($id);
        
        // get configuration
        $c = $this->getConfig($id, $cache['parent'], $config);
        
        // if a classname was configured
        if (isset($c['class'])) return $this->get($c['class']);
        
        // if no configuration
        if (empty($c)) return $this->build($id);
        
        // configure and return the object
        return $this->configure($id, $c);
    }
    
    /**
     * Add a configuration for a class/interface
     * 
     * @param string $id - name of class/interface
     * @param array $config - configuration
     */
    public function config(string $id, array $config)
    {
        $this->config[$id] = $config;
    }
    
    /**
     * Set the config property
     * 
     * @param array $config
     * 
     * NOTE: this replaces the old $config value with the new one
     */
    public function set(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * Merge the current config property with the $config value
     * 
     * @param array $config
     */
    public function merge(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Create an object from cache without any configuration
     */
    private function build($id)
    {        
        $cache = $this->cache->get($id);
        
        // if there is no constructor
        if ($cache['__construct'] === null) return new $id;
        
        $params = [];
        
        // resolve constructor parameters
        foreach ($cache['__construct'] as $name => $param) {
            // check if param has a class/interface type hint
            if (isset($param['type'])) {
                $params[] = $this->get($param['type']);
                continue;
            }
            
            if ($param['has.default.value']) {
                $params[] = $param['default.value'];
                continue;
            }
            
            throw new TheoryException("Unable to resolve parameter: $id::__construct($name)");
        }
        
        return new $id(...$params);
    }
    
    /**
     * Create an object from cache with configuration
     */
    private function configure($id, $config)
    {
        $cache = $this->cache->get($id);
        
        if ($cache['__construct'] === null) {
            // if there is no constructor or params
            $obj = new $id;
        } else {
            if (isset($config['params'])) {
                // if there is a configuration for the params
                $params = $this->getConfigParams(
                        $id,
                        $cache['__construct'],
                        $config['params']
                );
            } else {
                $params = $this->getCacheParams($id, $cache['__construct']);
            }
            
            $obj = new $id(...$params);
        }
        
        // if a method was configured to be called
        if (isset($config['call'])) {
            foreach ($config['call'] as $method => $args) {
                if (!isset($cache[$method])) {
                    $params = $this->cacheMethodParams($id, $method);
                } else {
                    $params = $cache[$method];
                }
                
                $obj->$method(...$this->getConfigParams(
                    $id, $params, $args
                ));
            }
        }
        
        // if the object has been configured to be reused
        if (isset($config['share']) && $config['share'] === true) {
            $this->shared[$id] = $obj;
        }
        
        return $obj;
    }
    
    /**
     * Resolve parameters with configuration
     */
    private function getConfigParams($id, $cache, $config)
    {
        $result = [];
        
        foreach ($cache as $name => $param) {
            // if the param is a typehint for a class/interface
            if (isset($param['type'])) {
                if (isset($config[$param['type']])) {
                    // if the param was configured by typehint
                    $result[] = $this->get($config[$param['type']]);
                } else if (isset($config[$name])) {
                    // if the param was configured by name
                    $result[] = $this->get($config[$name]);
                } else {
                    // if the param wasn't configured
                    $result[] = $this->get($param['type']);
                }
            } else {
                if (isset($config[$name])) {
                    $result[] = $config[$name];
                } else {
                    $result = $this->getCacheParam($id, $name, $param);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Resolve a parameter from cache without configuration
     */
    private function getCacheParam($id, $name, $param)
    {
        if ($param['has.default.value']) return $param['default.value'];
        
        throw new TheoryException("Unable to resolve parameter: $id::__construct($name)");
    }
    
    /**
     * Resolve all parameters from cache without configuration
     */
    private function getCacheParams($id, $params)
    {
        $result = [];
        
        foreach ($params as $name => $param) {
            $result[] = $this->getCacheParam($id, $name, $param);
        }
        
        return $result;
    }
    
    /** 
     * Add all the things necessary to create the object to cache
     */
    private function cacheObjectData($id)
    {
        $reflection = new ReflectionClass($id);
        
        $parent = $reflection->getParentClass();
        
        // if the object has an extended class
        if ($parent === false) {
            $this->cache->addNested([$id, 'parent'], null);
        } else {
            $this->cache->addNested([$id, 'parent'], $parent->name);
        }
        
        // if the object has a constructor
        if ($reflection->hasMethod('__construct')) {
            $this->cacheMethodParams($id, '__construct', $reflection);
        } else {
            $this->cache->addNested([$id, '__construct'], null);
        }
    }
    
    /**
     * Add a method and it's parameters to cache
     */
    private function cacheMethodParams($id, $method, $reflection = null)
    {
        // reflection is expensive, gotta be effecient with it
        if ($reflection === null) {
            $reflection = new ReflectionClass($id);
        }
        
        $m = $reflection->getMethod($method);
        
        $params = $m->getParameters();
        
        // if there are no parameters
        if ($params === null) {
            $this->cache->addNested([$id, $method], null);
            return;
        }
        
        $result = [];
        
        foreach ($params as $param) {
            $type = $param->getType();
            
            if ($type !== null && !$type->isBuiltIn()) {
                $result[$param->name]['type'] = $type->__toString();
            }
            
            // d for default
            $d = $param->isDefaultValueAvailable();
            
            // set a default value boolean to check if a default value exists 
            // without relying on array_key_exists
            $result[$param->name]['has.default.value'] = $d;
            
            // if a default value exists
            if ($d) {
                $result[$param->name]['default.value'] = $param->getDefaultValue();
            }
        }
        
        $this->cache->addNested([$id, $method], $result);
        
        return $result;
    }
    
    /**
     * Combine all possible configurations
     * 
     * replaced array_merge with foreach loops
     */
    private function getConfig($class, $parent, $config)
    {
        $result = [];
        
        // if an extended class has a configuration
        if (isset($this->config[$parent])) {
            foreach($this->config[$parent] as $k => $v) {
                $result[$k] = $v;
            }
        }
        
        // if the class/interface has a config
        if (isset($this->config[$class])) {
            foreach($this->config[$class] as $k => $v) {
                $result[$k] = $v;
            }
        }
        
        // if there was a config passed with the get/create method
        if (!empty($config)) {
            foreach($config as $k => $v) {
                $result[$k] = $v;
            }
        }
        
        return $result;
    }
}
