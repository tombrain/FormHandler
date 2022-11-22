<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_DateTextFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield");

        $this->assertEmpty($form->getValue("datetextfield"));

        $this->assertFormFlushContains($form, ['DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="" size="20" />error_datetextfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = "14-04-2020";
        $_POST['datetextfield2'] = "14.04.2020";
        $_POST['datetextfield3'] = "14.04.2020";
        $_POST['datetextfield4'] = "14.04.2020";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->dateTextField("Datetextfield", "datetextfield");
        $form->dateTextField("Datetextfield2", "datetextfield2");
        $form->dateTextField("Datetextfield3", "datetextfield3", null, "d.m.Y");
        $form->dateTextField("Datetextfield4", "datetextfield4", null, null, true);

        $this->assertEquals("14-04-2020", $form->getValue("datetextfield"));
        $this->assertEquals("14.04.2020", $form->getValue("datetextfield2"));
        $this->assertEquals("14.04.2020", $form->getValue("datetextfield3"));
        $this->assertEquals("14-04-2020", $form->getValue("datetextfield4"));  // already parsed into correct presentation!

        $this->assertEquals([2020, 4, 14], $form->getAsArray("datetextfield"));
        $this->expectError();
        $this->expectExceptionMessage("Value is not a valid date [14.04.2020]");
        $form->getAsArray("datetextfield2");
        $this->assertEquals([2020, 4, 14], $form->getAsArray("datetextfield3"));
        $this->assertEquals([2020, 4, 14], $form->getAsArray("datetextfield4"));

        $e = $form->catchErrors();

        $this->assertEquals(1, sizeof($e));
        $this->assertEquals('<span id="error_datetextfield2" class="error">You did not enter a correct value for this field!</span>', $e['datetextfield2']);
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = "14-04-2020";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield");

        $this->assertEquals("14-04-2020", $form->getValue("datetextfield"));

        $form->setError("datetextfield", "forcedError");

        $this->assertFormFlushContains($form, ['DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="14-04-2020" size="20" class="error" />error_datetextfield',
                                                '<span id="error_datetextfield" class="error">forcedError</span>']);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield", FH_NOT_EMPTY);

        $this->assertEmpty($form->getValue("datetextfield"));

        $t = $form->catchErrors(false);

        $this->assertEquals('<span id="error_datetextfield" class="error">You did not enter a correct value for this field!</span>',
                                $t['datetextfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield", null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("datetextfield"));

        $this->assertFormFlushContains($form, ['DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="" size="20"  data-old="123"']);
    }

    public function dataTestGetAsArray() : array
    {
        // The default display of the date fields useage:
        // d = day (2 digits with leading zeros)
        // D = day
        // m = month (2 digits with leading zeros)
        // M = month
        // y = year (two digits)
        // Y = year (four digits)
        return [
            [ ["mask" => 'd-m-Y', "value" => "",           "result" => ['day' => "",   'month' => "",   'year' => ""]] ],
            [ ["mask" => 'd-m-Y', "value" => "31-03-2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D-M-Y', "value" => "1-3-2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'Y-m-d', "value" => "2020-03-31", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'Y-M-D', "value" => "2020-3-1",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'd.m.Y', "value" => "31.03.2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D.M.Y', "value" => "1.3.2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'Y/m/d', "value" => "2020/03/31", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'Y/M/D', "value" => "2020/3/1",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'd/m/Y', "value" => "31/03/2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D/M/Y', "value" => "1/3/2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'd-m-y', "value" => "31-03-20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D-M-y', "value" => "1-3-20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'd.m.y', "value" => "31.03.20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D.M.y', "value" => "1.3.20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
            [ ["mask" => 'd/m/y', "value" => "31/03/20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]] ],
            [ ["mask" => 'D/M/y', "value" => "1/3/20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]] ],
        ];
    }
    /**
     * @dataProvider dataTestGetAsArray
     */
    public function testGetAsArray($dataTestGetAsArray) : void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = $dataTestGetAsArray['value'];

        $form = new FormHandler();

        $form->dateTextField("DateTextField", "datetextfield", null, $dataTestGetAsArray['mask']);

        $this->assertTrue($form->isPosted());

        list( $year, $month, $day ) = $form->getAsArray( 'datetextfield' );

        $this->assertEquals($dataTestGetAsArray['result']['year'], $year);
        $this->assertEquals($dataTestGetAsArray['result']['month'], $month);
        $this->assertEquals($dataTestGetAsArray['result']['day'], $day);
    }

    /**
     * @dataProvider dataTestGetAsArray
     */
    public function testParseOtherRepresentaions($dataTestGetAsArray) : void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = $dataTestGetAsArray['value'];
        $_POST['datetextfield2'] = $dataTestGetAsArray['value'];

        $form = new FormHandler();

        $form->dateTextField("DateTextField", "datetextfield", null, null, false);
        $form->dateTextField("DateTextField2", "datetextfield2", null, null, true);

        $this->assertTrue($form->isPosted());

        if ($dataTestGetAsArray['mask'] != FH_DATETEXTFIELD_DEFAULT_DISPLAY)
        {
            $this->expectError();
            $this->expectErrorMessage("Value is not a valid date [" . $dataTestGetAsArray['value'] . "]");
        }
        list( $year, $month, $day ) = $form->getAsArray( 'datetextfield' );

        $this->assertEquals($dataTestGetAsArray['result']['year'], $year);
        $this->assertEquals($dataTestGetAsArray['result']['month'], $month);
        $this->assertEquals($dataTestGetAsArray['result']['day'], $day);

        list( $year2, $month2, $day2 ) = $form->getAsArray( 'datetextfield2' );

        $this->assertEquals($dataTestGetAsArray['result']['year'], $year2);
        $this->assertEquals($dataTestGetAsArray['result']['month'], $month2);
        $this->assertEquals($dataTestGetAsArray['result']['day'], $day2);
    }
};
