<?php

declare(strict_types=1);

final class dbFormhandler_RecordIdTest extends dbFormhandlerTestCase
{
    /** @var array<string, mixed> what onSaved got and getNewId() returned there */
    private array $_onSaved = array();

    public function callback_onSaved_newId(mixed $id, array $data, dbFormHandler $form): bool
    {
        $this->_onSaved = ['id' => $id, 'newId' => $form->getNewId()];
        return true;
    }

    public function test_insert(): void
    {
        $this->insertRecord(['id' => 7, 'textNullable' => 'old']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "new";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $form->textField("TextNullable", "textNullable");
        $form->onSaved(array($this, "callback_onSaved_newId"));

        static::assertNull($form->getNewId());
        static::assertNull($form->getRecordId());

        static::assertFlush($form);

        static::assertEquals(8, $form->getNewId());
        static::assertEquals(8, $form->getRecordId());

        // onSaved gets the same id, and getNewId() knows it already there
        static::assertEquals(8, $this->_onSaved['id']);
        static::assertSame($this->_onSaved['id'], $this->_onSaved['newId']);
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 5, 'textNullable' => 'old']);

        $_GET['id'] = "5";
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "new";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $form->textField("TextNullable", "textNullable");

        static::assertNull($form->getNewId());
        static::assertEquals(5, $form->getRecordId());

        static::assertFlush($form);

        static::assertNull($form->getNewId());
        static::assertEquals(5, $form->getRecordId());
        $this->assertRecord(5, ['textNullable' => 'new']);
    }

    public function test_insert_failed(): void
    {
        // no value for the NOT NULL column "required": the INSERT fails
        $this->getConnection()->executeStatement(
            "CREATE TABLE strict (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, required VARCHAR(10) NOT NULL, optional VARCHAR(10) NULL)"
        );

        $_POST['FormHandler_submit'] = "1";
        $_POST['optional'] = "text";

        $errors = array();
        set_error_handler(function (int $level, string $message) use (&$errors)
        {
            $errors[] = $message;
            return true;
        });

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "strict");
        $form->textField("Optional", "optional");

        $r = $form->flush(true);
        restore_error_handler();

        static::assertCount(1, $errors);
        static::assertStringStartsWith('Error, query failed!', $errors[0]);
        static::assertNotEmpty($r);
        static::assertNull($form->getNewId());
        static::assertNull($form->getRecordId());
    }
}
