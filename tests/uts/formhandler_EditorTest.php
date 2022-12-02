<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_EditorTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->editor("Editor", "editor");

        $this->assertEmpty($form->getValue("editor"));

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
                                                '"language":"en","width":720,"height":400,"skin":"moono"}']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = "textvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->editor("Editor", "editor");

        $this->assertEquals("textvalue", $form->getValue("editor"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = "textvalue";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->editor("Editor", "editor");

        $this->assertEquals("textvalue", $form->getValue("editor"));

        $form->setError("editor", "forcedError");

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea class="error" name="editor" id="editor" cols="40" rows="7">textvalue</textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"']);
    }

    public function test_path(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, "thisisthepath");

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
                                                'thisisthepath","filebrowserUploadUrl"',
                                                'thisisthepath","language":"en","width":720,"height":400,"skin":"moono"}']);
    }
    
    public function test_toolbar(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, "thetoolbar");

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"thetoolbar"',
                                                '"language":"en","width":720,"height":400,"skin":"moono"}']);
    }
    
    public function test_skin(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, "theskin");

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
                                                '"language":"en","width":720,"height":400,"skin":"theskin"}']);
    }
    
    public function test_size(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, null, 123, 456);

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
                                                '"language":"en","width":123,"height":456,"skin":"moono"}']);
    }
    
    public function test_config(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, null, null, null, ["theconfig" => "vals"]);

        $this->assertFormFlushContains($form, ['FHTML/ckeditor/ckeditor.js',
                                                'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
                                                'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
                                                '"language":"en","width":720,"height":400,"skin":"moono","theconfig":"vals"}']);
    }
};
