<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class BuisnessDaysController extends Controller
{

    public function __construct()
    {

    }


    /**
     * Show the profile for the given user.
     *
     * @param json
     * @return Response
     */
    public function inputData(Request $request)
    {

        $dataRow = $request->json();
        $data = array();
        if (isset($dataRow) && !empty($dataRow) && !empty($dataRow->get('initialDate'))){
            $data['initialDate'] = $dataRow->get('initialDate');
            $data['delay'] = $dataRow->get('delay');
        } elseif (!empty($request->get('json'))) {
            $dataRow = json_decode($request->get('json'));
            $data['initialDate'] = $dataRow->initialDate;
            $data['delay'] = $dataRow->delay;
        }

        if (!isset($data) || empty($data)) {
            echo('Wrong or empty input');
            return;
        }

        $rules = [
            'delay' => 'required|integer',
            'initialDate' => 'required|string'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {

            //calculating
            $resultArray = $this->calculateData($data['initialDate'], $data['delay']);

            if ($resultArray) {


                //convert to PRINT
                $result = $this->outputData($data, $resultArray);

                //PUBLISH
                $this->publishData($result);

                return response()->json($result);

            } else {
                //error?
                dd($validator->errors()->all());
            }
        } else {
            //TODO Handle your error
            dd($validator->errors()->all());
        }
    }

    /**
     * calculate data
     *
     * @param $startDate
     * @param $delay
     * @return mixed
     */
    public function calculateData($startDate, $delay)
    {

        $holydaysArray = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');
        $weekendDays = $holydays = 0;
        $endTimestamp = $startTimestamp = strtotime($startDate);

        for ($days = 0; $days <= $delay; $days++) {

            if ($days > 1) {
                $endTimestamp = $endTimestamp + (60 * 60 * 24);
            }

            //weekend check
            if (date("N", $endTimestamp) <= 5) {
                //holyday check
                $mmgg = date('m-d', $endTimestamp);
                if (in_array($mmgg, $holydaysArray)) {
                    $holydays++;
                    $delay++;
                }
            } else {
                $weekendDays++;
                $delay++;
            }

        }

        $endTime = str_replace('+00:00', 'Z', gmdate('c', $endTimestamp));

        $resultArray['businessDate'] = $endTime;
        $resultArray['totalDays'] = $delay;
        $resultArray['holidayDays'] = $holydays;
        $resultArray['weekendDays'] = $weekendDays;

        return $resultArray;
    }

    /**
     * @param $initialQuery
     * @param $resultArray
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function outputData($initialQuery, $resultArray)
    {

        $results = array('businessDate' => $resultArray['businessDate'],
            'totalDays' => $resultArray['totalDays'],
            'holidayDays' => $resultArray['holidayDays'],
            'weekendDays' => $resultArray['weekendDays']
        );

        $result = json_encode(array('ok' => true,
            'initialQuery' => $initialQuery,
            'results' => $results
        ));

        return $result;
    }

    /**
     * SEND MESSAGE
     * @param $publishData
     */
    public function publishData($publishData)
    {

        $this->pubsub = app('pubsub');
        $this->pubsub->publish('BankWire:businessDates', $publishData);
        app('pubsub.connection');

    }

}