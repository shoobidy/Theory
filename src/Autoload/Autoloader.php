<?php namespace Theory\Autoload;

/**
 * PSR-4 compliant autoloader
 * 
 * https://www.php-fig.org/psr/psr-4/
 */
class Autoloader
{
    /**
     * An array of ['namespace' => 'directory'] pairs
     */
    private $map = [];

    /**
     * Add a namespace and directory pair.
     * Both directory separators are allowed.
     * 
     * @var string $namespace - Your\Namespace OR Your/Namespace
     * @var string $directory - Your\Directory OR Your/Directory
     * 
     * @return self
     */
    public function addNamespace(string $namespace, string $directory)
    {
        $this->map[$this->normalize($namespace)] = $this->normalize($directory);

        return $this;
    }

    /**
     * Try to include a class file using it's qualified name.
     * A qualified name example: My\Namespace\Classname
     * 
     * @var    string - qualified class name 
     * @return bool   - true if class file was included, false if not
     */
    public function load(string $classname)
    {
        $parts = explode('/', $this->normalize($classname));
        $class = array_pop($parts);

        while(!empty($parts)){
            $id = implode('/', $parts);

            if(!isset($this->map[$id])){
                $class = array_pop($parts) . '/' . $class;
                continue;
            }

            $file = $this->map[$id] . '/' . $class . '.php';

            if(!file_exists($file)) break;

            include $file;
            
            return true;
        }

        return false;
    }

    /**
     * Add this autoloader to the spl autoloader queue
     * 
     * @return self
     */
    public function register()
    {
        spl_autoload_register([$this, 'load']);

        return $this;
    }

    /**
     * Change all backslashes to forward slashes and remove trailing slashes
     */
    private function normalize(string $string)
    {
        return rtrim(str_replace('\\', '/', $string), '/');
    }
}