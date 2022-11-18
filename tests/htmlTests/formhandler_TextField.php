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

require_once '../../vendor/autoload.php';

$form = new FormHandler();
$form->onCorrect("mOnCorrect");

$form->textField("Text1", "text1");
$form->textField("Digit1", "digit1", FH_DIGIT);
$form->textField("Text2", "text2", FH_NOT_EMPTY);

$form->submitButton();

$form->flush();

function mOnCorrect(array $fields, FormHandler $fh)
{
    var_dump($fields);
    var_dump($fh);
}