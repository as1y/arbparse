<?php
namespace APP\core\base;
class View {
	public $route = [];
	public $view;
	public $layaout;
    static $assets = [];
	static $meta = ["title"=> APPNAME, "desc"=> "", "keywords"=> "", "H1" =>""];
    static $Breadcrumbs = ["HOME" => [], "DATA" => []];

	public function __construct($route, $layaout='', $view=''){
		$this->route = $route;

		if ($layaout === false){
			$this->layaout = false;
		} else{
			$this->layaout = $layaout ?: CONFIG['LAYOUT'];
		}
		$this->view = $view;
	}


	public function render($DATA, $return = false){


		if(is_array($DATA)) extract($DATA);
		$file_view = WWW."/APP/views/".$this->route['controller']."/".$this->view.".php";
		ob_start();
		if(is_file($file_view)){
			require $file_view;
		}else{
            if (ERRORS == 1) echo "<p><b>Не найден вид </b>$file_view</p>";
		}
		$content = ob_get_clean();
		$content = utf8_encode($content);

		if (false !== $this->layaout){
		    $file_layaout = WWW."/APP/views/layaouts/".$this->layaout.".php";
			if(is_file($file_layaout)){
			    if(!$return){
                    require $file_layaout;
                }else{
			        ob_start();
                    require $file_layaout;
			        return ob_get_clean();
                }
			}else{

			    if (ERRORS == 1) echo "<p><b>Не найден ШАБЛОН ".$this->layaout."</b></p>";


			}
		}
	}


	public static function getMeta(){
		echo '<title>'.self::$meta['title'].'</title>
			<meta name="description" content="'.self::$meta['desc'].'" />
			<meta name="keywords" content="'.self::$meta['keywords'].'" />';
	}


    public static function getH1(){
	    echo self::$meta['H1'];
    }


    public static function setMeta($META){

        if (!empty($META['title'])) self::$meta['title'] = $META['title'];
        if (!empty($META['description'])) self::$meta['description'] = $META['description'];
        if (!empty($META['keywords'])) self::$meta['keywords'] = $META['keywords'];
        if (!empty($META['H1'])) self::$meta['H1'] = $META['H1'];


    }


    public static function setBreadcrumbs($Breadcrumbs){

        if (!empty($Breadcrumbs['HOME'])) self::$Breadcrumbs['HOME'] = $Breadcrumbs['HOME'];
        if (!empty($Breadcrumbs['DATA'])) self::$Breadcrumbs['DATA'] = $Breadcrumbs['DATA'];

    }




    public static function setAssets($DATA = []){
        self::$assets = $DATA;
    }

    public static function getAssets($type){


	    // выводим тут асссеты.
        foreach (self::$assets as $key=>$val){

            if (key($val) == "js") echo '   <script src="'.array_shift($val).'"></script>'.PHP_EOL;
            if (key($val) == "css") echo '  <link href="'.array_shift($val).'" rel="stylesheet" type="text/css">'.PHP_EOL;

        }






    }


    public static function getBreadcrumbs(){

        if ( self::$Breadcrumbs['HOME'] == false) {
          //  echo "<br>";
            return false;
        }

        if (!isset(self::$Breadcrumbs['HOME']['Label'])) self::$Breadcrumbs['HOME']['Label'] = self::$meta['title'];
        if (!isset(self::$Breadcrumbs['HOME']['Url'])) self::$Breadcrumbs['HOME']['Url'] = "/";



        ?>

                    <b>
                        <a href="<?=self::$Breadcrumbs['HOME']['Url'];?>"><?=self::$Breadcrumbs['HOME']['Label'];?></a>
                    </b>



        <?php foreach(self::$Breadcrumbs['DATA'] as $val):?>


            <?php if (!empty($val['Url'])):?>


                <i class="fa fa-angle-right"></i>   <a href="<?=$val['Url'];?>"><?=$val['Label'];?></a>

                            <?php else:?>
                <i class="fa fa-angle-right"></i></span><?=$val['Label']?>
            <?php endif;?>



        <?php endforeach;?>












        <?php

    }




    public function RenderFinalExchange($MassivEX, $exname, $Method){


        echo "<h3>Вход - ".$exname."</h3>";

        foreach ($MassivEX['enter'] as $key=>$val){

            ?>
            <a href="/panel/work/?scanid=<?=$val['scanid']?>&exchange=<?=$exname?>&type=enter" type="button" class="btn btn-warning"><i class="icon-alert mr-2"></i>ВЗЯТЬ В РАБОТУ</a> <br>
            <?php


            echo "<b>1.</b>(".$val['scanid'].") Через <a href='".$val['url']."' target='_blank'>BestChange</a> меняем <b>".$Method."</b> на <b>".$val['symbol']."</b>. Цена ~ ".$val['enterprice']."  Вводим кошелек для зачисления биржи <b>".$exname."</b> <br>";
       //     echo " <a href='".$val['redirect']."' target='_blank'><b>ССЫЛКА НА ОБМЕННИК</b></a>  <br>";
            echo "<b>2.</b> На бирже <b>".$exname."</b> монету <b>".$val['symbol']."</b>  меняем на <b>".$Method."</b>  Цена ~ ".$val['exitprice']." <br>";
            echo "<b>3.</b> Зарабатываем <b> <font color='green'>".$val['spred']."% </font></b> с круга <br>";
            echo "<b>4.</b> МИН: <b> <font color='#b8860b'>".$val['limit']."</font></b> ".$Method." <br>";
            echo "<hr>";

        }

        echo "<h3>Выход - ".$exname."</h3>";

        foreach ($MassivEX['exit'] as $key=>$val){

            ?>
            <a href="/panel/work/?scanid=<?=$val['scanid']?>&exchange=<?=$exname?>&type=exit" type="button" class="btn btn-warning"><i class="icon-alert mr-2"></i>ВЗЯТЬ В РАБОТУ</a> <br>
            <?php

            echo "<b>1.</b>(".$val['scanid'].") На бирже <b>".$exname."</b> покупаем монету <b>".$val['symbol']."</b>  за  <b>".$Method."</b>  <b>Рекомендумая цена</b>  ~  ".$val['enterprice']." </b> <br>";
            echo "<b>2.</b> Через <a href='".$val['url']."' target='_blank' >BestChange</a> меняем <b>".$val['symbol']."</b> на <b>".$Method."</b>. <b>Рекомендумая цена</b>  ~  ".$val['exitprice']." </b> Вводим кошелек для зачисления биржи <b>".$exname."</b><br>";
      //      echo " <a href='".$val['redirect']."' target='_blank'><b>ССЫЛКА НА ОБМЕННИК</b></a>  <br>";
            echo "<b>3.</b> Зарабатываем <b> <font color='green'>".$val['spred']."% </font></b> с круга <br>";
            echo "<b>4.</b> МИН: <b> <font color='#b8860b'>".$val['limit']."</font></b> ".$Method." <br>";

            echo "<hr>";

        }






        return true;
    }




}
?>