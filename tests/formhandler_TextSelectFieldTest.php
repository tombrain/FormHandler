<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';

define( 'FH_TEXTSELECT_MASK', '%s-%s-%d-%s-%s-%s');
define( 'FH_TEXTSELECT_OPTION_MASK', '%s-%s');

final class formhandler_TextSelectFieldTest extends FormhandlerTestCase
{
    private $aOptions = [
        "o1" => "Option1",
        "o2" => "Option2",
        "o3" => "Option3"
    ];
    
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        $this->assertEmpty($form->getValue("textselectfield"));

        $this->assertFormFlushContains($form, ["<script type=\"text/javascript\">\n" .
                                                "function FH_CLOSE_TEXTSELECT( id )\n" .
                                                "{\n" .
                                                "  setTimeout( 'document.getElementById(\"'+id+'\").style.display=\"none\"', 110 );\n" .
                                                "}\n" .
                                                "\n" .
                                                "function FH_SET_TEXTSELECT( id, waarde )\n" . 
                                                "{\n" .
                                                "  document.getElementById(id).value=waarde;\n" .
                                                "  FH_CLOSE_TEXTSELECT( 'FHSpan_'+id );return false;\n" .
                                                "}\n" .
                                                "\n" .
                                                "</script>\n",
                                                'Textselectfield:textselectfield--20---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textselectfield'] = "Option2";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        $this->assertEquals("Option2", $form->getValue("textselectfield"));

        $this->assertTrue($form->isCorrect());
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textselectfield'] = "Option2";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        $this->assertEquals("Option2", $form->getValue("textselectfield"));

        $form->setError("textselectfield", "forcedError");

        $this->assertFormFlushContains($form, ['Textselectfield:textselectfield-Option2-20---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield<span id="error_textselectfield" class="error">forcedError</span>']);
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, 123);

        $this->assertEmpty($form->getValue("textselectfield"));

        $this->assertFormFlushContains($form, ['Textselectfield:textselectfield--123---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }

    public function test_new_maxlenght(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, null, 456);

        $this->assertEmpty($form->getValue("textselectfield"));

        $this->assertFormFlushContains($form, ['Textselectfield:textselectfield--20-maxlength="456" --textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("textselectfield"));

        $this->assertFormFlushContains($form, ['Textselectfield:textselectfield--20- data-old="123" --textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
     }

};
