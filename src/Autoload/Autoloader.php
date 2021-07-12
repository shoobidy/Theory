<?php namespace Theory\Autoload;

/**
 * PSR-4 compliant autoloader
 * 
 * https://www.php-fig.org/psr/psr-4/
 */
class Autoloader
{
    private $directories = [];

    /**
     * Directory separators at the end of filepaths aren't needed
     * 
     * Namespaces can be separated using '\\' or '/'
     * 
     * Example: (the 2 definitions below are equivalent)
     *   ['Vendor\\Namespace' => 'base/directory/path']
     *   ['Vendor/Namespace' => 'base/directory/path']
     * 
     * @param array $directories - ['Namespace' => 'base/directory/path']
     */
    public function __construct(array $directories)
    {
        $this->normalize($directories);
    }

    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * Tries to include the requested class file
     * 
     * @param string $namespace - fully qualified namespace
     * 
     * @return bool
     */
    public function load($namespace)
    {
        $namespacePath = str_replace('\\', '/', $namespace);
        $namespaceCopy = $namespacePath;

        while($offset = strrpos($namespaceCopy, '/')) {
            $id = substr($namespaceCopy, 0, $offset);

            if (!isset($this->directories[$id])) {
                $namespaceCopy = $id;
                continue;
            }

            $file = $this->directories[$id] . substr($namespacePath, $offset) . '.php';

            if (!file_exists($file)) return false;

            include $file;
            return true;      
        }

        return false;
    }

    private function normalize($directories)
    {
        foreach ($directories as $namespace => $directory) {
            $id = trim(str_replace('\\', '/', $namespace), '/');

            $this->directories[$id] = rtrim($directory, '/');
        }
    }
}