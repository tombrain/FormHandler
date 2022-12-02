<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_LookAndFeel_setMaxLengthTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $form = new FormHandler();

        $form->textArea("Textarea", "textarea");
        $form->setMaxLength("textarea", 123);

        $this->assertFormFlushContains($form, ['FHTML/js/maxlength.js',
                                                'Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7"',
                                                'onkeyup="displayLimit(\'FormHandler\', \'textarea\', 123, true, \'&lt;b&gt;%d&lt;/b&gt; characters remaining on your input limit\');">']);
    }

    public function test_noDisplayMessage(): void
    {
        $form = new FormHandler();

        $form->textArea("Textarea", "textarea");
        $form->setMaxLength("textarea", 123, false);

        $this->assertFormFlushContains($form, ['FHTML/js/maxlength.js',
                                                'Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7"',
                                                'onkeyup="displayLimit(\'FormHandler\', \'textarea\', 123, false, \'&lt;b&gt;%d&lt;/b&gt; characters remaining on your input limit\');">']);
    }

    public function test_noField(): void
    {
        $form = new FormHandler();

        $this->expectError();
        $this->expectErrorMessage('You have to declare the textarea first! The field "textarea" does not exists in the form!');
        $form->setMaxLength("textarea", 123);

    }

    public function test_wrongField(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $this->expectError();
        $this->expectErrorMessage('You have to declare the textarea first! The field "textarea" does not exists in the form!');
        $form->setMaxLength("textarea", 123);

    }

};
