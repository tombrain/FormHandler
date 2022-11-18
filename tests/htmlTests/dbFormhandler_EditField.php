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

// make a new formhandler object
$form = new dbFormHandler();
$form->dbInfo( "formhandler", "test", "mysqli" ); 
$form->dbConnect( "localhost", "formhandler", "formhandler" );
// passfield
$form->textField("Text1", "text2");

// submitbutton
$form -> submitButton("Save");

// set the onCorrect function
$form -> onCorrect("doRun");

// flush
$form -> flush();

// commit after form function
function doRun($data) 
{
    echo print_r($data);
} 