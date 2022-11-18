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

// textfield
$form -> textField("Name", "name", FH_STRING);

// submitbutton
$form -> submitButton("Save");

// get the errors of invalid fields
$errors = $form->catchErrors();

// oncorrect function...
$form -> onCorrect('doRun');

// flush
$form -> flush();

/** handle your own errors! **/

// any errors?
if( sizeof($errors) > 0 ) 
{
    // create a JS message
    $msg = "Some fields are incorrect!\\n";

    foreach($errors as $field => $error) 
    {
        $msg .= "- ". $form -> getTitle( $field )."\\n";
    }
    echo
    "<script language='javascript'>\n".
    "alert('".$msg."');\n".
    "</script>\n";
} 

// function to show a message
function doRun($data) 
{
    return print_r($data);
} 