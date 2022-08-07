<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class ParseoutController extends AppController {
    public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $vremya = 50; // Секунд
    public $type = "OUT";
    public $Methods = [];
    public $debug = false;
    public $sleep = 2;

    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $this->layaout = false;
        $Panel =  new Panel();

      //  $this->Methods[] = "ETH";
  //      $this->Methods[] = "BTC";
        $this->Methods[] = "USDT";


        $this->ControlTrek();
        $this->StartTrek();

        // Инициализация парсера
        $aparser = new \Aparser('http://91.210.171.153:9091/API', '', array('debug'=>$this->debug));

        foreach ($this->Methods as $Method){

            echo "<h1>РАБОТА С МЕТОДОМ ".$Method."</h1>";

            // Таблица статуса работы парсера
            $StatusTable =  $this->GetStatusTable($Method);

            // Парсер не запущен. Формируем запрос;
            if (empty($StatusTable))
            {

                $ZAPROS = $this->GetZapros($Method);

                if (empty($ZAPROS))
                {
                    echo "<font color='green'>Информация актуальная. Парсить нет необходимости </font><br>";
                    // $this->StopTrek();
                    continue;
                }

                $taskUid = $aparser->addTask('20', 'BestOUT', 'text', $ZAPROS);
                $this->AddTaskBD($taskUid, $this->type, $Method);
                continue;

            }

            // Смотрим СТАТУС!
            $AparserIN =   $aparser->getTaskState($StatusTable['taskid']);

            if ($AparserIN['status'] == "work")
            {
                echo "<font color='#8b0000'>ПАРСИНГ IN В РАБОТЕ</font><br>";

            }
            if ($AparserIN['status'] == "completed"){

                echo "<font color='green'>ПАРСИНГ IN ЗАКОНЧЕН</font><br>";

                $result = $aparser->getTaskResultsFile($StatusTable['taskid']);
                $content = file_get_contents($result);
                $content = str_replace(" ", "", $content); // Убираем пробелы
                $content = explode("\n", $content);

                // ОБНОВЛЯЕМ ТАБЛИЦУ
                // show($content);

                // Обновляем в БД цены
                $this->RenewTickers($content, $this->type, $Method);

                // Очищаем статус таблицу
                R::trash($StatusTable);


            }

            echo "<hr>";

        }

        $sleep = rand($this->sleep, $this->sleep*2);
        sleep($sleep);


        $this->StopTrek();




//        $this->set(compact(''));

    }



    private function GenerateURL($Method)
    {

        $arr['symbol'] = "BTC";
        $arr['uri'] = "bitcoin";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "BCH";
        $arr['uri'] = "bitcoin-cash";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "BTG";
        $arr['uri'] = "bitcoin-gold";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ETH";
        $arr['uri'] = "ethereum";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ETC";
        $arr['uri'] = "ethereum-classic";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "LTC";
        $arr['uri'] = "litecoin";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "XRP";
        $arr['uri'] = "ripple";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "XMR";
        $arr['uri'] = "monero";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "DOGE";
        $arr['uri'] = "dogecoin";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "MATIC";
        $arr['uri'] = "polygon";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "DASH";
        $arr['uri'] = "dash";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ZEC";
        $arr['uri'] = "zcash";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "XEM";
        $arr['uri'] = "nem";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "NEO";
        $arr['uri'] = "neo";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "EOS";
        $arr['uri'] = "eos";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ADA";
        $arr['uri'] = "cardano";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "XLM";
        $arr['uri'] = "stellar";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "TRX";
        $arr['uri'] = "tron";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "WAVES";
        $arr['uri'] = "waves";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "OMG";
        $arr['uri'] = "omg";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "BNB";
        $arr['uri'] = "binance-coin";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "BAT";
        $arr['uri'] = "bat";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "QTUM";
        $arr['uri'] = "qtum";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "LINK";
        $arr['uri'] = "chainlink";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ATOM";
        $arr['uri'] = "cosmos";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "XTZ";
        $arr['uri'] = "tezos";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "DOT";
        $arr['uri'] = "polkadot";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "UNI";
        $arr['uri'] = "uniswap";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "RVN";
        $arr['uri'] = "ravencoin";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "SOL";
        $arr['uri'] = "solana";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "VET";
        $arr['uri'] = "vechain";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ALGO";
        $arr['uri'] = "algorand";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "MKR";
        $arr['uri'] = "maker";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "AVAX";
        $arr['uri'] = "avalanche";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "YFI";
        $arr['uri'] = "yearn-finance";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "USDT";
        $arr['uri'] = "tether-erc20";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "BSV";
        $arr['uri'] = "bitcoin-sv";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ZRX";
        $arr['uri'] = "zrx";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ICX";
        $arr['uri'] = "icon";
        $SYMBOLS[] = $arr;

        $arr['symbol'] = "ONT";
        $arr['uri'] = "ontology";
        $SYMBOLS[] = $arr;


        $first = "";
        if ($Method == "BTC") $first = "bitcoin";
        if ($Method == "USDT") $first = "tether-trc20";
        if ($Method == "ETH") $first = "ethereum";
        if ($Method == "LTC") $first = "litecoin";

        if ($Method == "EOS") $first = "eos";
        if ($Method == "SOL") $first = "solana";
        if ($Method == "XMR") $first = "monero";
        if ($Method == "XRP") $first = "ripple";
        if ($Method == "TRX") $first = "tron";
        if ($Method == "DOGE") $first = "dogecoin";
        if ($Method == "XEM") $first = "nem";
        if ($Method == "XTZ") $first = "tezos";

        /*
        if ($Method == "QIWI") $first = "qiwi";
        if ($Method == "ADVRUB") $first = "advanced-cash-rub";
        */


        // show($SYMBOLS);

        $URL = [];
        foreach ($SYMBOLS as $key=>$value)
        {


            $uri = "https://www.bestchange.ru/".$value['uri']."-to-".$first.".html";

            if ($value['uri'] == $first) continue;


            $URL[$uri] = $value['symbol'];
            //   $headers = @get_headers($uri);
            //   echo  $headers[0]."<br>";
        }

        return $URL;

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
    private function GetZapros($Method){

        $ZAPROS = [];

        $obmen =  $this->GetBaseTable($Method); // Создаем BaseTickers
        if (empty($obmen)) $this->CreateTable($Method); // Если таблица пустая, то создаем


        // Проверяем созданную таблицу страниц
        foreach ($obmen as $key=>$val)
        {

            $proshlo = time() - $val['time'];

            //    echo "Method: ".$val['method']."<br>";
            //    echo "Ticker: ".$val['ticker']."<br>";
            //    echo "Прошло: ".$proshlo."<br>";

            if ($proshlo > $this->vremya)
            {
                echo "<i>Цена не актуальная! Пора обновлять</i><br>";
                $ZAPROS[] = $val['url'];
            }

        }

        return $ZAPROS;

    }
    private function CreateTable($Method)
    {

        $MassivTicker = $this->GenerateURL($Method);

        // СОЗДАЕМ ТАБЛИЦУ НА КИВИ ВХОД
        foreach ($MassivTicker as $url => $ticker)
        {
            $ARR['method'] = $Method;
            $ARR['url'] = $url;
            $ARR['ticker'] = $ticker;
            $this->AddARRinBD($ARR, "obmenout");
            //echo "<b><font color='green'>Добавили запись</font></b>";
            // Добавление ТРЕКА в БД
        }



        echo "<hr>";
        echo "<font color='green'>Таблица с ценами из обменников создана!!</font> <br>";

        return true;

    }
    private function RenewTickers($content, $type, $Method)
    {


        echo "МАССИВ ПАРСИНГА<br>";

        // Преобразовываем массив в примемлемый вид
        $MASSIV = [];
        foreach ($content as $key=>$value)
        {
            if (empty($value)) continue;
            $value = explode(";", $value);
            //           show($value);
            $MASSIV[$value[0]][] = $value[1];
            $MASSIV[$value[0]][] = $value[2];
            $MASSIV[$value[0]][] = $value[3];
            $MASSIV[$value[0]][] = $value[4];
         }


        $obmen = $this->GetBaseTable($Method);


        //  show($MASSIV);


        // Добавляем в БД данные из спарсенного контента!
        foreach ($obmen as $ticker)
        {

            if (empty($MASSIV[$ticker['url']])) continue;
            if ($MASSIV[$ticker['url']][0] == "none") continue;

            $ticker->price = $MASSIV[$ticker['url']][0];
            $ticker->limit = $MASSIV[$ticker['url']][1];
            $ticker->redirect = $MASSIV[$ticker['url']][3];

            if ($MASSIV[$ticker['url']][0] == 1)
            {
                $ticker->price = 1/$MASSIV[$ticker['url']][2];
            }


            $ticker->time = time();

            R::store($ticker);

        }


        return true;


    }
    private function AddTaskBD($taskid, $type, $Method)
    {

        $ARR = [];
        $ARR['taskid'] = $taskid;
        $ARR['type'] = $type;
        $ARR['method'] = $Method;

        $this->AddARRinBD($ARR, "statustable");
        echo "<b><font color='green'>Добавили запись</font></b>";
        // Добавление ТРЕКА в БД

    }
    private function GetBaseTable($Method)
    {
        $table = R::findAll("obmenout", "WHERE method=?", [$Method]);
        return $table;
    }
    private function GetStatusTable($Method)
    {

        $table = R::findOne("statustable", "WHERE type=? AND method=?", [$this->type, $Method]);
        return $table;
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