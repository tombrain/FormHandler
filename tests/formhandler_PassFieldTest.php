<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_PassFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->passField("Passfield", "passfield");

        $this->assertEmpty($form->getValue("passfield"));

        $this->assertFormFlushContains($form, ['Passfield:<input type="password" name="passfield" id="passfield" size="20" />error_passfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['passfield'] = "passvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->passField("Passfield", "passfield");

        $this->assertEquals("passvalue", $form->getValue("passfield"));
    }
    
    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['passfield'] = "passvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->passField("Passfield", "passfield");

        $this->assertEquals("passvalue", $form->getValue("passfield"));

        $form->setError("passfield", "forcedError");

        $this->assertFormFlushContains($form, ['Passfield:<input type="password" name="passfield" id="passfield" size="20" class="error" />error_passfield',
                                                '<span id="error_passfield" class="error">forcedError</span>']);
    }
    
    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->passField("Passfield", "passfield", FH_NOT_EMPTY);

        $this->assertEmpty($form->getValue("passfield"));

        $t = $form->catchErrors(false);

        $this->assertEquals('<span id="error_passfield" class="error">You did not enter a correct value for this field!</span>',
                                $t['passfield']);
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->passField("Passfield", "passfield", null, 123);

        $this->assertEmpty($form->getValue("passfield"));

        $this->assertFormFlushContains($form, ['Passfield:<input type="password" name="passfield" id="passfield" size="123" />error_passfield']);
    }

    public function test_new_maxlength(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->passField("Passfield", "passfield", null, null, 123);

        $this->assertEmpty($form->getValue("passfield"));

        $this->assertFormFlushContains($form, ['Passfield:<input type="password" name="passfield" id="passfield" size="20" maxlength="123" />error_passfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->passField("Passfield", "passfield", null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("passfield"));

        $this->assertFormFlushContains($form, ['Passfield:<input type="password" name="passfield" id="passfield" size="20" data-old="123" />error_passfield']);
    }

};
