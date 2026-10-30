<?php

declare(strict_types=1);

/**
 * PHPUnit has no real upload: copy the test file instead of moving it
 */
class uploadIdTest_UploadField extends UploadField
{
    protected function _isUploadedFile($sFile)
    {
        return is_file($sFile);
    }

    protected function _moveUploadedFile($sFrom, $sTo)
    {
        return copy($sFrom, $sTo);
    }
}

/**
 * A form whose uploadField() creates an uploadIdTest_UploadField, and which copies a
 * record like the projects do (?id=...&funktion=copy, see dbFormhandler_CopyTest)
 */
class uploadIdTest_dbFormHandler extends dbFormHandler
{
    private bool $_isCopy = false;

    public function __construct($name = null, $action = null, $extra = null)
    {
        parent::__construct($name, $action, $extra);

        $this->_isCopy = isset($_GET['funktion']) && $_GET['funktion'] == "copy";
    }

    public function uploadField($title, $name, $config = array(), $validator = null, $extra = null, $alertOverwrite = null)
    {
        $fld = new uploadIdTest_UploadField($this, $name, $config);

        if (!empty($validator))        $fld->setValidator($validator);
        if (!empty($extra))            $fld->setExtra($extra);
        if (!is_null($alertOverwrite)) $fld->setAlertOverwrite($alertOverwrite);

        $this->_registerField($name, $fld, $title);
        $this->_upload[] = $name;
    }

    public function flush($return = false)
    {
        if ($this->_isCopy)
        {
            $this->_id = array();
            $this->insert = true;
            $this->edit = false;
        }

        return parent::flush($return);
    }
}

final class dbFormhandler_UploadIdTest extends dbFormhandlerTestCase
{
    private string $_tempPath = '';

    /** @var array<int, mixed> the values given to onCorrect */
    private array $_onCorrectData = array();

    protected function setUp(): void
    {
        parent::setUp();

        $this->getConnection()->executeStatement(
            "CREATE TABLE spieler (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(50) NOT NULL,
                bild VARCHAR(255) NULL
            )"
        );

