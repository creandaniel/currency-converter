<?php 

namespace App\Controllers\CurrencyConverter;

use App\Kernel\ControllerAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends ControllerAbstract
{   

    const API_CURRENT_RATES_URL = "https://free.currconv.com/api/v7/convert?q=";
    const API_KEY = '&apiKey=a6045149413626f6664a';

    /**
     * 
     * @param url we use this to fetch the main url to fetch current dates or on historic level
     * @param to currency we convert to
     * @param from curency we convert from
     * @example https://api.ratesapi.io/api/latest?symbols=GBP,USD&base=GBP
     * @example https://api.ratesapi.io/api/latest?symbols=USD,GBP&base=USD
     * @link https://www.currencyconverterapi.com/docs
     * @return array
     */
    public function getCurrencyFeed($from, $to, $url)
    {
        $ch = curl_init($url);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
        $chUrl = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $urlDecoded = json_decode($chUrl);
        
        /**
         * Sometimes the API is down on server outside control, so we give a respecful error message to anybody wanting to use the app
         */
        if($httpcode === 404 || $httpcode === 503)
        {
            die("Service Seems unavaliable, due to a " . $httpcode . " Error");
        }
        else 
        {
           return $urlDecoded;
        }
        
    }

    /**
     * Grab the currency data from API level.
     * @example https://api.ratesapi.io/api/latest?symbols=GBP,USD&base=GBP
     * @example https://api.ratesapi.io/api/latest?symbols=USD,GBP&base=USD
     * @link https://www.currencyconverterapi.com/docs
     * @return array
     */
    public function getCurrencyData($from, $to)
    {   
        $url = self::API_CURRENT_RATES_URL . $from .'_'. $to . self::API_KEY;

        $data = $this->getCurrencyFeed($from, $to, $url);

        return $data;
    }

    
    /**
     * Get the data for the past 7 days, so we can use this for comparison data
     *
     * @link https://free.currconv.com/api/v7/convert?q=USD_PHP,PHP_USD&compact=ultra&date=2019-09-21&endDate=2019-09-15&apiKey=a6045149413626f6664a
     * @return array
     */
    public function getHistoricCurrencyData($from, $to)
    {   

        $currentDate = date("Y-m-d");
        $currentDatePrevday = date("Y-m-d", strtotime( $currentDate . "-1 day"));
        $currentDatePrev7Days = date("Y-m-d", strtotime( $currentDate . "-7 day"));

        $url = self::API_CURRENT_RATES_URL . $from .'_'. $to . '&date=' . $currentDatePrev7Days . '&endDate=' .  $currentDatePrevday . self::API_KEY;

        $data = $this->getCurrencyFeed($from, $to, $url);

        return $data;
    }

    /**
     * Index Action Controller
     *
     * @return string
     */
    public function index()
    {
        /**
         *We want the full list of country codes supported for curremncy conversion to pass to index render as a dropdown menu
         *@var $countryCodes
         *@link  https://www.currencyconverterapi.com/docs
         */
        require('countryCurrencyCodes.php');

        if(empty($_GET['amount']) || !is_numeric($_GET['amount']))
        {
            $_GET['to'] = "GBP";
            $_GET['from'] = "USD";
            $_GET['amount'] = 1;
        }

        $amountInput = $vars['get']['amount'] = $_GET['amount'];
        $fromInput = $vars['get']['from'] = $_GET['from'];
        $toInput = $vars['get']['to'] = $_GET['to'];


        $userCurrInput = $fromInput . '_'. $toInput;

        $smash = $this->getCurrencyData($fromInput, $toInput);
        $fromInputObj = $smash->results->{$userCurrInput}->val;

        $currencyConversion = $amountInput * $fromInputObj;


        $smash2 = $this->getHistoricCurrencyData($fromInput, $toInput);

        $fromInputObjResults = array();
        foreach($smash2->results->{$userCurrInput}->val as $key => $obj)
        {
            $fromInputObjResults[] = array(
                'key' => $key,
                'val' => $obj,
                );
        }

        $data = [
            'amount' => $amountInput,
            'from' => $fromInput,
            'to' => $toInput,
            'currencyConversion' => $currencyConversion,
            'fromInputObj'  => $fromInputObj,
            'fromInputObjResults' => $fromInputObjResults,
            'countryCodes' => $countryCodes
        ];

        return $this->render('CurrencyConverter/Home/index.twig', $data);
    }
}

