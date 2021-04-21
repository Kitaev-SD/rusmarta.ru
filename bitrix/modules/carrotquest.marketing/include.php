<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses('carrotquest.marketing', array(
    'CarrotQuest\Marketing\CarrotEvents'=>'lib/carrotevents.php',
	'CarrotQuest\Marketing\CarrotEventsOrder'=>'lib/carroteventsorder.php',
	'CarrotQuest\Marketing\CarrotEventsBasket'=>'lib/carroteventsbasket.php'
));