        $this->_tempPath = sys_get_temp_dir() . "/uploadidtest";
        if (is_dir($this->_tempPath))
        {
            array_map('unlink', glob($this->_tempPath . '/*'));
        }
        else
        {
            mkdir($this->_tempPath);
        }

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'bild' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
    }

    public function callback_onCorrect(array $data, FormHandler $form): bool
    {
        $this->_onCorrectData = $data;
        return true;
    }

    /**
     * a form for the table "spieler" with the uploadfield "bild" (posted with test.jpg)
     */
    private function createForm(array $config = array()): uploadIdTest_dbFormHandler
    {
        $form = new uploadIdTest_dbFormHandler();
        $form->setConnectionResource($this->getConnection(), 'spieler');

        $form->textField("Name", "name");
        $form->uploadField("Bild", "bild", array_merge(array(
            "type"   => "jpg",
            "name"   => "spieler_{id}",
            "path"   => $this->_tempPath,
            "exists" => "overwrite"
        ), $config));

        return $form;
    }

    private function assertFiles(array $expected): void
    {
        $files = array_map('basename', glob($this->_tempPath . '/*'));
        sort($files);

        static::assertSame($expected, $files);
    }

    private function getBild(int $id): mixed
    {
        return $this->getConnection()->fetchOne('SELECT bild FROM spieler WHERE id = ?', [$id]);
    }

    public function test_insert(): void
    {
        $this->getConnection()->insert('spieler', ['id' => 7, 'name' => 'old']);
        $_POST['name'] = "new";

        $form = $this->createForm();
        $form->onCorrect(array($this, "callback_onCorrect"));
        $this->setCallbackOnSaved($form);

        // before the INSERT: the file is uploaded, its name is not known yet
        static::assertTrue($form->isUploaded("bild"));
        static::assertEquals($_FILES['bild'], $form->getFileInfo("bild"));
        static::assertSame('', $form->getValue("bild"));

        static::assertFlush($form);

        static::assertSame('', $this->_onCorrectData['bild']);
        $this->assertSavedId(8);
        $this->assertSavedValue('spieler_8.jpg', 'bild');
        static::assertSame('spieler_8.jpg', $this->getBild(8));
        static::assertSame('spieler_8.jpg', $form->getValue("bild"));
        $this->assertFiles(['spieler_8.jpg']);
    }

    public function test_insert_noFile(): void
    {
        $_POST['name'] = "new";
        $_FILES['bild'] = ['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'size' => 0, 'tmp_name' => '', 'type' => ''];

        $form = $this->createForm();
        $this->setCallbackOnSaved($form);

        static::assertFlush($form);

        $this->assertSavedId(1);
        $this->assertSavedValueEmtpy('bild');
        static::assertNull($this->getBild(1));
        $this->assertFiles([]);
    }

    public function test_insert_rename(): void
    {
        static::assertTrue(copy(dirname(__FILE__) . "/test.jpg", "{$this->_tempPath}/spieler_1.jpg"));
        $_POST['name'] = "new";

        $form = $this->createForm(['exists' => 'rename']);

        static::assertFlush($form);

        static::assertSame('spieler_1(1).jpg', $this->getBild(1));
        $this->assertFiles(['spieler_1(1).jpg', 'spieler_1.jpg']);
    }

    public function test_insert_fieldNotInTable(): void
    {
        $_POST['name'] = "new";
        $_FILES['foto'] = $_FILES['bild'];

        $form = $this->createForm();
        $form->uploadField("Foto", "foto", [
            "type" => "jpg",
            "name" => "foto_{id}",
            "path" => $this->_tempPath
        ]);
        $this->setCallbackOnSaved($form);

        static::assertFlush($form);

        $this->assertSavedValue('foto_1.jpg', 'foto');
        $this->assertFiles(['foto_1.jpg', 'spieler_1.jpg']);
    }

    public function test_insert_failed(): void
    {
        // no name: the NOT NULL column makes the INSERT fail

        $errors = array();
        set_error_handler(function (int $level, string $message) use (&$errors)
        {
            $errors[] = $message;
            return true;
        });

        $form = new uploadIdTest_dbFormHandler();
        $form->setConnectionResource($this->getConnection(), 'spieler');
        $form->uploadField("Bild", "bild", [
            "type" => "jpg",
            "name" => "spieler_{id}",
            "path" => $this->_tempPath
        ]);

        $r = $form->flush(true);
        restore_error_handler();

        static::assertCount(1, $errors);
        static::assertStringStartsWith('Error, query failed!', $errors[0]);
        static::assertNotEmpty($r);
        static::assertEquals(0, $this->getConnection()->fetchOne('SELECT COUNT(*) FROM spieler'));
        $this->assertFiles([]);
    }

    public function test_edit(): void
    {
        $this->getConnection()->insert('spieler', ['id' => 100, 'name' => 'old', 'bild' => 'old.jpg']);
        $_GET['id'] = "100";
        $_POST['name'] = "new";

        $form = $this->createForm();
        $this->setCallbackOnSaved($form);

        static::assertFlush($form);

        $this->assertSavedId(100);
        $this->assertSavedValue('spieler_100.jpg', 'bild');
        static::assertSame('spieler_100.jpg', $this->getBild(100));
        $this->assertFiles(['spieler_100.jpg']);
    }

    public function test_copy(): void
    {
        $this->getConnection()->insert('spieler', ['id' => 100, 'name' => 'old', 'bild' => 'spieler_100.jpg']);
        static::assertTrue(file_put_contents("{$this->_tempPath}/spieler_100.jpg", 'original') > 0);
        $_GET['id'] = "100";
        $_GET['funktion'] = "copy";
        $_POST['name'] = "copy";

        $form = $this->createForm();

        static::assertFlush($form);

        // the copy gets its own file, the one of the original is kept
        static::assertSame('spieler_101.jpg', $this->getBild(101));
        static::assertSame('spieler_100.jpg', $this->getBild(100));
        static::assertSame('original', file_get_contents("{$this->_tempPath}/spieler_100.jpg"));
        $this->assertFiles(['spieler_100.jpg', 'spieler_101.jpg']);
    }

    public function test_invalidId(): void
    {
        $form = $this->createForm();

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('The id "../x" can not be used in the filename of the uploadfield "bild"!');

        $form->getField('bild')->resolveIdPlaceholder('../x');
    }

    public function test_multiplePages(): void
    {
        $_POST['name'] = "new";

        $form = $this->createForm();
        $form->newPage();
        $form->textField("Page 2", "page2");

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('The uploadfield "bild" uses {id} in its filename and has to be on the last page of the form!');

        $form->flush(true);
    }

    public function test_noTable(): void
    {
        $form = new uploadIdTest_dbFormHandler();
        $form->uploadField("Bild", "bild", [
            "type" => "jpg",
            "name" => "spieler_{id}",
            "path" => $this->_tempPath
        ]);

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('The uploadfield "bild" uses {id} in its filename, which needs a database table!');

        $form->flush(true);
    }

    public function test_formHandler(): void
    {
        $form = new FormHandler();

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage('The uploadfield "bild" uses {id} in its filename, which needs a dbFormHandler!');

        $form->uploadField("Bild", "bild", ["name" => "spieler_{id}"]);
    }
}
