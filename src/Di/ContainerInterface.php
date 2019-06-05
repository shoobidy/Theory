<?php namespace Theory\Di;

interface ContainerInterface
{
    public function get(string $id, array $config = []);
    public function create(string $id, array $config = []);
    public function config(string $id, array $config);
    public function set(array $config);
    public function merge(array $config);
}
