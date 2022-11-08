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

$_GLOBAL['FC'] = [];

if(!R::testConnection()){
     R::setup(CONFIG['db']['dsn'],CONFIG['db']['user'],CONFIG['db']['pass']);
     //  R::fancyDebug( TRUE );
     //  R::freeze(TRUE);
}



$Perework[] = "USDT";
$Perework[] = "BTC";
$Perework[] = "ETH";
$Perework[] = "TRX";
//$Perework[] = "RUB";
//$Perework[] = "LTC";
$Perework[] = "XRP";
$Perework[] = "USDN";
$Perework[] = "BCH";
$Perework[] = "BNB";
$Perework[] = "USDC";

$_GLOBAL['PW'] = $Perework;


//if (empty($_POST)) exit("fi");


$_POST['StartMoneta'] = "USDT";
$_POST['type'] = "enter";
$_POST['StartCapital'] = 1000;
$_POST['arrEx'] = "Hitbtc";









//show($DATAALL);

// ID CATEGORY = 18

exit("11");

// Центры

$DATA = [];


$xml = simplexml_load_file('https://top-psy.ru/admin/exchange/get_export/13279/?as_file=0');
$objects = $xml->objects;
$objects = (array)$objects;
$objects = $objects['object'];


$DATAALLA = [];
foreach ($objects as $key=>$value) {

    $attr = (array)$value->attributes();
    $value = (array)$value;

    $name = $value['@attributes']['name'];
    $DATA['name'] = $name;

    $content = $value['properties']->group[2]->property[1]->value;
    $content = (array)$content;
    $content = $content[0];
    $DATA['content'] = $content;


    $image = $value['properties']->group[2]->property[0]->value;
    $image = (array)$image;
    if (!empty($image[0])) $image = "https://top-psy.ru".$image[0];
    $DATA['image'] = $image;


    // Поиск контента
  //  show($value);
    echo "11111111111<br>";


//
//    // show($image);
//    // show($name);

    $DATAALL[] = $DATA;

}

foreach ($DATAALL as $key=>$value)
{
    if (is_array($value['image'])){
        unset($DATAALL[$key]);
        continue;
    }

    //   $value['image'] = substr($value['image'], 3);

    $host = parse_url($value['image'])['host'];
    if ($host != "top-psy.ru"){
        unset($DATAALL[$key]);
        continue;
    }

}

show($DATAALL);






exit("1112");


$xml = simplexml_load_file('https://top-psy.ru/admin/exchange/get_export/13278/?as_file=0');
$objects = $xml->objects;
$objects = (array)$objects;
$objects = $objects['object'];

//show($objects);

$DATAALLA = [];
foreach ($objects as $key=>$value) {

    $attr = (array)$value->attributes();
    $value = (array)$value;

    $name = $value['@attributes']['name'];
    $DATA['name'] = $name;

    // Поиск контента
    //show($value);

    $content = $value['properties']->group[2]->property[1]->value;
    $content = (array)$content;
    $content = $content[0];
    $DATA['content'] = $content;


    $image = $value['properties']->group[2]->property[0]->value;
    $image = (array)$image;
    if (!empty($image[0])) $image = "https://top-psy.ru".$image[0];
    $DATA['image'] = $image;

   // show($image);
   // show($name);

    $DATAALL[] = $DATA;
    echo "11111111111<br>";

}



foreach ($DATAALL as $key=>$value)
{
    if (is_array($value['image'])){
        unset($DATAALL[$key]);
        continue;
    }

 //   $value['image'] = substr($value['image'], 3);

    $host = parse_url($value['image'])['host'];
    if ($host != "top-psy.ru"){
        unset($DATAALL[$key]);
        continue;
    }

}


show($DATAALL);


exit("11");


