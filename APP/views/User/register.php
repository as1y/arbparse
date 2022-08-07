<!-- Registration form -->
<form action="/user/register" method="post" class="login-form" style="width: 30rem">
    <div class="card mb-0">
        <div class="card-body">
            <div class="text-center mb-3">
                <h5 class="mb-0">Регистрация</h5>
                <span class="d-block text-muted"><?=APPNAME?></span>
            </div>


            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="text" name="username" value="<?=isset($_SESSION['form_data']['username']) ? h($_SESSION['form_data']['username']) : '';?>" class="form-control" placeholder="Nickname">
                <div class="form-control-feedback">
                    <i class="icon-user-check text-muted"></i>
                </div>
            </div>


            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="email" name="email" value="<?=isset($_SESSION['form_data']['email']) ? h($_SESSION['form_data']['email']) : '';?>" class="form-control" placeholder="Email">
                <div class="form-control-feedback">
                    <i class="icon-mention text-muted"></i>
                </div>
            </div>

            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="password" name="password" value="<?=isset($_SESSION['form_data']['password']) ? h($_SESSION['form_data']['password']) : '';?>" class="form-control" placeholder="Пароль">
                <div class="form-control-feedback">
                    <i class="icon-user-lock text-muted"></i>
                </div>
            </div>

            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="password" name="password2" value="<?=isset($_SESSION['form_data']['password2']) ? h($_SESSION['form_data']['password2']) : '';?>" class="form-control" placeholder="Повторите пароль">
                <div class="form-control-feedback">
                    <i class="icon-user-lock text-muted"></i>
                </div>
            </div>

            <div class="form-group text-center text-muted content-divider">
                <span class="px-2">Дополнительно</span>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="terms"  checked class="form-input-styled" data-fouc>
                        Принимаю <a href="#">условия использования</a>
                    </label>
                </div>
            </div>



            <button type="submit" class="btn bg-teal-400 btn-block">Регистрация <i class="icon-circle-right2 ml-2"></i></button>
        </div>
    </div>
</form>


<?php if (isset($_SESSION['form_data']) ) unset($_SESSION['form_data'])?>





