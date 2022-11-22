<?php  declare(strict_types=1); ?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
</head>
<body>
<?php

require_once '../../vendor/autoload.php';

class myFormHandler extends dbFormHandler
{
	private $isCopy = false;

	public function __construct( $name = null, $action = null, $extra = null )
	{
		parent::__construct( $name, $action, $extra );

		if (isset($_GET['funktion']) && $_GET['funktion'] == "copy")
		{
			$this->isCopy = true;
			$this->insert = true;
		}
	}
	
	public function flush( $return = false )
	{
		if ($this->isCopy)
		{
			$this->_id	= array();
			$this->edit	= false;
		}

		return parent::flush($return);
	}
}

define('FH_FHTML_DIR', 'http://formhandler.test/src/FHTML/' );
ini_set("display_errors", "1");
@error_reporting(E_USER_WARNING | E_ALL);

$form = new myFormHandler();
// Set the database info and connect! (Create a new connection)
$form->dbInfo( "formhandler", "test", "mysqli" ); 
$form->dbConnect( "localhost", "formhandler", "formhandler" );

$form->onCorrect("doRun");

$form->textField("Text1", "text1");
$form->textField("Digit1", "digit1", FH_DIGIT);

$form->submitButton();


$form->flush();

function mOnCorrect(array $fields, FormHandler $fh)
{
    var_dump($fields);
    var_dump($fh);
}

// function to show a message
function doRun($data) 
{
    return print_r($data);
} 