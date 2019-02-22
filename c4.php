<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('ai.php');
header("Access-Control-Allow-Origin: *");
$ai = new ai();
$ai->go();



