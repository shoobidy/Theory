# Autoloader

A simple PSR-4 Autoloader

### Usage

// $registry = include '/path/to/registry.php';

// $registry = json_decode('/path/to/registry.json');

$registry = ['App' => '/www/app/src'];

$autoloader = new Autoloader($registry);


### Constructor

*Autoloader::__construct(array $registry, string $root = '')*

$registry - is where you define your namespaces in an array using the following format: ['namespace' => '/path/to/classes']

$root - is a common root path that will be prepended to the paths in the registry array


### Public Methods

| Method | Description |
| --------- | ---- |
| addNamespace(string $namespace, string $path) | Add a namespace and it's path to the registry property |
| addRegistry(array $registry) | Merge the given $registry with the current registry property |
| setRoot(string $root) | Set the root property - You can also pass an empty string ($autoloader->setRoot('')) to remove the root property
| register() | Register the autoloader (this is called automatically by the constructor so you only need to call this method if you have previously called unregister())
| unregister() | Unregister the autoloader - in other words that instance of the autoloader will stop being used