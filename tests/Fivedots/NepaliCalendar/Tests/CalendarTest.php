<?php
/**
 * Created by PhpStorm.
 * User: broncha
 * Date: 7/29/15
 * Time: 11:53 AM
 */
namespace Fivedots\NepaliCalendar\Tests;

use Fivedots\NepaliCalendar\Calendar;
use Fivedots\NepaliCalendar\Provider\ArrayProvider;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Calendar
     */
    private $calendar;

    protected function setUp()
    {
        $this->calendar = new Calendar(new ArrayProvider());
    }

    public function dateProvider()
    {
        return [
            [
                [
                    'year' => 2014,
                    'month' => 12,
                    'date' => 31,
                    'day' => 'Wednesday',
                    'numDay' => 4,
                    'nmonth' => 'December'
                ],
                [
                    'year' => 2071,
                    'month' => 9,
                    'date' => 16,
                    'day' => 'Wednesday',
                    'numDay' => 4,
                    'nmonth' => 'Poush'
                ],
            ],
            [
                [
                    'year' => 2018,
                    'month' => 7,
                    'date' => 3,
                    'day' => 'Tuesday',
                    'numDay' => 3,
                    'nmonth' => 'July'
                ],
                [
                    'year' => 2075,
                    'month' => 3,
                    'date' => 19,
                    'day' => 'Tuesday',
                    'numDay' => 3,
                    'nmonth' => 'Ashar'
                ],
            ],
            [
                [
                    'year' => 1942,
                    'month' => 4,
                    'date' => 13,
                    'day' => 'Monday',
                    'numDay' => 2,
                    'nmonth' => 'April'
                ],
                [
                    'year' => 1999,
                    'month' => 1,
                    'date' => 1,
                    'day' => 'Monday',
                    'numDay' => 2,
                    'nmonth' => 'Baisakh'
                ],
            ],
            [
                [
                    'year' => 1933,
                    'month' => 4,
                    'date' => 13,
                    'day' => 'Thursday',
                    'numDay' => 5,
                    'nmonth' => 'April'
                ],
                [
                    'year' => 1990,
                    'month' => 1,
                    'date' => 1,
                    'day' => 'Thursday',
                    'numDay' => 5,
                    'nmonth' => 'Baisakh'
                ],
            ],
            [
                [
                    'year' => 1943,
                    'month' => 4,
                    'date' => 13,
                    'day' => 'Tuesday',
                    'numDay' => 3,
                    'nmonth' => 'April'
                ],
                [
                    'year' => 1999,
                    'month' => 12,
                    'date' => 31,
                    'day' => 'Tuesday',
                    'numDay' => 3,
                    'nmonth' => 'Chaitra'
                ],
            ],
        ];
    }

    /**
     *
     * @dataProvider dateProvider
     */
    public function testNepaliToEnglishDate($ad, $bs)
    {
        $date = $this->calendar->nepaliToEnglish($bs['year'], $bs['month'], $bs['date']);
        $this->assertInternalType('array', $date);
        $this->assertSame($ad, $date);    
    }

    /**
     * @expectedException \Fivedots\NepaliCalendar\CalendarException
     */
    public function testNepaliToEnglishDateFail()
    {
        $date = $this->calendar->nepaliToEnglish(2043, 11, 31);
    }

    /**
     * @covers \Fivedots\NepaliCalendar\Calendar::englishToNepali()
     * @throws \Fivedots\NepaliCalendar\CalendarException
     * @dataProvider dateProvider
     */
    public function testEnglishToNepaliDate($ad, $bs)
    {
        $date = $this->calendar->englishToNepali($ad['year'], $ad['month'], $ad['date']);
        $this->assertInternalType('array', $date);
        $this->assertSame($bs, $date);
    }

    /**
     * @param $year
     * @param $expectedResult
     * @covers \Fivedots\NepaliCalendar\Calendar::isLeapYear()
     * @dataProvider providerTestIsLeapYear
     */
    public function testIsLeapYear($year,$expectedResult){

        $result = $this->calendar->isLeapYear($year);
        $this->assertEquals($expectedResult,$result);

    }

    /**
     * @return array Years to test as leap years
     */
    public function providerTestIsLeapYear(){
        return array(
            array(2006,false),
            array(2008,true),
            array(2010,false),
            array(2012,true),
            array(2013,false),
            array(2016,true),
        );
    }
}
