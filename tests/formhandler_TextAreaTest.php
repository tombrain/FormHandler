<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_TextAreaTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textArea("Textarea", "textarea");

        $this->assertEmpty($form->getValue("textarea"));

        $this->assertFormFlushContains($form, ['Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7"></textarea>error_textarea']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textarea'] = "text\nvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textArea("Textarea", "textarea");

        $this->assertEquals("text\nvalue", $form->getValue("textarea"));
    }
    
    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textarea'] = "textvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textArea("Textarea", "textarea");

        $this->assertEquals("textvalue", $form->getValue("textarea"));

        $form->setError("textarea", "forcedError");

        $this->assertFormFlushContains($form, ['Textarea:<textarea class="error" name="textarea" id="textarea" cols="40" rows="7">textvalue</textarea>error_textarea',
                                                '<span id="error_textarea" class="error">forcedError</span>']);
    }
    
    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textArea("Textarea", "textarea", FH_NOT_EMPTY);

        $this->assertEmpty($form->getValue("textarea"));

        $t = $form->catchErrors(false);

        $this->assertEquals('<span id="error_textarea" class="error">You did not enter a correct value for this field!</span>',
                                $t['textarea']);
    }

    public function test_new_cols(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textArea("Textarea", "textarea", null, 123);

        $this->assertEmpty($form->getValue("textarea"));

        $this->assertFormFlushContains($form, ['Textarea:<textarea name="textarea" id="textarea" cols="123" rows="7"></textarea>error_textarea']);
    }

    public function test_new_maxlength(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textArea("Textarea", "textarea", null, null, 123);

        $this->assertEmpty($form->getValue("textarea"));

        $this->assertFormFlushContains($form, ['Textarea:<textarea name="textarea" id="textarea" cols="40" rows="123"></textarea>error_textarea']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textArea("Textarea", "textarea", null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("textarea"));

        $this->assertFormFlushContains($form, ['Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7" data-old="123"></textarea>error_textarea']);
    }

};