foreach ($objects as $key=>$value)
{
    $attr = (array)$value->attributes();

    $value = (array)$value;


    //show($value);


    // Определить есть ли контент!
    $name = $value['@attributes']['name'];
    $ART['name'] = $name;


    // 3 СВОЙСТВО
    $title = $value['properties']->group->property[3]->title;
    if ($title == "Контент"){
        $content = $value['properties']->group->property[3]->value;
        $content = (array)$content;
        $content = $content[0];
    }

    // 4 СВОЙСТВО
    $title = $value['properties']->group->property[4]->title;
    if ($title == "Контент"){
        $content = $value['properties']->group->property[4]->value;
        $content = (array)$content;
        $content = $content[0];
    }

    // 5 СВОЙСТВО
    $title = $value['properties']->group->property[5]->title;
    if ($title == "Контент"){
        $content = $value['properties']->group->property[5]->value;
        $content = (array)$content;
        $content = $content[0];
    }
    $ART['content'] = $content;


    //date
    $date = $value['properties']->group[1]->property->value;
    $date = (array)$date;
    $date = $date[0];
    $ART['date'] = date("Y-m-d H:i:s", strtotime($date));;


    // image
    $image = $value['properties']->group[2]->property->value;
    $image = (array)$image;
    if (!empty($image[0])) $image = "https://top-psy.ru".$image[0];
    $ART['image'] = $image;


    $ARR[] = $ART;


}






if (!empty($_POST['StartMoneta']) && !empty($_POST['type'])){

     $arrEX = explode(",", $_POST['arrEx']);

     shuffle($arrEX);

     foreach ($arrEX as $exchange) {
          $DATA[$exchange] = GetWorkARR($exchange, $_POST['type'], $_GLOBAL['PW']);
     }


     $RESULT['type'] = $_POST['type'];
     $RESULT['result'] = $DATA;

     show($RESULT);

   //  $RESULT = json_encode($RESULT, JSON_UNESCAPED_UNICODE);
   //  print_r($RESULT);

}



