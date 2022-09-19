<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class ParseyobitController extends AppController {
    public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $type = "PAY";

    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $this->layaout = false;
        $Panel =  new Panel();

        $this->ControlTrek();
        $this->StartTrek();

        echo "<h2>ПАРСИНГ ТИКЕРОВ PAYEER</h2>";


        $RESULT = $this->GetAllPairs();

        $MASSIV = [];
        foreach ($RESULT['pairs'] as $key=>$VAL){

            $key2 = str_replace("_", "/", $key);
            $MASSIV[$key2] = $VAL;
          //  echo $key2."<br>";

        }

     //     show($MASSIV);

        $this->WriteTickers("Payeer", $MASSIV);



//        $exchange = new \ccxt\exmo (array ('timeout' => 30000));
//        $DATA = $exchange->fetchCurrencies();
//        show($DATA);


    //    show($TICKERS);

       // exit("11");



        // Проверка наличие файла


        //БИНАНС


        // Обновление



        $this->StopTrek();




//        $this->set(compact(''));

    }



    private function GetAllPairs()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://payeer.com/api/trade/ticker");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);


        return $response;

    }


    private function GetTickerList(){

         $table = [];
         $table = R::findAll("obmenin", 'WHERE method=?', ["USDT"]);


         return $table;

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



        if (!file_exists("Ticker".$exchangename.".txt"))
        {
            $fd = fopen("Ticker".$exchangename.".txt", 'w') or die("не удалось создать файл");
            fwrite($fd, "");
            fclose($fd);
        }

        $data = json_encode($Tickers);

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