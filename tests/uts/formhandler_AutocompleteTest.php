<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_AutocompleteTest extends FormhandlerTestCase
{
    public function test_show(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $aOptions = ["first", "second", "third"];

        $form->setAutoComplete("textfield", $aOptions);

        $this->assertFormFlushContains($form, ['FHTML/js/autocomplete.js',
                                                'textfield_values = ["first", "second", "third"];',
                                                'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   onkeypress=\'return FH_autocomplete(this, event, textfield_values);\'  />error_textfield']);
    }

    public function test_no_textfield(): void
    {
        $form = new FormHandler();

        $aOptions = ["first", "second", "third"];

        $this->expectError();
        $this->expectErrorMessage('You have to declare the textfield first! The field "textfield" does not exists in the form!');

        $form->setAutoComplete("textfield", $aOptions);
    }

    public function test_no_optionarray(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $this->expectError();
        $this->expectErrorMessage('You have to give an array as options!');

        $form->setAutoComplete("textfield", "nooptions");
    }

    public function test_after_show(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $aOptions = ["first", "second", "third"];

        $form->setAutoCompleteAfter("textfield", "@", $aOptions);

        $this->assertFormFlushContains($form, ['FHTML/js/autocomplete.js',
                                                'textfield_values = ["first", "second", "third"];',
                                                'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   onkeypress=\'return autocompleteafter(this, event,"@", textfield_values);\'  />error_textfield']);
    }

    public function test_after_no_textfield(): void
    {
        $form = new FormHandler();

        $aOptions = ["first", "second", "third"];

        $this->expectError();
        $this->expectErrorMessage('You have to declare the textfield first! The field "textfield" does not exists in the form!');

        $form->setAutoCompleteAfter("textfield", "@", $aOptions);
    }

    public function test_after_no_optionarray(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $this->expectError();
        $this->expectErrorMessage('You have to give an array as options!');

        $form->setAutoCompleteAfter("textfield", "@", "nooptions");
    }
};
