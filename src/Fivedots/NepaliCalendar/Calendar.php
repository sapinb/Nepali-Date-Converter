<?php
namespace Fivedots\NepaliCalendar;

use Fivedots\NepaliCalendar\Month\Nepali;
use Fivedots\NepaliCalendar\Month\English;
use Fivedots\NepaliCalendar\Provider\ProviderInterface;

class Calendar
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    public function __construct(ProviderInterface $providerInterface){
        $this->provider = $providerInterface;
    }


    /**
     * Calculates whether english year is leap year or not
     *
     * @param int $year Year to check for leap year
     * @return bool If is leap year return true otherwise false
     */
    public function isLeapYear($year)
    {
        $a = $year;
        if ($a % 100 == 0) {
            if ($a % 400 == 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            if ($a % 4 == 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Returns converted English date for the supplied Nepali date
     * @param $year int Year (2071)
     * @param $month int Month (09)
     * @param $date  int Date (16)
     * @return array Converted English Date
     * @throws CalendarException CalendarException
     */
    public function nepaliToEnglish($year, $month, $date)
    {
        // NOTE: bs month and date MUST BE XXXX/01/01 (Baisakh 1st)
        $date_range = $this->provider->getValidRange();
        $ref_ad = $date_range['ad_min'];

        $ref_bs = $date_range['bs_min'];

        $ref_ad_date = (new \DateTimeImmutable())
            ->setDate($ref_ad['year'], $ref_ad['month'], $ref_ad['date'])
            ->setTime(0, 0);

        // Check for date range
        if (!$this->provider->isValidDate($year, $month, $date)) {
            throw new CalendarException(sprintf(CalendarMessages::E_OUT_OF_RANGE, $year, $month, $date));
        }

        $total_nDays = 0;

        // count number of days from all years (except current year)
        for ($yy = $ref_bs['year']; $yy < $year; ++$yy) {
            $year_data = $this->provider->getData($yy);
            for ($mm = 1; $mm <= 12; ++$mm) {
                $total_nDays += $year_data[$mm];
            }
        }

        // count number of days from current year (except current month)
        $year_data = $this->provider->getData($year);
        for ($mm = 1; $mm < $month; ++$mm) {
            $total_nDays += $year_data[$mm];
        }

        // count number of days from current month 
        $total_nDays += $date;

        // adjust for reference date
        $total_nDays -= 1;

        $englishDate = $ref_ad_date->add(new \DateInterval("P{$total_nDays}D"));

        $engMonth = (int) $englishDate->format('n');
        $dayOfWeek = ((int) $englishDate->format('w')) + 1;

        $engDate = new DateVO();
        $engDate->year = (int) $englishDate->format('Y');
        $engDate->month = $engMonth;
        $engDate->date = (int) $englishDate->format('j');
        $engDate->day = Days::getTitle($dayOfWeek);
        $engDate->nmonth = English::getTitle($engMonth);
        $engDate->numDay = $dayOfWeek;

        return (array) $engDate;
    }

    /**
     * currently can only calculate the date between AD 1944-2033...
     *
     * @param int $year Year
     * @param int $month Month
     * @param int $date Date
     * @return array Converted Nepali Date for the supplied English Date
     * @throws CalendarException CalendarExceptions
     */
    public function englishToNepali($year, $month, $date)
    {
        // fixed points : AD 1943/04/14 === BS 2000/01/01
        // bs month and date MUST BE XXXX/01/01 (Baisakh 1st)
        $date_range = $this->provider->getValidRange();
        $ref_ad = $date_range['ad_min'];

        $ref_bs = $date_range['bs_min'];

        $ref_ad_date = (new \DateTimeImmutable())
            ->setDate($ref_ad['year'], $ref_ad['month'], $ref_ad['date'])
            ->setTime(0, 0);

        if (!$this->provider->isValidADDate($year, $month, $date)) {
            throw new CalendarException(sprintf(CalendarMessages::E_OUT_OF_RANGE, $year, $month, $date));
        }

        $ad_date = (new \DateTimeImmutable())
            ->setDate($year, $month, $date)
            ->setTime(0, 0);
        
        $diff_days = $ad_date->diff($ref_ad_date)->days;

        $total_days = $diff_days;
        $bs_year = $ref_bs['year'];
        $bs_month = $ref_bs['month'];
        $bs_day = $ref_bs['date'];

        while ($total_days > 0) {
            $year_data = $this->provider->getData($bs_year);

            $month_days = $year_data[$bs_month];

            $total_days--;
            $bs_day++;

            if ($bs_day > $month_days) {
                $bs_month++;
                $bs_day = 1;
            }

            if ($bs_month > 12) {
                $bs_year++;
                $bs_month = 1;
            }
        }

        $dayOfWeek = ((int) $ad_date->format('w')) + 1;

        $nepDate = new DateVO();
        $nepDate->year = $bs_year;
        $nepDate->month = $bs_month;
        $nepDate->date = $bs_day;
        $nepDate->day = Days::getTitle($dayOfWeek);
        $nepDate->nmonth = Nepali::getTitle($bs_month);
        $nepDate->numDay = $dayOfWeek;

        return (array)$nepDate;
    }


}