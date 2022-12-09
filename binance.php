<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Panel;
use RedBeanPHP\R;

define('WWW', __DIR__);
define('CONFIG', require 'config/main.php');

require 'vendor/autoload.php';
require 'lib/functions.php'; //ОБЩИЕ ФУНКЦИИ
require 'lib/functions_app.php'; //ФУНКЦИИ ПРИЛОЖЕНИЯ




echo "TICKERS";



TickersBinance();







function TickersBinance(){

        $exchange = new \ccxt\binance (array ('timeout' => 30000));
        $DATA = $exchange->fetch_tickers();

        if (!empty($DATA)) WriteTickers("Binance", $DATA);
        //sleep(1);
      TickersBinance();
        return true;
    }



    function WriteTickers($exchangename, $Tickers){

        echo "<b>Вывод тикеров</b> ".$exchangename."<br>";


        if (!file_exists("Ticker".$exchangename.".txt"))
        {
            $fd = fopen("Ticker".$exchangename.".txt", 'w') or die("не удалось создать файл");
            fwrite($fd, "");
            fclose($fd);
        }

       // show($Tickers);
        $data = json_encode($Tickers);
        //echo  json_last_error();
       // show($data);

        file_put_contents("Ticker".$exchangename.".txt", $data);

        return true;

    }




?>
