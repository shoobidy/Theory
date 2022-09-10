<?php namespace Theory\Di;

use ReflectionClass;
use ReflectionNamedType;

use Theory\Di\Definition\{
    AbstractDefinition,
    ObjectDefinition,
    ValueDefinition
};

use Theory\Di\Exception\{
    ClassNotShared,
    UndefinedParameter
};

use App\Model\Service\FilterService;

class Container
{
    private $share = [];
    private $definitions;

    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }

    public function get(string $id)
    {
        return $this->define($id)->resolve($this);
    }

    public function resolve(AbstractDefinition $definition)
    {
        return $definition->resolve($this);
    }

    public function share(object $obj)
    {
        $this->share[$obj::class] = $obj;
    }

    public function getSharedObject($id)
    {
        if(!isset($this->share[$id])) throw new ClassNotShared($id);

        return $this->share[$id];
    }

    private function define(string $id)
    {
        if(isset($this->definitions[$id])) return $this->definitions[$id];

        return $this->reflect($id);
    }

    private function reflect(string $id)
    {
        $definition = new ObjectDefinition($id);

        $reflection = new ReflectionClass($id);

        $constructor = $reflection->getConstructor();

        if($constructor === null) return $definition;

        $parameters = $constructor->getParameters();

        if(empty($parameters)) return $definition;

        foreach($parameters as $parameter){
            $definition->addParameterDefinition(
                $parameter->getName(), 
                $this->reflectParameterDefinition($parameter)
            );
        }

        return $definition;
    }

    private function reflectParameterDefinition($parameter)
    {
        $type = $parameter->getType();
 
        if($type === null || !($type instanceof ReflectionNamedType) || $type->isBuiltIn()){

            if($parameter->isDefaultValueAvailable()){
                return new ValueDefinition($parameter->getDefaultValue());
            }

            throw new UndefinedParameter($parameter);
        }

        return $this->define($type->getName()); 
    }
}