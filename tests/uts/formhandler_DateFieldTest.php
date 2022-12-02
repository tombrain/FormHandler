<?php

declare(strict_types=1);

require_once 'helper/formhandlerTestCase.php';


final class formhandler_DateFieldTest extends FormhandlerTestCase
{
    private function getDays($days, $daySelected = null) : array
    {
        $r = [];
        for ($i = 1; $i <= 31; $i++)
        {
            if ($daySelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%02d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%02d</option>", $i, $i);
        }

        return $r;
    }
    private function getMonths($monthSelected = null) : array
    {
        $aMonthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $r = [];
        for ($i = 1; $i <= 12; $i++)
        {
            if ($monthSelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%s</option>", $i, $aMonthNames[$i - 1]);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%s</option>", $i, $aMonthNames[$i - 1]);
        }

        return $r;
    }
    private function getYears($startYear, $endYear, $yearSelected = null) : array
    {
        $r = [];
        for ($i = $startYear; $i >= $endYear; $i--)
        {
            if ($yearSelected == $i)
                $r[] = sprintf("<option  value=\"%d\"  selected=\"selected\">%d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%d\" >%d</option>", $i, $i);
        }

        return $r;
    }


    public function test_new(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

    public function test_new_required(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, true);

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $t =$this->assertFormFlushContains($form, $aExpected);
        $this->assertFalse(strpos($t, '<option  value="" ></option>'));
    }

    public function test_new_no_default(): void
    {
        define('FH_DATEFIELD_SET_CUR_DATE', false);
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths());
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

    public function test_interval(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, null, '20:20');

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y') + 20;
        $endYear = $startYear - 39;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "03";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";
        $_POST['datefield2_day'] = "03";
        $_POST['datefield2_month'] = "04";
        $_POST['datefield2_year'] = "2018";
        $_POST['datefield3_day'] = "03";
        $_POST['datefield3_month'] = "04";
        $_POST['datefield3_year'] = "2018";
        $_POST['datefield4_day'] = "03";
        $_POST['datefield4_month'] = "04";
        $_POST['datefield4_year'] = "2018";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield");
        $form->dateField("Datefield2", "datefield2", null, null, "d.m.y");
        $form->dateField("Datefield3", "datefield3", null, null, "y/m/d");
        $form->dateField("Datefield4", "datefield4", null, null, "D-M-Y");

        $this->assertEquals("03-04-2018", $form->getValue("datefield"));
        $this->assertEquals("03.04.2018", $form->getValue("datefield2"));
        $this->assertEquals("2018/04/03", $form->getValue("datefield3"));
        $this->assertEquals("03-04-2018", $form->getValue("datefield4"));

        $this->assertEquals([2018, 4, 3], $form->getAsArray("datefield"));
        $this->assertEquals([2018, 4, 3], $form->getAsArray("datefield2"));
        $this->assertEquals([2018, 4, 3], $form->getAsArray("datefield3"));
        $this->assertEquals([2018, 4, 3], $form->getAsArray("datefield4"));
    }

    public function test_new_mask1(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "y-m-d");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

    public function test_new_mask2(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "d.m.y");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> . <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> . <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

    public function test_new_editfields(): void
    {
        define('FH_DATEFIELD_SET_CUR_DATE', false);
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");
        $form->dateField("Datefield2", "datefield2", null, null, "Y.M-D");

        $this->assertFormFlushContains($form, ['Datefield: <input type="text" name="datefield_day" id="datefield_day" value="" size="2" maxlength="2" /> - <input type="text" name="datefield_month" id="datefield_month" value="" size="2" maxlength="2" /> - <input type="text" name="datefield_year" id="datefield_year" value="" size="4" maxlength="4" /> error_datefield',
                                                'Datefield2: <input type="text" name="datefield2_year" id="datefield2_year" value="" size="4" maxlength="4" /> . <input type="text" name="datefield2_month" id="datefield2_month" value="" size="2" maxlength="2" /> - <input type="text" name="datefield2_day" id="datefield2_day" value="" size="2" maxlength="2" /> error_datefield2']);
    }

    public function test_posted_editfields_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "03";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        $this->assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        $this->assertEquals("03-04-2018", $form->getValue("datefield"));

        $form->setError("datefield", "forcedError");

        $this->assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="03" size="2" maxlength="2" class="error" />' .
                                                ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
                                                ' - <input type="text" name="datefield_year" id="datefield_year" value="2018" size="4" maxlength="4" class="error" /> error_datefield<span id="error_datefield" class="error">forcedError');
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $this->assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, null, null, 'data-old="123"');

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1" data-old="123">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $this->assertFormFlushContains($form, $aExpected);
    }

};
