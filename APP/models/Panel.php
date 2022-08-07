<?php
namespace APP\models;
use APP\core\Mail;
use Psr\Log\NullLogger;
use RedBeanPHP\R;


class Panel extends \APP\core\base\Model {

    public $minumumspred = 0.2;


    public $maxamountUSDT = 15000;


    public $TickersBDIN = [];
    public $TickersBDOUT = [];



    public function GetArrWorkExchange($exchange, $base){


        $TickersBDIN = $this->LoadTickersBD("IN", $base);
        $TickersBDOUT = $this->LoadTickersBD("OUT", $base);

        $ExchangeTickers = $this->GetTickerText($exchange);

        $ArrEnter =  $this->GetArrEnter($TickersBDIN, $ExchangeTickers, $base);
        $Obrabotka['enter'] = $this->LoadObrabotka($ArrEnter, "enter", $exchange);

        //  show($Obrabotka);


        $ArrExit =  $this->GetArrExit($TickersBDOUT, $ExchangeTickers, $base);
        $Obrabotka['exit'] = $this->LoadObrabotka($ArrExit, "exit", $exchange);





        return $Obrabotka;


    }

    private function GetArrEnter($TickersBDIN,$ExchangeTickers, $base ){

        $MASSIV = [];

        foreach ($TickersBDIN as $ket=>$TickerWork)
        {

            if ($TickerWork['price'] == "none") continue;
            if ($TickerWork['ticker'] == $base) continue;
            if ($TickerWork['price'] == 0) continue;


            $TickerBirga = $TickerWork['ticker']."/".$base."";

            // ПРИ ДЕБАГЕ И РЕФАКТОРИНГЕ ТЕСТИТЬ И ПРОВЕРЯТЬ!!!!!
            if (empty(($ExchangeTickers[$TickerBirga]['close']))) continue;

            $ExPRICE = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;

            $change = changemet($TickerWork['price'], $ExPRICE );


            //  echo "Работаем с <b> ".$TickerWork['ticker']."</b> <br>";
            //  echo "За ".$startdeposit." ".$MethodENTER." покупаем ".$amountbuy." <b> ".$TickerWork['ticker']." </b> <br> ";
            //  echo "На бирже продаем ".$TickerWork['ticker']." за ".$MethodENTER." получаем  ".$amountsell." <br> ";
            // echo "Цена входа по обменникам: ".$TickerWork['price']."<br>";
            // echo "Цена продажи тикера: ".$ExPRICE."<br>";
            // echo "Спред захода: ".$change."<br>";



            $MASSIV['spred'][$TickerWork['ticker']] = $change;

            $MASSIV['enterprice'][$TickerWork['ticker']] = $TickerWork['price'];
            $MASSIV['exitprice'][$TickerWork['ticker']] = $ExPRICE;

            $MASSIV['url'][$TickerWork['ticker']] = $TickerWork['url'];

            $MASSIV['redirect'][$TickerWork['ticker']] = $TickerWork['redirect'];

            $MASSIV['limit'][$TickerWork['ticker']] = $TickerWork['limit'];

            $MASSIV['scanid'][$TickerWork['ticker']] = $TickerWork['id'];


        }

        arsort($MASSIV['spred']);


        return $MASSIV;


    }

    private function GetArrExit($TickersBDOUT,$ExchangeTickers, $base ){

        $MASSIV = [];

        foreach ($TickersBDOUT as $ket=>$TickerWork)
        {

            if ($TickerWork['price'] == "none") continue;
            if ($TickerWork['ticker'] == $base) continue;
            if ($TickerWork['price'] == 0) continue;


            $TickerBirga = $TickerWork['ticker']."/".$base."";

            // ПРИ ДЕБАГЕ И РЕФАКТОРИНГЕ ТЕСТИТЬ И ПРОВЕРЯТЬ!!!!!
            if (empty(($ExchangeTickers[$TickerBirga]['close']))) continue;

            //      $ExPRICE = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;

            $ExPRICE = $ExchangeTickers[$TickerBirga]['bid'];


            $change = changemet($ExPRICE, $TickerWork['price'] );




            //   echo "Работаем с <b> ".$TickerWork['ticker']."</b> <br>";
            //    echo "На бирже покупаем ".$TickerWork['ticker']." за ".$base." получаем  ".$TickerWork['ticker']." <br> ";
            //    echo "Цена покупки на бирже: ".$ExPRICE."<br>";
            //    echo "Цена продажи по обменникам: ".$TickerWork['price']."<br>";
            //    echo "Спред выхода: ".$change."<br>";


            $limit =  round($TickerWork['limit']*$TickerWork['price'], 5);

            $MASSIV['spred'][$TickerWork['ticker']] = $change;

            $MASSIV['enterprice'][$TickerWork['ticker']] = $ExPRICE;
            $MASSIV['exitprice'][$TickerWork['ticker']] = $TickerWork['price'];

            $MASSIV['redirect'][$TickerWork['ticker']] = $TickerWork['redirect'];

            $MASSIV['url'][$TickerWork['ticker']] = $TickerWork['url'];
            $MASSIV['limit'][$TickerWork['ticker']] = $limit;

            $MASSIV['scanid'][$TickerWork['ticker']] = $TickerWork['id'];


        }

        arsort($MASSIV['spred']);


        return $MASSIV;


    }

