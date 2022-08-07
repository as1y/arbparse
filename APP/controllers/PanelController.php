<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Operator;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class PanelController extends AppController {
	public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $EXCHANGES = [];



    public function indexAction()
    {

        $Panel =  new Panel();
        $META = [
            'title' => 'Панель BURAN',
            'description' => 'Панель BURAN',
            'keywords' => 'Панель BURAN',
        ];
        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);
        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        // Браузерная часть


        $this->EXCHANGES[] = "Binance";
        $this->EXCHANGES[] = "Poloniex";

        $Base = "USDT";




        $DATA = [];

        foreach ($this->EXCHANGES as $key=>$exchange)
        {
            $MassivWork =  $Panel->GetArrWorkExchange($exchange, $Base);
            $DATA[$exchange] = $MassivWork;
            //$this->RenderFinalExchange($MassivWork, $exchange, "USDT");
        }


      //  show($DATA);


        $this->set(compact('DATA','Base'));



    }


    public function workAction(){

        $Panel =  new Panel();
        $META = [
            'title' => 'Панель BURAN',
            'description' => 'Панель BURAN',
            'keywords' => 'Панель BURAN',
        ];
        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);
        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        // Браузерная часть


        $Scanload =  $Panel->Loadscan($_GET['scanid'], $_GET['type']);



        $this->set(compact('Scanload'));



    }



    public function profileAction(){
        $Panel =  new Panel();


        $META = [
            'title' => 'Вывести средства',
            'description' => 'Вывести средства',
            'keywords' => 'Вывести средства',
        ];
        \APP\core\base\View::setMeta($META);

        $BREADCRUMBS['DATA'][] = ['Label' => "Вывести средства"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);



        $ASSETS[] = ["js" => "/global_assets/js/demo_pages/form_actions.js"];
        $ASSETS[] = ["js" => "/assets/js/form_inputs.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/selects/select2.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/styling/uniform.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);


        $requis = json_decode($Panel::$USER->requis, true);
        if (empty($requis)) $requis = [];



        if ($_POST && $_GET['action'] == "changerequis"){




            $Panel->addrequis($_POST);


            $_SESSION['success'] .= "Успешно сохранено! <br>";



            redir();


        }




        $this->set(compact('requis'));

    }





}
?>