<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';

final class formhandler_CheckboxTest extends FormhandlerTestCase
{
    private $aChecks = [
        "c1" => "Check1",
        "c2" => "Check2",
        "c3" => "Check3"
    ];
    
public function test_new_single(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->checkBox("Checkbox", "checkbox");

        $this->assertEmpty($form->getValue("checkbox"));

        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox" id="checkbox_1" value="on" />error_checkbox']);
    }

    public function test_new_array(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks);

        $this->assertEmpty($form->getValue("checkbox"));

        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox[]" id="checkbox_1" value="c1" /><label for="checkbox_1" class="noStyle">Check1</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_2" value="c2" /><label for="checkbox_2" class="noStyle">Check2</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_3" value="c3" /><label for="checkbox_3" class="noStyle">Check3</label>',
                                                'error_checkbox']);
    }

    public function test_new_array_useArrayKeyAsValueFalse(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks, null, false);

        $this->assertEmpty($form->getValue("checkbox"));

        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox[]" id="checkbox_1" value="Check1" /><label for="checkbox_1" class="noStyle">Check1</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_2" value="Check2" /><label for="checkbox_2" class="noStyle">Check2</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_3" value="Check3" /><label for="checkbox_3" class="noStyle">Check3</label>',
                                                'error_checkbox']);
    }

    public function test_new_array_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks, null, null, 'data-extra="true"');

        $this->assertEmpty($form->getValue("checkbox"));

        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox[]" id="checkbox_1" value="c1" data-extra="true" /><label for="checkbox_1" class="noStyle">Check1</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_2" value="c2" data-extra="true" /><label for="checkbox_2" class="noStyle">Check2</label>',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_3" value="c3" data-extra="true" /><label for="checkbox_3" class="noStyle">Check3</label>',
                                                'error_checkbox']);
    }

    public function test_new_array_mask(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks, null, null, null, "%field%ABC");

        $this->assertEmpty($form->getValue("checkbox"));

        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox[]" id="checkbox_1" value="c1" /><label for="checkbox_1" class="noStyle">Check1</label>ABC',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_2" value="c2" /><label for="checkbox_2" class="noStyle">Check2</label>ABC',
                                                '<input type="checkbox" name="checkbox[]" id="checkbox_3" value="c3" /><label for="checkbox_3" class="noStyle">Check3</label>ABC',
                                                'error_checkbox']);
    }

    public function test_posted_single(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['checkbox'] = "on";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->checkBox("Checkbox", "checkbox");

        $this->assertEquals("on", $form->getValue("checkbox"));
    }

    public function test_posted_array(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['checkbox'] = ["Check1", "Check2"];

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks);

        $this->assertEquals(["Check1", "Check2"], $form->getValue("checkbox"));
    }

    public function test_posted_single_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['checkbox'] = "on";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->checkBox("Checkbox", "checkbox");

        $this->assertEquals("on", $form->getValue("checkbox"));

        $form->setError("checkbox", "forcedError");
        $this->assertFormFlushContains($form, ['Checkbox:<input type="checkbox" name="checkbox" id="checkbox_1" value="on" checked="checked" class="error" />error_checkbox',
                                                '<span id="error_checkbox" class="error">forcedError</span>']);
    }

    public function test_posted_array_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['checkbox'] = ["Check1", "Check2"];

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->checkBox("Checkbox", "checkbox", $this->aChecks);

        $this->assertEquals(["Check1", "Check2"], $form->getValue("checkbox"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");

        $this->assertEquals("textvalue", $form->getValue("textfield"));

        $form->setError("textfield", "forcedError");

        $this->assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="textvalue" size="20" class="error" />error_textfield',
                                                '<span id="error_textfield" class="error">forcedError</span>']);

    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield", FH_NOT_EMPTY);

        $this->assertEmpty($form->getValue("textfield"));

        $t = $form->catchErrors(false);

        $this->assertEquals('<span id="error_textfield" class="error">You did not enter a correct value for this field!</span>',
                                $t['textfield']);
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, 123);

        $this->assertEmpty($form->getValue("textfield"));

        $this->assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="123" />error_textfield']);
    }

    public function test_new_maxlength(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, null, 123);

        $this->assertEmpty($form->getValue("textfield"));

        $this->assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" maxlength="123" />error_textfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("textfield"));

        $this->assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"  data-old="123"']);
    }

};
