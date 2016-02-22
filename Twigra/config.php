<?php

define('DSN', 'mysql:host=mysql480.db.sakura.ne.jp;dbname=miyama2013_twigra_test');
define('DB_USER', 'miyama2013');
define('DB_PASSWORD', 'miyama_gogo9738');
 
define('CONSUMER_KEY', '6oPVYaYS1IVwpvpcsUGA');
define('CONSUMER_SECRET', 't35EbieiYGHjPqKFffvbISQabjJirzYJ52cDDQyz4');
 
define('SITE_URL', 'http://miyama2013.sakura.ne.jp/connect_php/');
 
error_reporting(E_ALL & ~E_NOTICE);
 
session_set_cookie_params(0, '/connect_php/');
