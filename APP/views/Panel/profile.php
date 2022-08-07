
<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">ЗАЯВКА НА ВЫВОД</h5>

    </div>

    <div class="card-body">

        <div class="row">


            <div class="col-md-6">
                <h2>КЛЮЧИ</h2>

                <form action="/panel/profile/?action=changerequis" method="post">
                    <div class="form-group">
                        <label>Binance </label>
                        <input type="text" name="apiBinance" value="<?=(empty($requis['apiBinance'])) ? "" : $requis['apiBinance'] ?>" placeholder="ApiKey" class="form-control">
                        <input type="text" name="keyBinance" value="<?=(empty($requis['keyBinance'])) ? "" : $requis['keyBinance'] ?>" placeholder="SecretKey" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Poloniex </label>
                        <input type="text" name="apiPoloniex" value="<?=(empty($requis['apiPoloniex'])) ? "" : $requis['apiPoloniex'] ?>" placeholder="ApiKey" class="form-control">
                        <input type="text" name="keyPoloniex" value="<?=(empty($requis['keyPoloniex'])) ? "" : $requis['keyPoloniex'] ?>" placeholder="SecretKey" class="form-control">
                    </div>




                    <button  type="submit" class="btn btn-warning"><i class="icon-checkmark mr-2"></i>СОХРАНИТЬ РЕКВИЗИТЫ</button>

                </form>


            </div>






            </div>


        </div>

    </div>



</div>
