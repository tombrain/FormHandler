<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_jsDateTextFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");

        $this->assertEmpty($form->getValue("jsdatetextfield"));

        $this->assertFormFlushContains($form, ['FHTML/js/calendar_popup.js',
                                                'jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="" size="20" />',
                                                '<a href=\'javascript:;\' onclick="if( cal_jsdatetextfield ) cal_jsdatetextfield.select(document.forms[\'FormHandler\'].elements[\'jsdatetextfield\'], \'anchor_jsdatetextfield\', \'dd-MM-yyyy\'); return false;"  name=\'anchor_jsdatetextfield\' id=\'anchor_jsdatetextfield\'>',
                                                'FHTML/images/calendar.gif\' border=\'0\' alt=\'Select Date\' /></a>',
                                                '<span id=\'jsdatetextfield_span\'  style=\'position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\'></span>',
                                                'error_jsdatetextfield',
                                                'if( document.getElementById(\'jsdatetextfield_span\') )',
                                                'var cal_jsdatetextfield = new CalendarPopup(\'jsdatetextfield_span\');',
                                                "cal_jsdatetextfield.setMonthNames('January','February','March','April','May','June','July','August','September','October','November','December');",
                                                "cal_jsdatetextfield.setDayHeaders('S','M','T','W','T','F','S');",
                                                'cal_jsdatetextfield.setWeekStartDay(1);',
                                                "cal_jsdatetextfield.setTodayText('Today');",
                                                'cal_jsdatetextfield.showYearNavigation();',
                                                'cal_jsdatetextfield.showYearNavigationInput();']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['jsdatetextfield'] = "14-04-2020";
        $_POST['jsdatetextfield2'] = "14.04.2020";
        $_POST['jsdatetextfield3'] = "14.04.2020";
        $_POST['jsdatetextfield4'] = "14.04.2020";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");
        $form->jsDateTextField("jsDatejsdatetextfield2", "jsdatetextfield2");
        $form->jsDateTextField("jsDatejsdatetextfield3", "jsdatetextfield3", null, "d.m.Y");
        $form->jsDateTextField("jsDatejsdatetextfield4", "jsdatetextfield4", null, null, true);

        $this->assertEquals("14-04-2020", $form->getValue("jsdatetextfield"));
        $this->assertEquals("14.04.2020", $form->getValue("jsdatetextfield2"));
        $this->assertEquals("14.04.2020", $form->getValue("jsdatetextfield3"));
        $this->assertEquals("14-04-2020", $form->getValue("jsdatetextfield4"));  // already parsed into correct presentation!

        $this->assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield"));
        $this->expectError();
        $this->expectExceptionMessage("Value is not a valid date [14.04.2020]");
        $form->getAsArray("jsdatetextfield2");
        $this->assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield3"));
        $this->assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield4"));

        $e = $form->catchErrors();

        $this->assertEquals(1, sizeof($e));
        $this->assertEquals('<span id="error_jsdatetextfield2" class="error">You did not enter a correct value for this field!</span>', $e['jsdatetextfield2']);
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['jsdatetextfield'] = "14-04-2020";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");

        $this->assertEquals("14-04-2020", $form->getValue("jsdatetextfield"));

        $form->setError("jsdatetextfield", "forcedError");

        $this->assertFormFlushContains($form, ['jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="14-04-2020" size="20" class="error" />',
                                                '<a href=\'javascript:;\' onclick="if( cal_jsdatetextfield ) cal_jsdatetextfield.select(document.forms[\'FormHandler\'].elements[\'jsdatetextfield\'], \'anchor_jsdatetextfield\', \'dd-MM-yyyy\'); return false;"  name=\'anchor_jsdatetextfield\' id=\'anchor_jsdatetextfield\'>',
                                                'FHTML/images/calendar.gif\' border=\'0\' alt=\'Select Date\' class="error" /></a>',
                                                '<span id=\'jsdatetextfield_span\'  style=\'position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\'></span>',
                                                'error_jsdatetextfield<span id="error_jsdatetextfield" class="error">forcedError</span>',
                                                'if( document.getElementById(\'jsdatetextfield_span\') )',
                                                'var cal_jsdatetextfield = new CalendarPopup(\'jsdatetextfield_span\');',
                                                "cal_jsdatetextfield.setMonthNames('January','February','March','April','May','June','July','August','September','October','November','December');",
                                                "cal_jsdatetextfield.setDayHeaders('S','M','T','W','T','F','S');",
                                                'cal_jsdatetextfield.setWeekStartDay(1);',
                                                "cal_jsdatetextfield.setTodayText('Today');",
                                                'cal_jsdatetextfield.showYearNavigation();',
                                                'cal_jsdatetextfield.showYearNavigationInput();']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield", null, null, null, 'data-old="123"');

        $this->assertEmpty($form->getValue("jsdatetextfield"));

        $this->assertFormFlushContains($form, ['jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="" size="20"  data-old="123"']);
    }

};
