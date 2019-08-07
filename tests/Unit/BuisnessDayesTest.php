<?php

namespace Tests\Unit;

use App\DataSources\HolidaysDumbSource;
use App\Http\Controllers\BuisnessDaysController;
use Tests\TestCase;

class BusinessDaysTest extends TestCase
{
    public function compareResults()
    {

    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBusinesDatesLogic()
    {
        $bdaysUtil = new BuisnessDaysController();

        $result = $bdaysUtil->calculateData('2019-07-29T00:00:00Z', 1);

        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 0,
            'totalDays' => 1,
            'businessDate' => '2019-07-30T00:00:00Z'
        ], $result);


        // Playing with different input formats
        $result = $bdaysUtil->calculateData('2019-07-29T00:00:00Z', 1);
        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 0,
            'totalDays' => 1,
            'businessDate' => '2019-07-30T00:00:00Z'
        ], $result);


        $result = $bdaysUtil->calculateData('2018-11-10T00:00:00Z', 3);
        $this->assertEquals([
            'holidayDays' => 1,
            'weekendDays' => 2,
            'totalDays' => 5,
            'businessDate' => '2018-11-15T00:00:00Z'
        ], $result);



        $result = $bdaysUtil->calculateData('2018-11-15T00:00:00Z', 3);
        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 2,
            'totalDays' => 5,
            'businessDate' => '2018-11-20T00:00:00Z' # Wrong result in task description
        ], $result);



        $bdaysUtil = new BuisnessDaysController();
        $result = $bdaysUtil->calculateData('2018-12-25T00:00:00Z', 20);
        $this->assertEquals([
            'holidayDays' => 4,
            'weekendDays' => 8,
            'totalDays' => 31,
            'businessDate' => '2019-01-25T00:00:00Z'  # Wrong result in task description
        ], $result);
    }
}
