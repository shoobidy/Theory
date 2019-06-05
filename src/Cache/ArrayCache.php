<?php namespace Theory\Cache;

class ArrayCache implements CacheInterface
{
    private $cache;
    private $file;

    /**
     * Constructor
     * 
     * @param string $file - path to cache file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->load();
    }

    /**
     * Load cache from file
     */
    private function load()
    {
        if (is_file($this->file)) {
            $this->cache = include $this->file;
        } else {
            $this->cache = [];
        }
    }

    /**
     * 
     * Check if cache data exists
     * 
     * @param string $id - cache data id
     * 
     * @return boolean
     */
    public function has(string $id)
    {
        return isset($this->cache[$id]);
    }

    /**
     * 
     * Get cache data by id
     * 
     * @param string $id - id of item in cache
     * 
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->cache[$id];
    }
    
    /**
     * Get the cache property - $this->cache
     * 
     * @return array
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Add data to cache
     * 
     * @param string $id
     * @param mixed $data
     */
    public function add(string $id, $data)
    {
        $this->cache[$id] = $data;
    }
    
    public function addNested(array $keys, $value)
    {        
        $array = &$this->cache;
        
        foreach($keys as $key) {
            if (!is_array($array)) {
                $array = [];
            }
            
            if (!array_key_exists($key, $array)) {
                $array[$key] = null;
            }
            
            $array = &$array[$key];
        }
        
        $array = $value;
    }
    
    /**
     * Delete a single item from cache
     * 
     * @param string $id - cache data id
     */
    public function delete(string $id)
    {
        unset($this->cache[$id]);
    }

    /**
     * Reset cache and delete cache file
     */
    public function clear()
    {
        $this->cache = [];
        unlink($this->file);
    }
    
    /**
     * Check if the cache needs to be updated
     * 
     * @return boolean
     */
    public function isOld()
    {       
        if (!is_file($this->file)) return true;
        
        return $this->cache !== (include $this->file);
    }
    
    /**
     * Write cache to file
     */
    public function save()
    {
        return file_put_contents($this->file, '<?php return ' . var_export($this->cache, true) . ';');
    }
}
