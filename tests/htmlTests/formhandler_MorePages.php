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

//first page... 
$form -> textField("Question 1", "q1", FH_STRING, 30, 50); 
$form -> submitButton("Next page"); 

// second page 
$form -> newPage(); 
$form -> textArea("Question 2", "q2", FH_TEXT); 
$form->backButton();
$form -> submitButton("Next Page"); 

// third and last page 
$form -> newPage(); 
$form -> textField("Question 3", "q3", FH_STRING); 
$form->backButton();
$form -> submitButton("Submit"); 
$form->flush();

// function to show a message
function doRun($data) 
{
    return print_r($data);
} 