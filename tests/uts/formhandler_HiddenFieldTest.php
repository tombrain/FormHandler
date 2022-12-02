<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_HiddenFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->hiddenField("hiddenfield", "thevalue");

        $this->assertEquals("thevalue", $form->getValue("hiddenfield"));

        $this->assertFormFlushContains($form, ['<input type="hidden" name="hiddenfield" id="hiddenfield" value="thevalue" />']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['hiddenfield'] = "hiddenvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield");

        $this->assertEquals("hiddenvalue", $form->getValue("hiddenfield"));
    }
    
    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['hiddenfield'] = "hiddenvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield");

        $this->assertEquals("hiddenvalue", $form->getValue("hiddenfield"));

        $form->setError("hiddenfield", "forcedError");

        $this->assertFormFlushContains($form, ['<input type="hidden" name="hiddenfield" id="hiddenfield" value="hiddenvalue" />',
                                                '<span id="error_hiddenfield" class="error">forcedError</span>']);
    }
    
    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield", "", FH_NOT_EMPTY);

        $this->assertEmpty($form->getValue("hiddenfield"));

        $t = $form->catchErrors(false);

        $this->assertEquals('<span id="error_hiddenfield" class="error">You did not enter a correct value for this field!</span>',
                                $t['hiddenfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->hiddenField("hiddenfield", "", null, 'data-old="123"');

        $this->assertEmpty($form->getValue("hiddenfield"));

        $this->assertFormFlushContains($form, ['<input type="hidden" name="hiddenfield" id="hiddenfield" value="" data-old="123" />']);
    }

};
