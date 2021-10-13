<?php
return array(
  'cache' => array(
    'value' => array(
      'type' => 'memcache',
      'memcache' => array(
        'host' => '127.0.0.1',
        'port' => '11211',
      ),
      'sid' => $_SERVER["DOCUMENT_ROOT"]."#01"
    ),
  ),
);
?>