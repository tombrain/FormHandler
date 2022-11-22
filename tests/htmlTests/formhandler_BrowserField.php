<?php  declare(strict_types=1); ?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
</head>
<body>
<?php

define('FH_FHTML_DIR', 'http://formhandler.test/src/FHTML/' );
ini_set("display_errors", "1");
@error_reporting(E_USER_WARNING | E_ALL);

// uploads in tempdir
$uploaddir = sys_get_temp_dir() . "/formhandler_upload_tests";
if (!is_dir($uploaddir))
  mkdir($uploaddir);

require_once '../../vendor/autoload.php';

$form = new FormHandler();
$form->onCorrect("mOnCorrect");

$form->browserField("Browserfield", "browserfield", $uploaddir);

$form->submitButton();

$form->flush();

function mOnCorrect(array $fields, FormHandler $fh)
{
    var_dump($fields);
    var_dump($fh);
}