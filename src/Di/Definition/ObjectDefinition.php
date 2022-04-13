<?php namespace Theory\Di\Definition;

use ReflectionClass;

class ObjectDefinition extends AbstractDefinition
{
    protected $container;
    protected $parameters = [];
    protected $share = false;

    public function resolve($container)
    {
        $this->container = $container;

        $object = new $this->value(...$this->resolveParameters());

        if($this->share === true) $container->share($object);

        return $object;
    }

    public function addParameterDefinition(string $name, AbstractDefinition $definition)
    {
        $this->parameters[$name] = $definition;

        return $this;
    }

    public function share()
    {
        $this->share = true;

        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['parameters'] = $this->parameters;

        return $array;
    }

    private function resolveParameters()
    {
        $resolved = [];

        $reflection = new ReflectionClass($this->value);

        $constructor = $reflection->getConstructor();
        if($constructor === null) return [];

        $parameters = $constructor->getParameters();
        if(empty($parameters)) return [];

        foreach($parameters as $parameter){
            $resolved[] = $this->resolveParameter($parameter);
        }

        return $resolved;
    }

    private function resolveParameter($parameter)
    {
        if(isset($this->parameters[$parameter->name])){
            return $this->container->resolve(
                $this->parameters[$parameter->name]
            );
        }

        $type = $parameter->getType();

        // if parameter has no type or isnt a ReflectionNamedType or is built in
        // then it can only be resolved if there's a default value
        if($type === null || !($type instanceof ReflectionNamedType) || $type->isBuiltIn()){

            if($parameter->isDefaultValueAvailable()) return $parameter->getDefaultValue();

            throw new UndefinedParameterException($parameter);
        }

        return $this->get($type->getName());
    }
}