<?php namespace App\Controllers\Demo;

use App\Kernel\ControllerAbstract;

class HomeController extends ControllerAbstract
{
    const API_URL = "https://free.currconv.com/api/v7/";
    const API_KEY =  "a6045149413626f6664a";
    const COMAPCT = "ULTRA";
    public $queryCurrenyOne;
    public $queryCurrenyTwo;

    public function getCurrencyFeed()
    {
        $url = "test";

        return $url;
    }

    public function getCurrencyData()
    {

    }

    /**
     * Index Action
     *
     * @return string
     */
    public function index()
    {
        return $this->render('Demo/Home/index.twig');
    }
}
