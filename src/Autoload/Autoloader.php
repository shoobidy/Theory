<?php namespace Theory\Autoload;

class Autoloader
{
    private $registry;
    private $root;

    /**
     * Constructor
     *
     * @param array $registry - namespace => path/to/classes
     * @param string $root - /path/to/project/root
     */
    public function __construct(array $registry, string $root = '')
    {
        $this->registry = $registry;
        $this->setRoot($root);
        $this->register();
    }
    
    /**
     * Register a namespace
     * 
     * @param string $namespace
     * @param string $path - path/to/classes
     * 
     * NOTE: $path should be relative to the provided root path
     */
    public function addNamespace(string $namespace, string $path)
    {
        $this->registry[$namespace] = $path;
    }
    
    /**
     * Merge the current registry with another
     * 
     * @param array $registry - [$namespace => $path/to/classes]
     */
    public function addRegistry(array $registry)
    {
        $this->registry = array_merge($this->registry, $registry);
    }

    /**
     * Set the project directory
     *
     * @param string $dir - /path/to/project/root/directory
     *
     * NOTE: disable by passing an empty string as an argument ''
     */
    public function setRoot(string $dir)
    {
        if ($dir === '') return $this->root = '';

        $this->root = rtrim($dir, '/\\') . '/';
    }

    /**
     * Register Autoload Handler
     */
    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * Unregister Autoload Handler
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    private function load($namespace)
    {
        $parts = explode('\\', $namespace);
		
        // no namespace
        if (empty($parts)) return false;

        $class = array_pop($parts);

        while (!empty($parts)) {
            $key = implode('\\', $parts);

            if (!isset($this->registry[$key])) {
                $class = array_pop($parts) . '/' . $class;
                continue;
            }

            $file = $this->root . rtrim($this->registry[$key], '/\\') . '/' . $class . '.php';

            // if file doesn't exist
            if (!is_file($file)) return false;

            include $file;

            return $file;
        }

        return false;
    }
}
