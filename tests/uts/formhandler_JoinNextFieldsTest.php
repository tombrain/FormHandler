<?php

declare(strict_types=1);

final class formhandler_JoinNextFieldsTest extends FormhandlerTestCase
{
    private const FIELD_A = '<input type="text" name="a" id="a" value="" size="20" />';
    private const FIELD_B = '<input type="text" name="b" id="b" value="" size="20" />';
    private const FIELD_C = '<input type="text" name="c" id="c" value="" size="20" />';
    private const FIELD_D = '<input type="text" name="d" id="d" value="" size="20" />';

    public function test_twoFields(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->textField("C", "c");

        static::assertFormFlushContains($form, [
            'A:' . self::FIELD_A . ' ' . self::FIELD_B . 'error_a',
            'C:' . self::FIELD_C . 'error_c'
        ]);
    }

    public function test_threeFields_separator(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(3, ' / ');
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->textField("C", "c");
        $form->textField("D", "d");

        $t = static::assertFormFlushContains($form, [
            'A:' . self::FIELD_A . ' / ' . self::FIELD_B . ' / ' . self::FIELD_C . 'error_a',
            'D:' . self::FIELD_D . 'error_d'
        ]);
        static::assertStringNotContainsString('B:', $t);
        static::assertStringNotContainsString('C:', $t);
    }

    public function test_percentInSeparator(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(2, ' %title% ');
        $form->textField("A", "a");
        $form->textField("B", "b");

        static::assertFormFlushContains($form, 'A:' . self::FIELD_A . ' %title% ' . self::FIELD_B . 'error_a');
    }

    public function test_errorOfTheSecondField(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['a'] = "";
        $_POST['b'] = "";

        $form = new FormHandler();

        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->textField("B", "b", FH_NOT_EMPTY);

        $t = static::assertFormFlushContains($form, 'error_a<span id="error_b" class="error">');
        static::assertStringContainsString('<input type="text" name="b" id="b" value="" size="20" class="error" />', $t);
    }

    public function test_errorsOfBothFields(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['a'] = "";
        $_POST['b'] = "";

        $form = new FormHandler();

        $form->joinNextFields(2);
        $form->textField("A", "a", FH_NOT_EMPTY);
        $form->textField("B", "b", FH_NOT_EMPTY);

        static::assertFormFlushContains($form, [
            'error_a<span id="error_a" class="error">',
            '</span> <span id="error_b" class="error">'
        ]);
    }

    public function test_viewMode(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(2, ' - ');
        $form->textField("A", "a");
        $form->setValue("a", "valueA");
        $form->textField("B", "b");
        $form->setValue("b", "valueB");
        $form->enableViewMode();

        static::assertFormFlushContains($form, 'A:valueA - valueBerror_a');
    }

    public function test_hiddenFieldIsNotCounted(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->hiddenField("h", "hidden");
        $form->textField("B", "b");

        static::assertFormFlushContains($form, 'A:' . self::FIELD_A . ' ' . self::FIELD_B . 'error_a');
    }

    public function test_htmlEndsTheRow(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(3);
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->addHTML("HTML");
        $form->textField("C", "c");

        static::assertFormFlushContains($form, [
            'A:' . self::FIELD_A . ' ' . self::FIELD_B . 'error_aHTML',
            'C:' . self::FIELD_C . 'error_c'
        ]);
    }

    public function test_lastRowWithFewerFields(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(3);
        $form->textField("A", "a");
        $form->textField("B", "b");

        static::assertFormFlushContains($form, 'A:' . self::FIELD_A . ' ' . self::FIELD_B . 'error_a');
    }

    public function test_maskRepeated(): void
    {
        $form = new FormHandler();

        $form->setMask("[%title%|%field%]", true);
        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->textField("C", "c");
        $form->joinNextFields(2);
        $form->textField("D", "d");
        $form->textField("E", "e");

        static::assertFormFlushContains($form, [
            '[A|' . self::FIELD_A . ' ' . self::FIELD_B . ']',
            '[C|' . self::FIELD_C . ']',
            '[D|' . self::FIELD_D . ' <input type="text" name="e" id="e" value="" size="20" />]'
        ]);
    }

    public function test_maskOnce(): void
    {
        $form = new FormHandler();

        // the row uses the mask once, the next field the default mask
        $form->setMask("[%title%|%field%]", false);
        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->textField("C", "c");

        static::assertFormFlushContains($form, [
            '[A|' . self::FIELD_A . ' ' . self::FIELD_B . ']',
            'C:' . self::FIELD_C . 'error_c'
        ]);
    }

    public function test_maskAfterTheRow(): void
    {
        $form = new FormHandler();

        $form->joinNextFields(2);
        $form->textField("A", "a");
        $form->textField("B", "b");
        $form->setMask("[%title%|%field%]", false);
        $form->textField("C", "c");
        $form->textField("D", "d");

        static::assertFormFlushContains($form, [
            'A:' . self::FIELD_A . ' ' . self::FIELD_B . 'error_a',
            '[C|' . self::FIELD_C . ']',
            'D:' . self::FIELD_D . 'error_d'
        ]);
    }

    public function test_newPage(): void
    {
        $form = new FormHandler();

        $form->textField("A", "a");
        $form->newPage();
        $form->joinNextFields(2);
        $form->textField("B", "b");
        $form->textField("C", "c");

        // page 1: the row of page 2 does not join a field of page 1
        $t = static::assertFormFlushContains($form, 'A:' . self::FIELD_A . 'error_a');
        static::assertStringNotContainsString(self::FIELD_A . ' ', $t);
    }

    public function test_tooFewFields(): void
    {
        $form = new FormHandler();

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('joinNextFields() needs at least 2 fields!');

        $form->joinNextFields(1);
    }
}