    private function LoadObrabotka($ARR, $type, $exchange){
        $DATA = [];

        // echo "КАУНТ: ".count($ARR['spred'])."<br>";

        for ($i=0; $i<=count($ARR['spred']); $i++ ) {

            if (reset($ARR['spred']) < $this->minumumspred) continue;

            $MASS['symbol'] = array_key_first($ARR['spred']);

            if ( $ARR['limit'][$MASS['symbol']] > $this->maxamountUSDT) continue;


            $MASS['exchange'] = $exchange;
            $MASS['type'] = $type;
            $MASS['spred'] = reset($ARR['spred']);
            $MASS['enterprice'] = $ARR['enterprice'][$MASS['symbol']];
            $MASS['exitprice'] = $ARR['exitprice'][$MASS['symbol']];
            $MASS['url'] = $ARR['url'][$MASS['symbol']];
            $MASS['limit'] = $ARR['limit'][$MASS['symbol']];
            $MASS['redirect'] = $ARR['redirect'][$MASS['symbol']];
            $MASS['scanid'] = $ARR['scanid'][$MASS['symbol']];

            array_shift($ARR['spred']);

            $DATA[] = $MASS;

        }


        return $DATA;
    }

    public function LoadScan($id, $type)
    {

        $table = [];
        if ($type == "enter") $table = R::Load("obmenin", $id);
        if ($type == "exit") $table = R::Load("obmenout", $id);

        return $table;
    }

    private function LoadTickersBD($type, $method)
    {

        $table = [];
        if ($type == "IN") $table = R::findAll("obmenin", 'WHERE method=?', [$method]);
        if ($type == "OUT") $table = R::findAll("obmenout",'WHERE method=?', [$method]);

        return $table;
    }

    public static function GetTickerText($exchange){

        $file = file_get_contents(WWW."/Ticker".$exchange.".txt");     // Открыть файл data.json
        $MASSIV = json_decode($file,TRUE);              // Декодировать в массив
        return $MASSIV;

    }

    public function addrequis($DATA){

        if (!empty($DATA['apiBinance'])){

            $requis = json_decode(self::$USER->requis, true);
            $DATA['apiBinance'] = clearrequis( $DATA['apiBinance']);
            $DATA['keyBinance'] = clearrequis( $DATA['keyBinance']);

            $requis['apiBinance'] = $DATA['apiBinance'];
            $requis['keyBinance'] = $DATA['keyBinance'];

            $requis = json_encode($requis, true);


            $_SESSION['ulogin']['requis'] = $requis;

            self::$USER->requis = $requis;
        }

        if (!empty($DATA['apiPoloniex'])){

            $requis = json_decode(self::$USER->requis, true);
            $DATA['apiPoloniex'] = clearrequis( $DATA['apiPoloniex']);
            $DATA['keyPoloniex'] = clearrequis( $DATA['keyPoloniex']);

            $requis['apiPoloniex'] = $DATA['apiPoloniex'];
            $requis['keyPoloniex'] = $DATA['keyPoloniex'];

            $requis = json_encode($requis, true);


            $_SESSION['ulogin']['requis'] = $requis;

            self::$USER->requis = $requis;
        }






        R::store(self::$USER);

        return true;
    }



    public static function GetWalletAddr($exchange, $moneta){

        $addr = "test";

        if($exchange == "Binance")
        {
            $EXCHANGECCXT = new \ccxt\binance (array(
                'apiKey' => json_decode($_SESSION['ulogin']['requis'], true)['apiBinance'],
                'secret' => json_decode($_SESSION['ulogin']['requis'], true)['keyBinance'],
                'timeout' => 30000,
                'enableRateLimit' => true,
            ));


            $params = [];
            if ($moneta == "USDT") $params = ['network' => 'TRX',];
            $addr = $EXCHANGECCXT->fetch_deposit_address($moneta, $params);

        }

        if($exchange == "Poloniex")
        {


            $EXCHANGECCXT = new \ccxt\poloniex (array(
                'apiKey' => json_decode($_SESSION['ulogin']['requis'], true)['apiPoloniex'],
                'secret' => json_decode($_SESSION['ulogin']['requis'], true)['keyPoloniex'],
                'timeout' => 30000,
                'enableRateLimit' => true,
            ));

           // show($EXCHANGECCXT->has['createDepositAddress']);

           // $params = [];
           // if ($moneta == "USDT") $params = ['network' => 'TRX',];

           // $addr = $EXCHANGECCXT->fetch_deposit_address($moneta, $params);



        }






        return $addr;
    }




}
?>