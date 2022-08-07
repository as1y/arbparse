<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">МОИ СПРЕДЫ</h5>

    </div>



    <div class="card-body">


        <?php

        //show($_SESSION['ulogin']);



        foreach ($DATA as $exname=>$MassivEX)
        {

         //   echo " - ".$exchangemassiv."<br>";

            \APP\core\base\View::RenderFinalExchange($MassivEX, $exname, $Base);



        }


        ?>



    </div>



</div>