// РАБОЧИЕ ФУНКЦИИ


    function GetTickerBDONE($type, $Ticker){

        $table = [];
        if ($type == "IN") $table = R::findOne("obmenin", 'WHERE ticker=?', [$Ticker]);
        if ($type == "OUT") $table = R::findOne("obmenout",'WHERE ticker=?', [$Ticker]);

        return $table;



    }





    function GetWorkARR($exchange, $napravlenie, $PW){

        $ExchangeTickers = GetTickerText($exchange);


        if (!is_numeric($_POST['StartCapital'])){
            $DATA['errors'] = "Не корректно задан рабочий капитал<br>";
            return $DATA;
        }

        if (empty(GetCurText($exchange))){
            $DATA['errors'] = "Ошибка загрузки монет<br>";
            return $DATA;
        }



        if ($napravlenie == "enter")
        {
            foreach ($PW as $Perekrestok)
            {
                // Сколько ты получаешь монет за исходные USDT
                $StartArr = GetStartArr($exchange);
                //show($StartArr);

                $SITO1 = SitoStep1($StartArr, $Perekrestok, $ExchangeTickers, $exchange);
                if (!empty($SITO1['errors'])) continue;
                $DATA[] = $SITO1;
                continue;
            }
            return $DATA;
        }



        if ($napravlenie == "exit")
        {

            foreach ($PW as $Perekrestok){

                $SITO2 = SitoStep2($ExchangeTickers, $exchange, $Perekrestok);
                if(empty($SITO2)) continue;
                $DATA[] = $SITO2;

            }

            return $DATA;

        }




        return true;

    }


     function GetStartArr($exchange){

        $DATA = [];
         $TickersIN = LoadTickersBD("IN");

        if (empty($TickersIN))
        {
            $DATA['errors'] = "Монета ".$_POST['StartMoneta']." не поддерживается<br>";
            return $DATA;
        }

         foreach ($TickersIN as $VAL)
         {

             // Проверка на доступностью тикера на покупку в бирже

             $checksymbol = checksymbolenter($VAL['ticker'],$exchange);

             if ($checksymbol == false)
             {
                //  echo "<font color='red'>Тикер ".$VAL['ticker']." отключен  </font> <br>";
                 continue;
             }


             if ($VAL['limit'] > $_POST['StartCapital']){
                 //  echo "<font color='red'>Тикер ".$VAL['ticker']." не проходит по стартовому капиталу  </font> <br>";
                 continue;
             }

             $DATA[$VAL['ticker']] = $_POST['StartCapital']/$VAL['price'];

         }


        return $DATA;
    }


     function SitoStep1($StartArr, $Perekrestok,$ExchangeTickers, $exchange){

        $DATA = [];
        $STEP1 = [];
        // ШАГ-1 Получаем самый выгодный курс переход в монету перекрестка

        foreach ($StartArr as $key=>$value)
        {

            // Получаем ТИКЕР с БИРЖИ
            $TickerBirga = $key."/".$Perekrestok;
            if (empty($ExchangeTickers[$TickerBirga]['bid'])) continue;
           //   show($ExchangeTickers[$TickerBirga]);
                   echo "Тикер на бирже: ".$TickerBirga." <br>";
            $avgprice = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;
            $amountPerekrestok = $value*$avgprice;
             echo "Берем монету ".$key." меняем ее на ".$Perekrestok." и получаем ".$amountPerekrestok." ".$Perekrestok." <br> ";

            // Фильтрация символ на ОБЪЕМ ТОРГОВ
            echo "Объем торгов монетой: ".$TickerBirga." - ".$ExchangeTickers[$TickerBirga]['baseVolume']." <br>";
            echo "Наше кол-во монеты: ".$value."<br>";




            if ($value > $ExchangeTickers[$TickerBirga]['baseVolume']/2)
            {
                $continue = 0;
                if ($exchange == "Payeer") $continue = 1;
           //    if ($exchange == "Waves") $continue = 1;

                if ($continue == 0) continue;
                //echo "<font color='red'>Тикер ".$TickerBirga." не проходит по объему торгов</font> <br> ";

            }

            $STEP1['amount'][$key] = $value;
            $STEP1['result'][$key] = $amountPerekrestok;

        }

        if (empty($STEP1))
        {
            $DATA['errors'] = "Ошибка 101 (".$Perekrestok.")";
            //  echo "<font color='#8b0000'>На бирже отсутсвует перекидывание через <b>".$Perekrestok."</b></font>";
            return $DATA;
        }

        arsort($STEP1['result']);

        //show($STEP1);

        $DATA['exchange'] = $exchange;
        $DATA['startcapital'] = $_POST['StartCapital'];
        $DATA['startmoneta'] = $_POST['StartMoneta'];
        $DATA['symbolbest'] = array_key_first($STEP1['result']);
        $DATA['symbolamount'] = $STEP1['amount'][$DATA['symbolbest']];
        $DATA['perekrestok'] = $Perekrestok;
        $DATA['amount'] = reset($STEP1['result']);

        if ($_POST['StartMoneta'] == $Perekrestok)
        {
            $DATA['amountstart'] = reset($STEP1['result']);
        }

        if ($_POST['StartMoneta'] != $Perekrestok)
        {

            $pricetick = $Perekrestok."/".$_POST['StartMoneta'];

            if ($Perekrestok == "USDN") $pricetick = $_POST['StartMoneta']."/".$Perekrestok;
            if ($Perekrestok == "USDC") $pricetick = $_POST['StartMoneta']."/".$Perekrestok;



           // echo "ПрайсТИК ".$pricetick."<br>";

            if (empty($ExchangeTickers[$pricetick]['bid']))
            {
                if ($Perekrestok == "LTC") $pricetick = $_POST['StartMoneta']."/".$Perekrestok;

                //echo "Перекрестный тикер не найден<br>";

            }

            $avgprice = ($ExchangeTickers[$pricetick]['bid']+$ExchangeTickers[$pricetick]['ask'])/2;
            $DATA['amountstart'] = $DATA['amount']*$avgprice;
        }



        return $DATA;

    }

    function SitoStep2($ExchangeTickers, $exchange, $Perekrestok){

        $DATA = [];

        $TickersOUT = LoadTickersBD("OUT");


        // Проверка на доступностью тикера на покупку в бирже

        foreach ($TickersOUT as $VAL){



            $checksymbol = checksymbolenter($VAL['ticker'], $exchange);


            if ($checksymbol == false)
            {
              //  echo "<font color='red'>Тикер ".$VAL['ticker']." отключен  </font> <br>";
                continue;
            }
            if ($VAL['limit'] > $_POST['StartCapital']){
               // echo "<font color='red'>Тикер ".$VAL['ticker']." не проходит по стартовому капиталу  </font> <br>";
                continue;
            }

            $TickerBirga = $VAL['ticker']."/".$Perekrestok;

            if (empty($ExchangeTickers[$TickerBirga]['bid'])) continue;

          //  echo "Тикер на бирже ".$TickerBirga."<br>";


            $amountperekrestok = 0;
            if ($_POST['StartMoneta'] == $Perekrestok) $amountperekrestok = $_POST['StartCapital'];
            if ($_POST['StartMoneta'] != $Perekrestok)
            {
                $PerekrestokTicker = $Perekrestok."/".$_POST['StartMoneta'];
                $PerekrestokAVGprice = ($ExchangeTickers[$PerekrestokTicker]['bid'] + $ExchangeTickers[$PerekrestokTicker]['ask'])/2;
                $amountperekrestok = $_POST['StartCapital']/$PerekrestokAVGprice;
            }
            // Получаем сколько монеты получим после продажи

 
            $avgprice = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;
          //  $avgprice = $ExchangeTickers[$TickerBirga]['bid'];
            $amoumtMoneta = $amountperekrestok/$avgprice;


            // Фильтрация символ на ОБЪЕМ ТОРГОВ
       //      echo "Объем торгов монетой: ".$TickerBirga." - ".$ExchangeTickers[$TickerBirga]['baseVolume']." <br>";
       //      echo "Наше кол-во монеты: ".$amoumtMoneta."<br>";

            if ($amoumtMoneta > $ExchangeTickers[$TickerBirga]['baseVolume']/2)
            {

                if ($exchange != "Payeer") continue;

            //    echo "Кол-во монеты: ".$amoumtMoneta."<br>";
            //    echo "Объем в кол-ве монеты: ".$ExchangeTickers[$TickerBirga]['baseVolume']."<br>";
            //    echo "<font color='red'>Тикер ".$TickerBirga." не проходит по объему торгов</font> <br> ";
            //    echo "<hr>";
              //  continue;
            }

            $resultsale = $amoumtMoneta*$VAL['price'];

          //   echo "Работаем с тикером ".$VAL['ticker']."<br>";
         //     echo "Тикер на бирже ".$TickerBirga."<br>";
          //     echo "Цена ".$avgprice."<br>";
          //     echo "Кол-во актива ".$amoumtMoneta."<br>";


            $RESULT['amount'][$VAL['ticker']] = $amoumtMoneta;
            $RESULT['result'][$VAL['ticker']] = $resultsale;

        }

      //  show($RESULT);

        if (empty($RESULT['result'])) return false;

        arsort($RESULT['result']);

        $DATA['exchange'] = $exchange;
        $DATA['startcapital'] = $_POST['StartCapital'];
        $DATA['startmoneta'] = $_POST['StartMoneta'];
        $DATA['perekrestok'] = $Perekrestok;
        $DATA['amountperekrestok'] = $amountperekrestok;
        $DATA['symbolbest'] = array_key_first($RESULT['result']);
        $DATA['symbolamount'] = $RESULT['amount'][$DATA['symbolbest']];
        $DATA['amount'] = reset($RESULT['result']);


        return $DATA;


    }





    // ТЕХНИЧЕСКИЕ ФУНКЦИИ
   function GetCurText($exchange){

        if ($exchange == "Payeer") return true;
       if ($exchange == "Waves") return true;


        $DATA = [];
        $file = file_get_contents(WWW."/Cur".$exchange.".txt");     // Открыть файл data.json
        $DATA = json_decode($file,TRUE);              // Декодировать в массив


        if (empty($DATA)) return false;

        return $DATA;

    }

    function checksymbolenter($symbol,$exchange)
    {


        if ($exchange == "Payeer") return true;
        if ($exchange == "Waves") return true;


        if ($symbol == "USDT") return true;
        if ($symbol == "BTC") return true;
        if ($symbol == "ETH") return true;

        $FC = GetCurText($exchange);


        if (empty($FC))
        {
            //echo "Символа ".$symbol." нет на бирже! <br>";
            return false;
        }


        if (isset($FC[$symbol]['payin']) && $FC[$symbol]['payin'] == false) return false;
        if (isset($FC[$symbol]['payout']) && $FC[$symbol]['payout'] == false) return false;



        if ($FC[$symbol]['code'] == $symbol)
        {
            if (!empty($FC[$symbol]['info']['disabled']) && $FC[$symbol]['info']['disabled'] == 1) return false;

        }





        return true;

    }


    function GetTickerText($exchange){
        $file = file_get_contents(WWW."/Ticker".$exchange.".txt");     // Открыть файл data.json
        $MASSIV = json_decode($file,TRUE);              // Декодировать в массив
        return $MASSIV;
    }


    function LoadTickersBD($type)
    {

        $table = [];
        if ($type == "IN") $table = R::findAll("obmenin", 'WHERE method=?', [$_POST['StartMoneta']]);
        if ($type == "OUT") $table = R::findAll("obmenout",'WHERE method=?', [$_POST['StartMoneta']]);

        return $table;
    }



?>
