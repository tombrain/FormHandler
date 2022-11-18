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

$form = new dbFormHandler();

$form -> setTableSettings(400, 2, 2, 0, ' style="border: 1px solid green"' );

// set the database info
$form->dbInfo( "formhandler", "test", "mysqli" ); 
$form->dbConnect( "localhost", "formhandler", "formhandler" );

// on edit mode, just display the data
if( $form -> edit )
{
    $form -> enableViewMode();
}

// the fields
$form -> textField( 'Name', 'text1', FH_STRING );
$form -> textField( 'Age', 'digit1', FH_DIGIT );

// view mode for the username in edit mode
if(! $form -> edit ) 
{
    $form -> setFieldViewMode( "text1" );
} 
// button to submit the form
if( !$form -> isViewMode() )
    $form -> submitButton();

if ($form->isFieldViewMode("digit1"))
    $form->addHTML("digit write");
else
    $form->addHTML("digit note write");

// what to do when the form is saved
$form -> onSaved('showMessage');

// display the form
$form -> flush();

// the function which is runned when the form is saved
function showMessage( $id, $data )
{
    echo
    "Hello ". $data['Name'] ."\n".
    "Your data is saved with id ". $id."\n";
} 