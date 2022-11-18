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
$form->onCorrect("doRun");

// some fields + button
$form -> textField("Field 1", "fld1");
$form -> textField("Field 2", "fld2");
$form -> textField("Field 3", "fld3");
$form -> submitButton("Submit", "submitBtn");

// the tabs!
$tabs = array(
  3 => "fld1",
  1 => "fld2",
  2 => "fld3",
  4 => "submitBtn"
);

// set the tabs
$form -> setTabIndex($tabs);

/* // this is also correct!
* $form -> setTabIndex( "fld2, fld3, fld1, submitBtn" );
*/ 

//$form->setFocus(true);

$form->flush();

// function to show a message
function doRun($data) 
{
    return print_r($data);
} 