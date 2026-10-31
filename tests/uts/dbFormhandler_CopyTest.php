<?php

declare(strict_types=1);

/**
 * Copies a record with setCopyOf(): the form loads the record like an edit form and
 * saves it as a new record.
 */
final class dbFormhandler_CopyTest extends dbFormhandlerTestCase
{
    private function createForm(mixed $copyOf = "100"): dbFormHandler
    {
        $form = new dbFormHandler();
        $form->setCopyOf($copyOf);
        $this->setConnectedTable($form, "test");

        return $form;
    }

    public function test_copy_showsTheRecord(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $form = $this->createForm();
        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        static::assertTrue($form->isCopy());
        static::assertEquals(100, $form->getCopyOfId());
        static::assertNull($form->getEditId());
        static::assertNull($form->getRecordId());

        static::assertFormFlushContains($form, [
            'input type="text" name="textNullable" id="textNullable" value="text1"',
            'input type="text" name="textNotNullable" id="textNotNullable" value="text2"'
        ]);
    }

    public function test_copy_savesANewRecord(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "copy1";
        $_POST['textNotNullable'] = "copy2";
        // the URL of a copy usually has the id too (?id=100&funktion=copy)
        $_GET['id'] = "100";

        $form = $this->createForm();
        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);
        $form->flush(true);

        $this->assertSavedId(101);
        $this->assertRecordCount(2);
        $this->assertRecord(100, ['textNullable' => 'text1', 'textNotNullable' => 'text2']);
        $this->assertRecord(101, ['textNullable' => 'copy1', 'textNotNullable' => 'copy2']);

        static::assertTrue($form->isCopy());
        static::assertEquals(100, $form->getCopyOfId());
        static::assertNull($form->getEditId());
        static::assertEquals(101, $form->getNewId());
        static::assertEquals(101, $form->getRecordId());
    }

    public function test_copy_primaryKeyIsNotCopied(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "copy1";

        $form = $this->createForm();
        $form->textField("TextNullable", "textNullable");

        // the values of the record without the primary key
        static::assertArrayNotHasKey('id', $form->_dbData);
        static::assertEquals('text1', $form->getDBValue('textNullable'));

        static::assertFlush($form);

        $this->assertRecordCount(2);
        $this->assertRecord(101, ['textNullable' => 'copy1']);
    }

    public function test_copy_postedWithAnError(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "copy1";
        $_POST['textNotNullable'] = "";

        $form = $this->createForm();
        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable", FH_NOT_EMPTY);

        static::assertFormFlushContains($form, [
            'input type="text" name="textNullable" id="textNullable" value="copy1"',
            'error_textNotNullable<span id="error_textNotNullable" class="error">'
        ]);

        // still a copy, nothing saved
        static::assertTrue($form->isCopy());
        static::assertNull($form->getEditId());
        static::assertNull($form->getRecordId());
        $this->assertRecordCount(1);
        $this->assertRecord(100, ['textNullable' => 'text1', 'textNotNullable' => 'text2']);
    }

    public function test_copy_uniqueField(): void
    {
        $this->getConnection()->executeStatement('CREATE UNIQUE INDEX test_text ON test (textNullable)');
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "text1";

        $form = $this->createForm();
        $form->textField("TextNullable", "textNullable");

        // the value of the original is used already: before and in flush()
        static::assertFalse($form->isCorrect());
        $form->flush(true);
        $this->assertRecordCount(1);
    }

    public function test_copy_noneExistingRecord(): void
    {
        $form = new dbFormHandler();
        $form->setCopyOf(100);

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('Try to copy a none existing record!');

        $this->setConnectedTable($form, "test");
    }

    public function test_copy_afterTheConnection(): void
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('setCopyOf() has to be called before dbInfo(), setConnectionResource() and setDatabaseAdapter()!');

        $form->setCopyOf(100);
    }

    public function test_copy_noId(): void
    {
        $form = new dbFormHandler();

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('setCopyOf() needs the id of the record to copy!');

        $form->setCopyOf('');
    }

    public function test_noCopy(): void
    {
        $_GET['id'] = "100";

        $form = new dbFormHandler();

        static::assertFalse($form->isCopy());
        static::assertNull($form->getCopyOfId());
        static::assertEquals(100, $form->getEditId());
    }
}
