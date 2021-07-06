<?php
$arUrlRewrite=array (
  47 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandex.market/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandex.market/trading/index.php',
    'SORT' => 100,
  ),
  40 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  42 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  30 => 
  array (
    'CONDITION' => '#^/acrit.googlemerchant/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.googlemerchant/index.php',
    'SORT' => 100,
  ),
  50 => 
  array (
    'CONDITION' => '#^/sitemap.xml#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/sitemap.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  41 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/contacts/\\??(.*)#',
    'RULE' => '&$1',
    'ID' => 'bitrix:catalog.section',
    'PATH' => '/include/header_location.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/promotions/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/promotions/index.php',
    'SORT' => 100,
  ),
  27 => 
  array (
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => 100,
  ),
  48 => 
  array (
    'CONDITION' => '#^/contacts/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/contacts/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/reviews/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/reviews/index.php',
    'SORT' => 100,
  ),
  36 => 
  array (
    'CONDITION' => '#^/vendors/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/vendors/index.php',
    'SORT' => 100,
  ),
  49 => 
  array (
    'CONDITION' => '#^/market/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/market/index.php',
    'SORT' => 100,
  ),
  33 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
);
