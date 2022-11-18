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

// uploads in tempdir
$uploaddir = sys_get_temp_dir() . "/formhandler_upload_tests";
if (!is_dir($uploaddir))
  mkdir($uploaddir);

require_once '../../vendor/autoload.php';

$form = new dbFormHandler();
// Set the database info and connect! (Create a new connection)
$form->dbInfo( "formhandler", "test", "mysqli" ); 
$form->dbConnect( "localhost", "formhandler", "formhandler" );

$form->onCorrect("doRun");

$form->textField("Text1", "text1");
$form->textField("Digit1", "digit1", FH_DIGIT);
$form->setErrorMessage( "digit1", "Erbitte Digit!");
$form->setFocus("digit1");
$form->textField("Text2", "text2", FH_NOT_EMPTY);
$form->setHelpText('text1', 'Type here your help message!');
$form->setHelpIcon("check.png");

$form->textArea("Textarea1", "textarea1", FH_TEXT);
$form->textArea("Textarea2", "textarea2", _FH_TEXT);
$form->textArea("Textarea3", "textarea3", FH_VARIABLE);


// create the two password fields
$form->passField( "Password", "myPass" ); 
$form->passField( "Retype password", "myPass_re" ); 

// check the password fields. 
// See for more info about this function the manual
$form->checkPassword( "myPass", "myPass_re" );

// set a hidden field
$form->hiddenField("language", "nl");

// the options
$browsers = array(
    ""             => "-- Select --",
    "__LABEL(IE)__" => "Microsoft Internet Explorer",
    "msie3"         => "Microsoft Internet Explorer 3",
    "msie4"         => "Microsoft Internet Explorer 4",
    "msie5"         => "Microsoft Internet Explorer 5",
    "msie55"        => "Microsoft Internet Explorer 5.5",
    "msie6"         => "Microsoft Internet Explorer 6",
    "__LABEL(MO)__" => "Mozilla",
    "moz1"          => "Mozilla 1",
    "__LABEL(NN)__" => "Netscape Navigator",
    "nn3"           => "Netscape Navigator 3",
    "nn4"           => "Netscape Navigator 4",
    "nn6"           => "Netscape Navigator 5",
    "nn6"           => "Netscape Navigator 6",
    "nn7"           => "Netscape Navigator 7",
    "__LABEL(OP)__" => "Opera",
    "op3"           => "Opera 3",
    "op35"          => "Opera 3.5",
    "op4"           => "Opera 4",
    "op5"           => "Opera 5",
    "op6"           => "Opera 6",
    "op7"           => "Opera 7"
);
$form->selectField("Your browser", "browser", $browsers, FH_NOT_EMPTY, true);

// the options for the checkbox
$animals = array(
    "Dog",
    "Cat",
    "Cow"
  ); 
  // make the checkbox
$form->checkBox("Favorite animal(s)", "animal", $animals, null, false);
$form->checkBox("Ok?", "ok", 1); 

// the options for the radiobutton
$gender = array(
    "m" => "Male",
    "f" => "Female"
  ); 
$form->radioButton("Gender", "gender", $gender, FH_NOT_EMPTY);
$form->SetValue("gender", "m");

$cfg = array(
    "path"       => $uploaddir,  
    "type"       => "jpg jpeg",
    "name"       => "", // <-- keep the original name
    "required"   => true,
    "exists"     => "rename"
  );
// upload field
$form->uploadField("Image", "image", $cfg);

// the values for the listfield
$values = array(
    1 => "PHP",
    2 => "MySQL database",
    3 => "Frontpage extensions",
    4 => "ASP",
    5 => "10 MB extra webspace",
    6 => "Webmail",
    7 => "Cronjobs"
  ); 
  $form->ListField("Products", "products", $values); 
  $form->ListField("Products", "products2", $values, null, null, null, null, null, null, true); 

  $form->editor("Message", "message", null, $uploaddir);

// start a fieldset! 
$form->borderStart("Browser");    
$form->dateField("Date1", "date1", null, false);
$form->dateField("Date2 (not null)", "date2", null, false);
$form->dateField("Date3", "date3", null, false, "D.M.Y");

$form->jsDateField("Date4", "date4", null, false);
$form->jsDateField("Date5 (not null)", "date5", null, false);
$form->jsDateField("Date6", "date6", null, false, "D.M.Y");

$form->dateTextField("Birthdate", "birthdate");
$form->jsDateTextField("Birthdate", "birthdate2"); 
// stop the border 
$form->borderStop(); 
$form->timeField("Time", "time"); 

$form->browserField('Image','image2', $uploaddir);

$form->colorpicker("Color", "color");

// set the options array
$aOptions = array( 'Red', 'Green' );
// new TextSelect field
$form->textSelectField( 'Color', 'color2', $aOptions );

$form->CaptchaField("Verify the code", "code"); 

$form->button("Test", "btnTest", "onclick='alert(this.name)'");

$form->imageButton("check.png", "check", "onclick='alert(this.name)'");

$form->resetButton(); 
$form->submitButton();

// Limited textarea 
$form->textArea("Text", "myTextArea"); 
$form->setMaxLength("myTextArea", 20); 

// addHTML! 
$form->addHTML( 
  "  <tr>\n". 
  "    <td colspan='3'><hr size='1' /></td>\n". 
  "  </tr>\n" 
);  

// star for required fields 
$star = ' <font color="red">*</font>'; 

// some fields 
$form->textField("Name".$star, "name", FH_STRING, 20, 50); 
$form->textField("Age".$star, "age", FH_INTEGER, 3, 2); 

// add a line that every field with a red * is required 
$form->addLine($star ."  = Required fields");  

//$form->useTable(false);

// set another type of mask 
$form->setMask( 
  "  <tr><td>%title% %seperator%</td></tr>\n". 
  "  <tr><td>%field% %error%</td></tr>\n", 
  2 
); 

// some fields 
$form->textField("Name", "name2", FH_STRING); 
$form->textField("Age", "age2", FH_INTEGER, 3, 2); 
$form->selectField("Gender", "gender2", array('M', 'F'), null, false);

// the auto complete items 
$colors3 = array ( "red", "orange", "yellow", "green", "blue", "indigo", "violet", "brown", "rood" ); 
// the textfield used for auto completion 
$form->textField("Type a color", "color3", FH_STRING); 

// set the auto completion for the field Color 
$form->setAutoComplete("color3", $colors3); 

// the auto complete items 
$providers = array ( "hotmail.com", "live.com", "php-globe.nl", "freeler.nl" ); 
// the textfield used for auto completion after
$form->textField("Type your email", "email", FH_STRING); 
// set the auto completion for the field Color 
$form->setAutoCompleteAfter("email", "@", $providers);

// set the language to dutch
$form->setLanguage( 'de-utf8' );

$form->dbSelectField(
  'Options from a table',
  'saveInField',
  'loadFromTable',
  array('keyField', 'valueField'),
  ' ORDER BY `valueField`',
  FH_NOT_EMPTY
);
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