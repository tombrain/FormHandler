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
$form = new FormHandler();

// textfield
$form -> textField("Name", "name", FH_STRING);

// set a default value
$form -> setValue("name", "Enter your name...");

// options to use in the listfield
$options = array(
    1 => "option 1",
    2 => "option 2",
    3 => "option 3"
  ); 

  // listfield
$form -> listField("Options", "options", $options, FH_NOT_EMPTY, true);

// set a default value
// (We have to set the index of the array,
// because we have set the option useArrayKeyAsValue to true.
// If we had not done that, we had to set
// the real "value" of the options array)
$form -> setValue( "options", "1, 3" );

// This was also correct!!
// $form -> setValue( "options", array(1, 3) ); 

// button
$form -> submitButton();

// set the 'commit after form' function
$form -> onCorrect("doRun");

// flush the form
$form -> flush();

// the 'commit after form' function
function doRun( $data ) 
{
    $t = "Hello ". $data["name"];

    global $options;

    $msg = "Selected: \n";

    // show the selected options
    foreach($data['options'] as $id) 
    {
        $msg .= " - ".$options[$id]." \n";
    }

    return "{$t} ||| {$msg}"; 
} 