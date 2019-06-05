<?php namespace Theory\Di;

interface ContainerInterface
{
    public function get(string $id, array $config = []);
    public function create(string $id, array $config = []);
    public function setRules(array $rules);
    public function addRule(string $id, array $rule);
    public function addRules(array $rules);
}
