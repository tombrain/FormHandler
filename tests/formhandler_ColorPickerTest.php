<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_ColorPickerTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        $this->assertEmpty($form->getValue("colorpicker"));

        $this->assertFormFlushContains($form, ['FHTML/js/jscolor/jscolor.js',
                                                'Colorpicker:<input type="text" name="colorpicker" id="colorpicker" value="" size="20"  class="color" />error_colorpicker']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['colorpicker'] = "FFCC00";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        $this->assertEquals("FFCC00", $form->getValue("colorpicker"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['colorpicker'] = "FFCC00";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        $this->assertEquals("FFCC00", $form->getValue("colorpicker"));

        $form->setError("colorpicker", "forcedError");

        $this->assertFormFlushContains($form, ['Colorpicker:<input type="text" name="colorpicker" id="colorpicker" value="FFCC00" size="20"  class="error color" />error_colorpicker',
                                                '<span id="error_colorpicker" class="error">forcedError</span>']);
    }
};
