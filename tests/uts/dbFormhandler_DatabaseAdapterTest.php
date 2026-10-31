<?php

declare(strict_types=1);

final class dbFormhandler_DatabaseAdapterTest extends dbFormhandlerTestCase
{
    public function test_noDatabase(): void
    {
        $form = new dbFormHandler();

        static::assertNull($form->getDatabaseAdapter());
    }

    public function test_beforeSaving(): void
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        static::assertInstanceOf(DbalAdapter::class, $form->getDatabaseAdapter());
    }

    public function test_setDatabaseAdapter(): void
    {
        $adapter = new DbalAdapter($this->getConnection());

        $form = new dbFormHandler();
        $form->setDatabaseAdapter($adapter);

        static::assertSame($adapter, $form->getDatabaseAdapter());
    }

    public function test_afterSaving(): void
    {
        $this->insertRecord(['id' => 5, 'textNullable' => 'old']);

        $_GET['id'] = "5";
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "new";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $form->textField("TextNullable", "textNullable");

        static::assertFlush($form);

        // e.g. to compare the old and the new values after saving
        $db = $form->getDatabaseAdapter();
        static::assertInstanceOf(DbalAdapter::class, $db);
        $sql = $db->query("SELECT textNullable FROM test WHERE id = 5");
        static::assertEquals(['textNullable' => 'new'], $db->getRecord($sql));
    }

    public function test_afterInsert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "new";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $form->textField("TextNullable", "textNullable");

        static::assertFlush($form);

        static::assertInstanceOf(DbalAdapter::class, $form->getDatabaseAdapter());
    }
}
