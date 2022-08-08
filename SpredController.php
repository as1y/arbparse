<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class SpredController extends AppController {
    public $layaout = 'EMPTY';


    public $TickersBDIN = [];
    public $TickersBDOUT = [];
    public $EXCHANGES = [];
    public $minumumspred = 0.3;

    public $FC = [];
    public $StartCapital = 0;
    public $StartMoneta = "";
    public $PereWork = [];

    public $EXLOGOS = [];


    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $Panel =  new Panel();

        $this->layaout = "EMPTY";

        // С какими перекрестками работаем
        $this->PereWork[] = "USDT";
        $this->PereWork[] = "BTC";
        $this->PereWork[] = "ETH";
        $this->PereWork[] = "TRX";


        $this->EXLOGOS['Hitbtc'] = "/assets_base/hitbtc.png";
        $this->EXLOGOS['Exmo'] = "/assets_base/exmo.png";



        if (empty($_POST)) exit("fi");

        $napravlenie = "exit";


        if (!empty($_POST['currency']) && $napravlenie == "enter")
        {

                     $arrEX = explode(",", $_POST['arrEx']);
                     $this->StartMoneta = $_POST['currency'];
                     $this->StartCapital = $_POST['amount'];

                foreach ($arrEX as $exchange) {
                    $DATA = $this->GetWorkARR($exchange, $napravlenie);

                    exit("111");
                   // $this->renderEnter($DATA, $exchange);

                }

            return true;

        }


        if (!empty($_POST['currency']) && $napravlenie == "exit")
        {

            $arrEX = explode(",", $_POST['arrEx']);
            $this->StartMoneta = $_POST['currency'];
            $this->StartCapital = $_POST['amount'];


            foreach ($arrEX as $exchange) {
                $DATA[] = $this->GetWorkARR($exchange, $napravlenie);

            //    $this->renderExit($DATA, $exchange);

            }

            header('Content-Type: application/json;charset=utf-8');

            $display = json_encode($DATA, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);


          //  print_r($display);
           // exit($display);
           // return true;

        }







        // Получение тикера из БД
        if(!empty($_POST['BDIN']) &&  $_POST['BDIN'] == true)
        {
            $Ticker = $this->GetTickerBDONE("IN", $_POST['ticker']);
            $this->WritePersonalTickers($_POST['id'], $Ticker);
            return true;
        }

        if(!empty($_POST['BDOUT']) &&  $_POST['BDOUT'] == true)
        {
            $Ticker = $this->GetTickerBDONE("OUT", $_POST['ticker']);
            $this->WritePersonalTickers($_POST['id'], $Ticker);
            return true;
        }



        return true;
//        $this->set(compact(''));

    }



    private function GetTickerBDONE($type, $Ticker){

        $table = [];
        if ($type == "IN") $table = R::findAll("obmenin", 'WHERE ticker=?', [$Ticker]);
        if ($type == "OUT") $table = R::findAll("obmenout",'WHERE ticker=?', [$Ticker]);

        return $table;



    }

    private function WritePersonalTickers($id, $Tickers){



        if (!file_exists($_SERVER["DOCUMENT_ROOT"] ."/PTICKERS/".$id.".txt"))
        {
            $fd = fopen($_SERVER["DOCUMENT_ROOT"] ."/PTICKERS/".$id.".txt", 'w') or die("не удалось создать файл");
            fwrite($fd, "");
            fclose($fd);
        }

        $data = json_encode($Tickers);

        file_put_contents($_SERVER["DOCUMENT_ROOT"] ."/PTICKERS/".$id.".txt", $Tickers);

        return true;

    }



    private function renderExit($DATA, $exchange){


        if ($this->StartMoneta == "USDT") $round = 2;

       // show($DATA);

        foreach ($DATA as $VAL)
        {
            $profit = $VAL['amount']-$VAL['startcapital'];
            $profit = round($profit, $round);

            if ($profit < 0) continue;

            ?>


            <div class="card card-body">
                <div class="media align-items-center align-items-lg-start text-center text-lg-left flex-column flex-lg-row">
                    <div class="mr-lg-3 mb-3 mb-lg-0">
                        <a href="#">
                            <img src="<?=$this->EXLOGOS[$exchange]?>" width="96" alt="">
                        </a>
                    </div>

                    <div class="media-body">
                        <h6 class="media-title font-weight-semibold">
                            <a href="#">СВЯЗКА НА ВЫХОД</a>
                        </h6>

                        <ul class="list-inline list-inline-dotted mb-3 mb-lg-2">
                            <li class="list-inline-item"><a href="#" class="text-muted"><?=$VAL['startmoneta']?></a></li>
                            <li class="list-inline-item"><a href="#" class="text-muted"><?=$VAL['symbolbest']?></a></li>
                        </ul>

                        <p class="mb-3">

            <?php if($VAL['startmoneta'] == $VAL['perekrestok']): ?>

                1. На бирже продаем <?=$VAL['startcapital']?> <b><?=$VAL['startmoneta']?></b> <i class="icon-redo2"></i>  Получаем ~ <?=$VAL['symbolamount']?> <b><?=$VAL['symbolbest']?></b> <br>
                2. Меняем <?=$VAL['symbolamount']?> <b><?=$VAL['symbolbest']?></b> через обменник и получаем <?=$VAL['amount']?> <b><?=$VAL['startmoneta']?></b><br>
                3. Профит <b><?=$profit?>  <?=$VAL['startmoneta']?></b><br>
            <?php else:?>

                1. На бирже продаем <?=$VAL['startcapital']?> <b><?=$VAL['startmoneta']?></b> и получаем ~ <?=$VAL['amountperekrestok']?> <b><?=$VAL['perekrestok']?></b><br>
                2. На бирже продаем <?=$VAL['amountperekrestok']?> <b><?=$VAL['perekrestok']?></b> и получаем ~ <?=$VAL['symbolamount']?> <b><?=$VAL['symbolbest']?></b><br>
                2. Меняем <?=$VAL['symbolamount']?> <b><?=$VAL['symbolbest']?></b> через обменник и получаем <?=$VAL['amount']?> <b><?=$VAL['startmoneta']?></b><br>
                3. Профит <b><?=$profit?>  <?=$VAL['startmoneta']?></b><br>
            <?php endif; ?>

                        </p>


                    </div>

                    <div class="mt-3 mt-lg-0 ml-lg-3 text-center">

                        <h3 class="mb-0 font-weight-semibold"><span class="text-success">+<?=$profit?><b> <?=$this->StartMoneta?></b></span></h3>

                        <!--                        <div class="text-muted">85 использований</div>-->

                        <a href="/main/work/?symbolbest=<?=$VAL['symbolbest']?>&exchange=<?=$exchange?>&perekrestok=<?=$VAL['perekrestok']?>&type=exit" type="button"  class="btn btn-teal mt-3"><i class="icon-arrow-right8 mr-2"></i> В РАБОТУ</a>
                    </div>
                </div>
            </div>

            <?php
        }


        return true;
    }


    private function renderEnter($DATA, $exchange){


        if ($this->StartMoneta == "USDT") $round = 2;

        foreach ($DATA as $VAL)
        {
            $profit = $VAL['amountstart']-$VAL['startcapital'];
            $profit = round($profit, $round);

            if ($profit < 0) continue;

            ?>


            <div class="card card-body">
                <div class="media align-items-center align-items-lg-start text-center text-lg-left flex-column flex-lg-row">
                    <div class="mr-lg-3 mb-3 mb-lg-0">
                        <a href="#">
                            <img src="<?=$this->EXLOGOS[$exchange]?>" width="96" alt="">
                        </a>
                    </div>

                    <div class="media-body">
                        <h6 class="media-title font-weight-semibold">
                            <a href="#">СВЯЗКА НА ВХОД</a>
                        </h6>

                        <ul class="list-inline list-inline-dotted mb-3 mb-lg-2">
                            <li class="list-inline-item"><a href="#" class="text-muted"><?=$VAL['startmoneta']?></a></li>
                            <li class="list-inline-item"><a href="#" class="text-muted"><?=$VAL['symbolbest']?></a></li>
                        </ul>

                        <p class="mb-3">

                            1. Отдаем <?=$VAL['startcapital']?> <?=$VAL['startmoneta']?> <i class="icon-redo2"></i> | Получаем <?=$VAL['symbolbest']?> <?=$VAL['symbolamount']?> на кошелек биржи <br>
                            2. На бирже продаем <?=$VAL['symbolamount']?> <?=$VAL['symbolbest']?> | Получаем <?=$VAL['amount']?> <?=$VAL['perekrestok']?>  <br>

                            <?php if($VAL['startmoneta'] == $VAL['perekrestok']): ?>
                                3. Профит <b><?=$profit?>  <?=$VAL['startmoneta']?></b><br>
                            <?php else:?>
                                3. Продаем <?=$VAL['amount']?> <?=$VAL['perekrestok']?> и получаем ~ <?=$VAL['startmoneta']?> <?=$VAL['amountstart']?> <br>
                                4. Профит <b><?=$profit?>  <?=$VAL['startmoneta']?></b> <br>
                            <?php endif; ?>

                        </p>


                    </div>

                    <div class="mt-3 mt-lg-0 ml-lg-3 text-center">

                        <h3 class="mb-0 font-weight-semibold"><span class="text-success">+<?=$profit?><b> <?=$this->StartMoneta?></b></span></h3>

                        <!--                        <div class="text-muted">85 использований</div>-->

                        <a href="/main/work/?symbolbest=<?=$VAL['symbolbest']?>&exchange=<?=$exchange?>&type=enter" type="button"  class="btn btn-teal mt-3"><i class="icon-arrow-right8 mr-2"></i> В РАБОТУ</a>
                    </div>
                </div>
            </div>

            <?php
        }


        return true;
    }




    private function GetWorkARR($exchange, $type){
        $DATA = [];

        // Загрузка данных
        $ExchangeTickers = $this->GetTickerText($exchange);

        $TickersIN = $this->LoadTickersBD("IN");
        $TickersOUT = $this->LoadTickersBD("OUT");


        // Проверка параметров

        if (empty($TickersIN))
        {
            $DATA['errors'] = "Монета ".$this->StartMoneta." не поддерживается<br>";
            return $DATA;
        }

        if (empty($TickersOUT))
        {
            $DATA['errors'] = "Монета ".$this->StartMoneta." не поддерживается<br>";
            return $DATA;
        }


        if (!is_numeric($this->StartCapital)){
            $DATA['errors'] = "Не корректно задан рабочий капитал<br>";
            return $DATA;
        }

        // Получение положения монет
       $this->FC = $this->GetCurText($exchange);
        if (empty($this->FC)){
            $DATA['errors'] = "Ошибка загрузки монет<br>";
            return $DATA;
        }


        if ($type == "exit")
        {

            foreach ($this->PereWork as $Perekrestok){

                $SITO2 = $this->SitoStep2($TickersOUT, $ExchangeTickers, $exchange, $Perekrestok);
                if(empty($SITO2)) continue;
                $DATA[] = $SITO2;

            }


        }

        if ($type == "enter")
        {
            foreach ($this->PereWork as $Perekrestok)
            {
                $StartArr = $this->GetStartArr($TickersIN);
                $SITO1 = $this->SitoStep1($StartArr, $Perekrestok, $ExchangeTickers, $exchange);
                if (!empty($SITO1['errors'])) continue;
                $DATA[] = $SITO1;
                continue;
            }

        }



           // $EndArr[$exchange][$Perekrestok]['enter'] = $SITO1;
            //show($SITO1);
            // echo "Получаем список монет которые сможем купить за ".$SITO1['amount']." - ".$SITO1['perekrestok']." <br>";
            //show($SITO2);
            //$EndArr[$exchange][$Perekrestok]['exit'] = $this->GetEndArr($SITO2, $TickersOUT);



        return $DATA;

    }



    private function GetStartArr($TickersIN){

        $DATA = [];

        foreach ($TickersIN as $VAL)
        {

            // Проверка на доступностью тикера на покупку в бирже
            $checksymbol = $this->checksymbolenter($VAL['ticker']);

            if ($checksymbol == false)
            {
                // echo "<font color='red'>Тикер ".$VAL['ticker']." отключен  </font> <br>";
                continue;
            }


            if ($VAL['limit'] > $this->StartCapital){
                //  echo "<font color='red'>Тикер ".$VAL['ticker']." не проходит по стартовому капиталу  </font> <br>";
                continue;
            }


            $DATA[$VAL['ticker']] = $this->StartCapital/$VAL['price'];
        }



        return $DATA;
    }

    private function SitoStep1($StartArr, $Perekrestok,$ExchangeTickers, $exchange){

        $DATA = [];
        $STEP1 = [];
        // ШАГ-1 Получаем самый выгодный курс переход в монету перекрестка

        foreach ($StartArr as $key=>$value)
        {

            // Получаем ТИКЕР с БИРЖИ
            $TickerBirga = $key."/".$Perekrestok."";
            if (empty($ExchangeTickers[$TickerBirga]['bid'])) continue;
           //   show($ExchangeTickers[$TickerBirga]);
            //       echo "Тикер на бирже: ".$TickerBirga." <br>";
            $avgprice = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;
            $amountPerekrestok = $value*$avgprice;
            // echo "Берем монету ".$key." меняем ее на ".$Perekrestok." и получаем ".$amountPerekrestok." ".$Perekrestok." <br> ";

            // Фильтрация символ на ОБЪЕМ ТОРГОВ
           // echo "Объем торгов монетой: ".$TickerBirga." - ".$ExchangeTickers[$TickerBirga]['baseVolume']." <br>";
           // echo "Наше кол-во монеты: ".$value."<br>";

            if ($value > $ExchangeTickers[$TickerBirga]['baseVolume']/2)
            {
                //echo "<font color='red'>Тикер ".$TickerBirga." не проходит по объему торгов</font> <br> ";
                continue;
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
        $DATA['startcapital'] = $this->StartCapital;
        $DATA['startmoneta'] = $this->StartMoneta;
        $DATA['symbolbest'] = array_key_first($STEP1['result']);
        $DATA['symbolamount'] = $STEP1['amount'][$DATA['symbolbest']];
        $DATA['perekrestok'] = $Perekrestok;
        $DATA['amount'] = reset($STEP1['result']);

        if ($this->StartMoneta == $Perekrestok)
        {
            $DATA['amountstart'] = reset($STEP1['result']);
        }

        if ($this->StartMoneta != $Perekrestok)
        {
            $pricetick = $Perekrestok."/".$this->StartMoneta;
            $avgprice = ($ExchangeTickers[$pricetick]['bid']+$ExchangeTickers[$pricetick]['ask'])/2;
            $DATA['amountstart'] = $DATA['amount']*$avgprice;
        }


        // Добавление кол-во итоговой монеты в монете входа


        return $DATA;

    }

    private function SitoStep2($TickersOUT, $ExchangeTickers, $exchange, $Perekrestok){

        $DATA = [];

        // Проверка на доступностью тикера на покупку в бирже

        foreach ($TickersOUT as $VAL){


            $checksymbol = $this->checksymbolenter($VAL['ticker']);

            if ($checksymbol == false)
            {
              //  echo "<font color='red'>Тикер ".$VAL['ticker']." отключен  </font> <br>";
                continue;
            }
            if ($VAL['limit'] > $this->StartCapital){
               // echo "<font color='red'>Тикер ".$VAL['ticker']." не проходит по стартовому капиталу  </font> <br>";
                continue;
            }


            $TickerBirga = $VAL['ticker']."/".$Perekrestok;

            if (empty($ExchangeTickers[$TickerBirga]['bid'])) continue;

          //  echo "Тикер на бирже ".$TickerBirga."<br>";


            $amountperekrestok = 0;
            if ($this->StartMoneta == $Perekrestok) $amountperekrestok = $this->StartCapital;

            if ($this->StartMoneta != $Perekrestok)
            {
                $PerekrestokTicker = $Perekrestok."/".$this->StartMoneta;
                $PerekrestokAVGprice = ($ExchangeTickers[$PerekrestokTicker]['bid'] + $ExchangeTickers[$PerekrestokTicker]['ask'])/2;
                $amountperekrestok = $this->StartCapital/$PerekrestokAVGprice;
            }
            // Получаем сколько монеты получим после продажи


         //   $avgprice = ($ExchangeTickers[$TickerBirga]['bid']+$ExchangeTickers[$TickerBirga]['ask'])/2;
            $avgprice = $ExchangeTickers[$TickerBirga]['bid'];
            $amoumtMoneta = $amountperekrestok/$avgprice;


            // Фильтрация символ на ОБЪЕМ ТОРГОВ
            // echo "Объем торгов монетой: ".$TickerBirga." - ".$ExchangeTickers[$TickerBirga]['baseVolume']." <br>";
            // echo "Наше кол-во монеты: ".$amoumtMoneta."<br>";

            if ($amoumtMoneta > $ExchangeTickers[$TickerBirga]['baseVolume']/2)
            {
              //  echo "Кол-во монеты: ".$amoumtMoneta."<br>";
              //  echo "Объем в кол-ве монеты: ".$ExchangeTickers[$TickerBirga]['baseVolume']."<br>";
              //  echo "<font color='red'>Тикер ".$TickerBirga." не проходит по объему торгов</font> <br> ";
              //  echo "<hr>";
                continue;
            }

            $resultsale = $amoumtMoneta*$VAL['price'];

            //  echo "Работаем с тикером ".$VAL['ticker']."<br>";
            //  echo "Тикер на бирже ".$TickerBirga."<br>";
            //   echo "Цена ".$avgprice."<br>";
            //   echo "Кол-во актива ".$amoumtMoneta."<br>";

        //    $STEP1['amount'][$key] = $value;
        //    $STEP1['result'][$key] = $amountPerekrestok;

            $RESULT['amount'][$VAL['ticker']] = $amoumtMoneta;
            $RESULT['result'][$VAL['ticker']] = $resultsale;

        }

        if (empty($RESULT['result'])) return false;

        arsort($RESULT['result']);

        $DATA['exchange'] = $exchange;
        $DATA['startcapital'] = $this->StartCapital;
        $DATA['startmoneta'] = $this->StartMoneta;
        $DATA['perekrestok'] = $Perekrestok;
        $DATA['amountperekrestok'] = $amountperekrestok;
        $DATA['symbolbest'] = array_key_first($RESULT['result']);
        $DATA['symbolamount'] = $RESULT['amount'][$DATA['symbolbest']];
        $DATA['amount'] = reset($RESULT['result']);



        return $DATA;


    }

    private function GetEndArr($SITO2, $TickersOUT){

        $DATA = [];

        //show($SITO2);

        foreach ($TickersOUT as $VAL){

           // echo "Цена выхода: ".$VAL['price']."<br>";
            if (empty($SITO2[$VAL['ticker']])) continue;

          //  echo "Продаем ".$VAL['ticker']." по цене ".$VAL['price']."  <br>";

            $DATA[$VAL['ticker']] = $SITO2[$VAL['ticker']]*$VAL['price'];

        }

        arsort($DATA);

        return $DATA;

    }



    // ВСПОМОГАЮЩИЕ ФУНКЦИИ


    private function checksymbolenter($symbol)
    {

        if ($symbol == "USDT") return true;
        if ($symbol == "BTC") return true;
        if ($symbol == "ETH") return true;


        if (empty($this->FC[$symbol]))
        {
            //echo "Символа ".$symbol." нет на бирже! <br>";
            return false;
        }


        if (isset($this->FC[$symbol]['payin']) && $this->FC[$symbol]['payin'] == false) return false;
        if (isset($this->FC[$symbol]['payout']) && $this->FC[$symbol]['payout'] == false) return false;



        if ($this->FC[$symbol]['code'] == $symbol)
        {
            if (!empty($this->FC[$symbol]['info']['disabled']) && $this->FC[$symbol]['info']['disabled'] == 1) return false;

        }





        return true;

    }

    private function GetCurText($exchange){

        $DATA = [];

        $file = file_get_contents(WWW."/Cur".$exchange.".txt");     // Открыть файл data.json
        $DATA = json_decode($file,TRUE);              // Декодировать в массив


        if (empty($DATA)) return false;

        return $DATA;

    }

    private function LoadTickersBD($type)
    {

        $table = [];
        if ($type == "IN") $table = R::findAll("obmenin", 'WHERE method=?', [$this->StartMoneta]);
        if ($type == "OUT") $table = R::findAll("obmenout",'WHERE method=?', [$this->StartMoneta]);

        return $table;
    }

    private function GetTickerText($exchange){

        $file = file_get_contents(WWW."/Ticker".$exchange.".txt");     // Открыть файл data.json
        $MASSIV = json_decode($file,TRUE);              // Декодировать в массив
        return $MASSIV;

    }






}
?>