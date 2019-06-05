<?php return array (
  'Theory\\Tests\\Di\\TestObjects\\Obj' => 
  array (
    'parent' => NULL,
    '__construct' => NULL,
  ),
  'Theory\\Tests\\Di\\TestObjects\\Autowire' => 
  array (
    'parent' => NULL,
    '__construct' => 
    array (
      'a' => 
      array (
        'type' => 'Theory\\Tests\\Di\\TestObjects\\Obj',
        'has.default.value' => false,
      ),
      'b' => 
      array (
        'type' => 'Theory\\Tests\\Di\\TestObjects\\Obj',
        'has.default.value' => false,
      ),
    ),
  ),
);