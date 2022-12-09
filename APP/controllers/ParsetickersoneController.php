<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class ParsetickersoneController extends AppController {
    public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $type = "TICKERS1";

    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $this->layaout = false;
        $Panel =  new Panel();

       // $this->ControlTrek();
      //  $this->StartTrek();

        echo "<h2>ПАРСИНГ ТИКЕРОВ</h2>";

        // Перезаписывать на диск???

        //$this->TickersBinance();

        $this->TickersPoloniex();



        //$this->StopTrek();




//        $this->set(compact(''));

    }


    private function TickersPoloniex(){

        $exchange = new \ccxt\poloniex (array ('timeout' => 30000));
        $DATA = $exchange->fetch_tickers();
        if (!empty($DATA)) $this->WriteTickers("Poloniex", $DATA);
     
        return true;

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