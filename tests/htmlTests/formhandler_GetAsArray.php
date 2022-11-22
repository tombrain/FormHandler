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

// new form object
$form = new FormHandler();

// datefield
$form -> dateField( 'Date', 'date' );

// only when the form is posted..
if( $form -> isPosted() )
{
    // get the value from the field 
    list( $year, $month, $day ) = $form -> getAsArray( 'date' );

    echo 
    "Day: ". $day ."\n".
    "Month: ". $month ."\n".
    "Year: ". $year ."\n";
}

// submitbutton
$form -> submitButton();

// which function to run when the form is correct
$form -> onCorrect( 'doRun' );

// display the form
$form -> flush();

// the function which is called when the form is correct
function doRun( $data )
{
    // do something here..
    echo "Selected date: ". $data['date'] ."\n";
} 