# Autoload
The Autoload component simplifies PHP's autoloading functionality using PSR-4 specifications.

Usage
-----
```php
include '/path/to/theory/src/Autoload/Autoloader.php';

$definitions = [
  'Theory' => '/path/to/theory/src',
  'My/Namespace' => '/path/to/directory'
];

$autoloader = new Theory\Autoload\Autoloader($definitions);
$autoloader->register();
```
Notes
-----
Namespace definitions can use '/' or '\\' as separators
```php
$forwardslash = ['My/Namespace'  => '/path/to/directory'];
$backslash    = ['My\\Namespace' => '/path/to/directory'];
```
