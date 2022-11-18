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
// Set the database info and connect! (Create a new connection)
$form->dbInfo( "formhandler", "test", "mysqli" ); 
$form->dbConnect( "localhost", "formhandler", "formhandler" );

$form->onCorrect("doRun");

$form->dbSelectField('Options from a table',
                      'saveInField2',
                      'loadFromTable',
                      array('keyField', 'valueField'),
                      ' ORDER BY `valueField`',
                      FH_NOT_EMPTY,
                      true
                    );
$form->submitButton();
$form->flush();

// function to show a message
function doRun($data, dbFormHandler $form) 
{
    return print_r($data);
} 