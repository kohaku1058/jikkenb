<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/util/env.php');

echo 'DB_HOST: '.env('DB_HOST', 'undefined').'<br />';
echo 'DB_PORT: '.env('DB_PORT', 'undefined').'<br />';
echo 'DB_DATABASE: '.env('DB_DATABASE', 'undefined').'<br />';
echo 'DB_USERNAME: '.env('DB_USERNAME', 'undefined').'<br />';
echo 'DB_PASSWORD: '.env('DB_PASSWORD', 'undefined').'<br />';
