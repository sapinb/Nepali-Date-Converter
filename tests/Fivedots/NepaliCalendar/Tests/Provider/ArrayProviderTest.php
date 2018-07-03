<?php
/**
 * Created by PhpStorm.
 * User: broncha
 * Date: 7/29/15
 * Time: 11:37 AM
 */
namespace Fivedots\NepaliCalendar\Tests\Provider;

use Fivedots\NepaliCalendar\Provider\ArrayProvider;

class ArrayProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new ArrayProvider();
    }

    public function testGetData()
    {
        $expected = array(2007, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31);
        $found = $this->provider->getData(2007);

        $this->assertEquals($expected, $found);
    }

    /**
     * @expectedException \Fivedots\NepaliCalendar\CalendarException
     */
    public function testGetDataException()
    {
        $this->provider->getData(3050);
    }

    public function testIsValidDate()
    {
        $this->assertTrue($this->provider->isValidDate(2007, 1, 31));
    }

    public function testIsValidDateFail()
    {
        $this->assertFalse($this->provider->isValidDate(2043,11,31));
    }

    public function testIsValidADDate()
    {
        $this->assertTrue($this->provider->isValidADDate(2018, 7, 3));
        $this->assertTrue($this->provider->isValidADDate(1913, 4, 13));
        $this->assertTrue($this->provider->isValidADDate(2034, 4, 13));
    }

    public function testIsValidADDateFail()
    {
        $this->assertFalse($this->provider->isValidADDate(2018, 13, 3));
        $this->assertFalse($this->provider->isValidADDate(2018, 2, 30));
        $this->assertFalse($this->provider->isValidADDate(1910, 1, 1));
        $this->assertFalse($this->provider->isValidADDate(2035, 1, 1));
    }
}
