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

if(!R::testConnection()){
     R::setup(CONFIG['db']['dsn'],CONFIG['db']['user'],CONFIG['db']['pass']);
     //  R::fancyDebug( TRUE );
     //  R::freeze(TRUE);
}



$GLOBALS['minimumspred'] = 0.7;


$GLOBALS['Perekrestok'][] = "USDT";
$GLOBALS['Perekrestok'][] = "BTC";
$GLOBALS['Perekrestok'][] = "ETH";
$GLOBALS['Perekrestok'][] = "TRX";
//$Perework[] = "XRP";
//$Perework[] = "USDN";
//$Perework[] = "BCH";
$GLOBALS['Perekrestok'][] = "BNB";
$GLOBALS['Perekrestok'][] = "RUB";
//$Perework[] = "USDC";



// Базовая монета
$BaseMoneta = "USDT"; // Финальная монета в которую продаем связку

$Exchange = "Binance";




$GLOBALS['CUR'] = GetCurText($Exchange);
$GLOBALS['TICKERS'] = GetTickerText($Exchange);



// Фильтруем тикеры на наличие наших перекрестков


// ЭТАП1 Кол-во перекрестков
$AllSpreads = Step1();

// ЭТАП2 Получаем ЛУЧШИЕ спреды
$AllSpreads = Step2($AllSpreads);


// Получение монет с лучшим спредом
//show($AllSpreads);




foreach ($AllSpreads as $Donor=>$VAL)
{
    $AllSpreads[$Donor]['track'] = GetTickersBuy($BaseMoneta, $Donor);
}


show($AllSpreads);


//$TickersBuy = GetTickersBuy($BaseMoneta, $Donor);
//show($TickersBuy);
// Получение цен на бирже покупки





// РАБОЧИЕ ФУНКЦИИ

// Поиск спредов




    function Step1(){
        $AllSpreads = [];
        foreach ($GLOBALS['TICKERS'] as $key=>$val):
            $rasklad = explode("/",$key);

            //echo "Работаем с тикером ".$key."<br>";
            //echo "Монета перекрестка ".$rasklad[1]."<br>";
            if (!in_array($rasklad[1], $GLOBALS['Perekrestok'])){
              //  err("Символа ".$rasklad[1]." нет в ПЕРЕКРЕСТКЕ<br>");
                continue;
            }
            $AllSpreads[$rasklad[0]] = $AllSpreads[$rasklad[0]] +1;
        endforeach;


        // Убираем Монеты у которых нет ПЕРЕКРЕСТКОВ!!
        foreach ($AllSpreads as $Donor=>$val):
            if ($val < 2) unset($AllSpreads[$Donor]);
        endforeach;

        return $AllSpreads;
    }


    function Step2($AllSpreads){


        foreach ($AllSpreads as $Donor=>$val):
            //Обнуляем входящий массив
           unset($AllSpreads[$Donor]);
            // Получаем максимальный спред
            foreach ($GLOBALS['Perekrestok'] as $Perekrestok):
                $TickerCheck = $Donor."/".$Perekrestok;
                if (isset($GLOBALS['TICKERS'][$TickerCheck])){
                    $Ticker = $GLOBALS['TICKERS'][$TickerCheck];
                    $spread = changemet($Ticker['bid'], $Ticker['ask']);
                   // echo "Работаем с ".$TickerCheck." <br>";
                  //  echo "Спред ".$spread." <br>";

                    if ($spread < $GLOBALS['minimumspred']) continue;

                    if ($Ticker['quoteVolume'] < 1) continue;

                    // Записываем ЛУЧШИЙ СПРЕД!
                    if ($spread > $AllSpreads[$Donor]){
                        $AllSpreads[$Donor]['Donor'] = $Donor;
                        $AllSpreads[$Donor]['Base'] = $Perekrestok;
                        $AllSpreads[$Donor]['QuoteVolume'] = $Ticker['quoteVolume'];
                        $AllSpreads[$Donor]['SpreadGlobal'] = $spread;
                    }

                }
            endforeach;
        endforeach;




        return $AllSpreads;

    }





    function GetTickersBuy($BaseMoneta, $Donor){
        $ARR = [];

        $TICKERS = $GLOBALS['TICKERS'];

        $SellBID = [];
        $BuyBID = [];

        foreach ($GLOBALS['Perekrestok'] as $key=>$Perekrestok){

            $symbol = $Donor."/".$Perekrestok;

            //$price = 0;

            if ($Perekrestok == $BaseMoneta){
                $SellBID[$symbol] = $TICKERS[$symbol]['bid'];
              //  $ask = $TICKERS[$symbol]['ask'];
              //  $bid = $TICKERS[$symbol]['bid'];
                continue;
            }

            if ($Perekrestok != $BaseMoneta){

                $bid = $TICKERS[$symbol]['bid']; // Цена в основной монете
                $ask = $TICKERS[$symbol]['ask']; // Цена в основной монете

                if (empty($ask)){
                   // err('Тикер'.$symbol.' не торгуется<br>');
                    continue;
                }
               // echo "Цена в основной монете ".$price."<br>";

                if (empty($TICKERS[$Perekrestok."/".$BaseMoneta]['close'])) {
                   err('Не найден тикер'.$Perekrestok."/".$BaseMoneta.' <br>');
                    continue;
                } // Если тикер не найден, то пропускаем
                $bid = $bid*$TICKERS[$Perekrestok."/".$BaseMoneta]['bid']; // Цена если перекресток сконвертировать в исходную монету
                $BuyBID[$symbol] = $bid;
            }


        }

        //show($SellASK);

        asort($BuyBID);
        $firstkey = array_key_first($BuyBID);
        $BuyBIDFINAL[$firstkey] = $BuyBID[$firstkey];

        $spread = changemet(reset($BuyBIDFINAL),reset($SellBID));
       // show($BuyBIDFINAL);

        $ARR['BuyBidFinal'] = $BuyBIDFINAL;
        $ARR['SellBid'] = $SellBID;
        $ARR['spread'] = $spread;

        return $ARR;

    }


    function GetActualPrice($BaseMoneta, $value, $TICKERS){

        $price = 0;



        return $price;
    }


    // ТЕХНИЧЕСКИЕ ФУНКЦИИ
   function GetCurText($exchange){

        $DATA = [];
        $file = file_get_contents(WWW."/Cur".$exchange.".txt");     // Открыть файл data.json
        $DATA = json_decode($file,TRUE);              // Декодировать в массив


        if (empty($DATA)) return false;

        return $DATA;

    }


    function GetTickerText($exchange){
        $file = file_get_contents(WWW."/Ticker".$exchange.".txt");     // Открыть файл data.json
        $MASSIV = json_decode($file,TRUE);              // Декодировать в массив
        return $MASSIV;
    }


    function err($text){
         echo "<font color='#8b0000'>".$text."</font>";
    }


?>
