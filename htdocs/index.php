<?php

define ( 'SITE_ROOT', substr ( dirname(__FILE__), 0, strrpos ( dirname(__FILE__), '/' ) ) . '/' );   
include SITE_ROOT . 'config/config.inc';
require CORE_ROOT . 'kernel/index.inc';

?>