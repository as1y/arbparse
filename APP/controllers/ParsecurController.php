<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class ParsecurController extends AppController {
    public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";

    // Типа парсинга записываем для БД
    public $type = "Currency";


    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $this->layaout = false;
        $Panel =  new Panel();

        $this->ControlTrek();
        $this->StartTrek();

        echo "<h2>ПАРСИНГ СОСТОЯНИЕ МОНЕТ</h2>";


        // КУРЕНСИ WAVES
        $exchange = new \ccxt\wavesexchange (array ('timeout' => 30000));
        $DATA = $exchange->fetchMarkets();

        $MASS = [];
        foreach ($DATA as $key=>$val){

            if (!in_array($val['base'], $MASS)){
                $MASS[] = $val['base'];
               // echo "Добавили элмент".$val['base']."<br>";
            }
        }

        $FEES = [];
        foreach ($MASS as $key=>$val)
        {

            $url = "https://api.waves.exchange/v1/withdraw/currencies/".$val;
           $res = fCURL($url);
           $res = json_decode($res, true);
           if (!empty($res['fees']['flat'])) $FEES[$val]['fee'] = $res['fees']['flat'];
          // show($res);
        }

        $this->WriteTickers("Waves", $FEES);


        //////////////////////////////////

        $exchange = new \ccxt\binance (array ('timeout' => 30000));
        $DATA = $exchange->fetchCurrencies();
        $this->WriteTickers("Binance", $DATA);


        $exchange = new \ccxt\hitbtc (array ('timeout' => 30000));
        $DATA = $exchange->fetchCurrencies();
        $this->WriteTickers("Hitbtc", $DATA);

        $exchange = new \ccxt\poloniex (array ('timeout' => 30000));
        $DATA = $exchange->fetchCurrencies();
        $this->WriteTickers("Poloniex", $DATA);



        $exchange = new \ccxt\exmo (array ('timeout' => 30000));
        $DATA = $exchange->fetchCurrencies();
        $this->WriteTickers("Exmo", $DATA);

        /*
        $exchange = new \ccxt\deribit (array ('timeout' => 30000));
        $DATA = $exchange->fetchCurrencies();
        $this->WriteTickers("Deribit", $DATA);
        */



        // Проверка наличие файла


        //БИНАНС


        // Обновление



        $this->StopTrek();




//        $this->set(compact(''));

    }


    private function ControlTrek(){
        $tbl = R::findOne("trekcontrol", "WHERE type =?", [$this->type]);
        if (empty($tbl)) return true;
        if ($tbl['work'] == 1)
        {
            echo "Процесс в работе. Новый не запускаем<br>";
            exit();
        }
        return true;
    }
    private function StartTrek(){
        $tbl = R::findOne("trekcontrol", "WHERE type =?", [$this->type]);
        if (empty($tbl)){

            $ARR['type'] = $this->type;
            $ARR['work'] = 1;
            $this->AddARRinBD($ARR, "trekcontrol");
            return true;
        }

        $tbl->work = 1;
        R::store($tbl);
        return true;
    }
    private function StopTrek(){
        $tbl = R::findOne("trekcontrol", "WHERE type =?", [$this->type]);
        $tbl->work = 0;
        R::store($tbl);
        exit();
    }

    private function WriteTickers($exchangename, $Tickers){


        if (!file_exists("Cur".$exchangename.".txt"))
        {
            $fd = fopen("Cur".$exchangename.".txt", 'w') or die("не удалось создать файл");
            fwrite($fd, "");
            fclose($fd);
        }

        $data = json_encode($Tickers);

        file_put_contents("Cur".$exchangename.".txt", $data);

        return true;

    }



    private function AddARRinBD($ARR, $BD = false)
    {

        $tbl = R::dispense($BD);
        //ДОБАВЛЯЕМ В ТАБЛИЦУ

        foreach ($ARR as $name => $value) {
            $tbl->$name = $value;
        }

        $id = R::store($tbl);

        echo "<font color='green'><b>ДОБАВИЛИ ЗАПИСЬ В БД!</b></font><br>";

        return $id;


    }





}
?>