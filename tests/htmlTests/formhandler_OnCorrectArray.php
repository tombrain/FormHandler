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


/**** Simple example class!! *******/
class Example 
{
    // this is the method we are going to call
    // when the form is correct!
    function doRun( $data, &$form ) 
    {
        echo "Hello! Your name is " . $data["name"];
    }
} 
// create a new example object
// for usage in the onCorrect call!
$example = new Example();

// create new formhandler object
$form = new FormHandler("myForm");

// simple textfield + submitbutton
$form -> textField    ( "Name", "name", FH_STRING );
$form -> submitbutton ( "Save" );

// set the method which we should call when the form is saved
$form->onCorrect(
  // note: the & caracter is needed!
  array(&$example, "doRun")
);

// flush the form
$form -> flush(); 