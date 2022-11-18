<?php declare(strict_types=1);

require_once 'helper/dbFormhandlerTestCase.php';


final class dbFormhandler_BaseTest extends dbFormhandlerTestCase
{
    public function testConstructor(): void
    {
        $form = new dbFormHandler();

        $this->assertInstanceOf("dbFormHandler", $form);

        $_sql = $this->getPrivateProperty($form, '_sql');
        $this->assertTrue(is_array($_sql));
        $this->assertEquals(0, sizeof($_sql));

        $_dbData = $this->getPrivateProperty($form, '_dbData');
        $this->assertTrue(is_array($_dbData));
        $this->assertEquals(0, sizeof($_dbData));

        $_dontSave = $this->getPrivateProperty($form, '_dontSave');
        $this->assertTrue(is_array($_dontSave));
        $this->assertEquals(0, sizeof($_dontSave));

        $_id = $this->getPrivateProperty($form, '_id');
        $this->assertTrue(is_array($_id));
        $this->assertEquals(0, sizeof($_id));

        $dieOnQuery = $this->getPrivateProperty($form, 'dieOnQuery');
        $this->assertFalse($dieOnQuery);

        $_editName = $this->getPrivateProperty($form, '_editName');
        $this->assertEquals("id", $_editName);

        $this->assertTrue($form->insert);
        $this->assertFalse($form->edit);
    }

    public function test_SetConnectionResource(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $_db = $this->getPrivateProperty($form, '_db');
        $this->assertInstanceOf("YadalDibi", $_db);

        $this->assertTrue($_db->isConnected());
        $this->assertTrue($_db->_quoteNumbers);

        $_table = $this->getPrivateProperty($form, '_table');
        $this->assertEquals("test", $_table);
    }
};
