# Autoload
The Autoload component simplifies PHP's autoloading functionality using PSR-4 specifications.

Usage
-----
Include the autoloader using the helper file which returns a registered instance of the autoloader class
```php
$autoloader = include '/path/to/theory/autoloader.php';
$autoloader->addNamespace('My/Namespace', '/path/to/directory');
```

OR instantiate the autoloader class
```php
include '/path/to/theory/src/Autoload/Autoloader.php';

$autoloader = new Theory\Autoload\Autoloader();
$autoloader->addNamespace('My/Namespace', 'path/to/directory');
$autoloader->register();
```

Notes
-----
Namespace definitions can use '/' or '\\' as separators
```php
$autoloader->addNamespace('My/Namespace', '/path/to/directory');
$autoloader->addNamespace('My\\Namespace', '\\path\\to\\directory');
```
