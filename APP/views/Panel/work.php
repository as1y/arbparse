&#xFEFF;

<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">РАБОЧАЯ ОБЛАСТЬ</h5>

    </div>



    <div class="card-body">


        Направление: <?=$_GET['type']?> <br>

        <?php

     //   show($_GET);

        $TickerExchange = \APP\models\Panel::GetTickerText($_GET['exchange']);

            //show($Scanload);
        ?>




        <? if($_GET['type'] == "enter"):?>


            <h2>ШАГ-1: ПЕРЕХОД В ОБМЕННИК</h2>
            <a href="<?=$Scanload['redirect']?>" type="button" class="btn btn-success" target="_blank"><b>Перейти в обменник</b></a> |         <a href="/panel/" type="button" class="btn btn-danger"><i class="icon-alert mr-2"></i>НАЗАД</a> <br>
            <br>

            Вводим сумму которую хотим отдать в <b><?=$Scanload['method']?></b><br>
            Вводим кол-во получаемоемой монеты <b><?=$Scanload['ticker']?></b><br>
            <div class="form-group">
                <input type="text" id="moneta"   value="0" class="form-control" >
            </div>

            Примерная полученная сумма с продажи <b><?=$Scanload['ticker']?></b> будет: <b><font color="#006400"> <div id="exit"></div> </font></b>

            <font color="#8b0000"> Убеждаемся, что сумма продажи выше чем сумма покупки! </font><br>



        <?php
            $ticker = $Scanload['ticker']."/".$Scanload['method'];
            $avgprice = ($TickerExchange[$ticker]['bid']+$TickerExchange[$ticker]['ask'])/2;
            ?>
            Цена на бирже: <div id="exprice"><?=$avgprice?></div><br>

            <b>Кошелек на бирже <?=$Scanload['method']?> </b> <br>
            <?php
            $WalletAddr = \APP\models\Panel::GetWalletAddr($_GET['exchange'],$Scanload['method']);
            show($WalletAddr);
            ?>


            <b>Кошелек на бирже <?=$Scanload['ticker']?> </b> <br>
            <?php
            $WalletAddr = \APP\models\Panel::GetWalletAddr($_GET['exchange'],$Scanload['ticker']);
            show($WalletAddr);
            ?>




            <a href="<?=$Scanload['url']?>"  target="_blank"><b>Еще варианты на BestChange</b></a><br>




        <?php endif;?>




        <? if($_GET['type'] == "exit"):?>

            <h3>ШАГ-1: СОЗДАЕМ СДЕЛКУ В ОБМЕННИКЕ</h3>
            ОТДАЕМ: <?=$Scanload['ticker']?><br>
            ПОЛУЧАЕМ: <?=$Scanload['method']?><br>

            <a href="<?=$Scanload['redirect']?>" type="button" class="btn btn-success" target="_blank"><b>Перейти в обменник</b></a><br>
        <br>
            <a href="<?=$Scanload['url']?>"  target="_blank"><b>Еще варианты на BestChange</b></a><br>


        <?php endif;?>



    </div>

</div>


<script>

    $("#moneta").change(function() {

        let exitamount = $("#moneta").val();

        let exprice = $("#exprice").text();

      let final = exitamount*exprice;


        $("#exit").text(final);

    });

</script>
