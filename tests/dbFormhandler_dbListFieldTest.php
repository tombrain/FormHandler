<?php declare(strict_types=1);

require_once 'helper/dbFormhandlerTestCase.php';


final class dbFormhandler_dbListFieldTest extends dbFormhandlerTestCase
{
    public function test_new(): void
    {
        $this->createMocksForTable();

        $form = new dbFormHandler();

        $this->assertTrue($form->insert);
        $this->assertFalse($form->edit);

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->assertEmpty($form->getValue("textNullable"));
        $this->assertEmpty($form->getValue("textNotNullable"));

        $this->assertFormFlushContains($form, ['input type="text" name="textNullable" id="textNullable" value=""',
                                                 'input type="text" name="textNotNullable" id="textNotNullable" value=""']);
    }

    public function test_edit_noDataset(): void
    {
        $this->createMocksForTable();

        $_GET['id'] = "123";

        $form = new dbFormHandler();

        $this->assertFalse($form->insert);
        $this->assertTrue($form->edit);

        $this->getDatabaseMock()
                ->expects($this->exactly(1))
                ->query($this->matches("SELECT * FROM test WHERE id = '123'"))
                ->willReturnResultSet([
                ]);

        $this->expectError();
        $this->expectErrorMessage("Try to edit a none existing record!");

        $this->setConnectedTable($form, "test");
    }

    public function test_edit(): void
    {
        $this->createMocksForTable();

        $_GET['id'] = "100";

        $form = new dbFormHandler();

        $this->assertFalse($form->insert);
        $this->assertTrue($form->edit);

        $this->getDatabaseMock()
                ->expects($this->exactly(1))
                ->query($this->matches("SELECT * FROM test WHERE id = '100'"))
                ->willReturnResultSet([
                    ['id' => '100', 'textNullable' => 'text1', 'textNotNullable' => 'text2'],
                ]);

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->assertEquals("text1", $form->getValue("textNullable"));
        $this->assertEquals("text2", $form->getValue("textNotNullable"));

        $this->assertFormFlushContains($form, ['input type="text" name="textNullable" id="textNullable" value="text1"',
                                                'input type="text" name="textNotNullable" id="textNotNullable" value="text2"']);
    }

    public function test_insert_noValues(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";

        $form = new dbFormHandler();

        $this->assertTrue($form->insert);
        $this->assertFalse($form->edit);

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->getDatabaseMock()
                ->expects($this->once())
                ->query("INSERT INTO test (textNullable, textNotNullable) VALUES (NULL, '');")
                ->willSetAffectedRows(1)
                ->willSetLastInsertId(4712);

        $this->setCallbackOnSaved($form);
        
        $r = $form->flush(true);

        $this->assertEmpty($r);
        $this->assertSavedId(4712);
        $this->assertSavedValueEmtpy('textNullable');
        $this->assertSavedValueEmtpy('textNotNullable');
    }
 
    public function test_insert(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "thetext";
        $_POST['textNotNullable'] = "anothertext";

        $form = new dbFormHandler();

        $this->assertTrue($form->insert);
        $this->assertFalse($form->edit);

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->getDatabaseMock()
                ->expects($this->once())
                ->query("INSERT INTO test (textNullable, textNotNullable) VALUES ('thetext', 'anothertext');")
                ->willSetAffectedRows(1)
                ->willSetLastInsertId(4713);

        $this->setCallbackOnSaved($form);
        
        $r = $form->flush(true);

        $this->assertEmpty($r);
        $this->assertSavedId(4713);
        $this->assertSavedValue('thetext', 'textNullable');
        $this->assertSavedValue('anothertext', 'textNotNullable');
    }
    
    public function test_update_noValues(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4714";

        $form = new dbFormHandler();

        $this->assertFalse($form->insert);
        $this->assertTrue($form->edit);

        $this->getDatabaseMock()
                ->expects($this->exactly(1))
                ->query($this->matches("SELECT * FROM test WHERE id = '4714'"))
                ->willReturnResultSet([
                    ['id' => '4714', 'textNullable' => 'text1', 'textNotNullable' => 'text2'],
                ]);

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->getDatabaseMock()
                ->expects($this->once())
                ->query("UPDATE test SET textNullable = NULL, textNotNullable = '' WHERE id = '4714'");

        $this->setCallbackOnSaved($form);
        
        $r = $form->flush(true);

        $this->assertEmpty($r);
        $this->assertSavedId(4714);
        $this->assertSavedValueEmtpy('textNullable');
        $this->assertSavedValueEmtpy('textNotNullable');
    }
 
    public function test_update(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4715";
        $_POST['textNullable'] = "thetext";
        $_POST['textNotNullable'] = "anothertext";

        $form = new dbFormHandler();

        $this->assertFalse($form->insert);
        $this->assertTrue($form->edit);

        $this->getDatabaseMock()
                ->expects($this->exactly(1))
                ->query($this->matches("SELECT * FROM test WHERE id = '4715'"))
                ->willReturnResultSet([
                    ['id' => '4715', 'textNullable' => 'text1', 'textNotNullable' => 'text2'],
                ]);
        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->getDatabaseMock()
                ->expects($this->once())
                ->query("UPDATE test SET textNullable = 'thetext', textNotNullable = 'anothertext' WHERE id = '4715'");

        $this->setCallbackOnSaved($form);
        
        $r = $form->flush(true);

        $this->assertEmpty($r);
        $this->assertSavedId(4715);
        $this->assertSavedValue('thetext', 'textNullable');
        $this->assertSavedValue('anothertext', 'textNotNullable');
    }
};
