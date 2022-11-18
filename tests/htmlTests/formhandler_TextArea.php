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

$form->textArea("Textarea1", "textarea1");
$form->textArea("Textarea2", "textarea2", FH_NOT_EMPTY);
$form->textArea("Textarea3", "textarea3", null, 5, 10);

$form->submitButton();

$form->flush();

function mOnCorrect(array $fields, FormHandler $fh)
{
    var_dump($fields);
    var_dump($fh);
}