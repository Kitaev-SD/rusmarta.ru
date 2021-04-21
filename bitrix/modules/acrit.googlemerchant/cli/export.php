<?php
$strModuleId = pathinfo(realpath(__DIR__.'/../'), PATHINFO_BASENAME);
require(realpath(__DIR__.'/../../acrit.core/cli/export/'.pathinfo(__FILE__, PATHINFO_BASENAME)));
?>