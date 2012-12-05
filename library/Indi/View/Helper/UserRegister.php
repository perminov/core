<?php
class Indi_View_Helper_UserRegister extends Indi_View_Helper_Abstract{
	public function userRegister(){
		if ($this->view->post['register']) {
			$error =  $this->view->error;
			$field = @key($error);
			$post = $this->view->post;
			$result = $this->view->result;
		}
		$xhtml = '
    <form action="" method="post">
	  <font color="green" style="position: relative; left: -40px;">' . $result . '</font>	
      <div class="register-form-wrapper">
        <h2 class="block-title">Регистрация</h2>
        <div id="edit-login-wrapper" class="form-item' . ($field == 'title' ? ' error' : '') . '">
          <label for="login">Логин</label>
          <div class="form-text-wrapper">
			<input id="login" type="text" name="title" value="' . $post['title'] . '">
			<div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>
        </div>
        <div id="edit-email-reg-wrapper" class="form-item' . ($field == 'email' ? ' error' : '') . '">
          <label for="email">Эл. почта</label>
          <div class="form-text-wrapper">
            <input id="email" type="text" name="email" value="' . $post['email'] . '">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>
        </div>
        <div id="edit-objectTitleCity-wrapper" class="form-item' . ($field == 'city' ? ' error' : '') . '">
          <label for="objectTitleCity">Город</label>
          <div class="form-text-wrapper">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
			<input type="text" id="city" name="city" autocomplete="off" class="form-text" style="z-index: 0;" value="' . $post['city'] . '"/>
		  </div>
        </div>
        <div id="edit-pass1-wrapper" class="form-item' . ($field == 'password' ? ' error' : '') . '">
          <label for="pass1">Пароль</label>
          <div class="form-text-wrapper">
            <input id="pass1" type="password" name="password" value="' . $post['password'] . '">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>
        </div>
        <div id="edit-pass1-wrapper" class="form-item' . ($field == 'passwordConfirm' ? ' error' : '') . '">
          <label for="pass2">Подтвердите пароль</label>
          <div class="form-text-wrapper">
            <input id="pass2" type="password" name="passwordConfirm" value="' . $post['passwordConfirm'] . '">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>
        </div>
        <div id="edit-kap-wrapper" class="form-item captcha-item' . ($field == 'captcha' ? ' error' : '') . '">
          <label for="kap">Цифры с картинки</label>
          <div id="kpt" style="display:inline;">
            <img align="absmiddle" src="/misc/captcha/type/register/?' . rand(0, 100) . '" alt="" width="100" height="60" id="pic">
          </div>
          <!--Начало изменений -->
          <div id="captha-actions" class="' . ($field == 'captcha' ? ' error' : '') . '">
            <div class="refresh-wrapper">
              <a href="#" class="refresh-captcha" onclick="$(\'#pic\').attr(\'src\',\'/misc/captcha/type/register/?12\'); return false;"><span>Обновить</span></a>
            </div>
		    <div class="form-text-wrapper">
			  <input id="kap" type="text" value="" maxlength="30" size="15" name="captcha">
			  <div class="error-message"><span>' . $error[$field] . '</span></div>
		    </div>
          </div>
          <!--Конец изменений -->
        </div>
	  </div>
	  
      <!--<div class="social-login">
        <h2 class="block-title">Через социальную сеть</h2>
        <ul>
          <li><a href="#" class="login-vk" onclick="VK.Auth.login(vkAuth);">Вконтакте</a></li>
          <li><a href="#" class="login-fb" onclick="$(\'a.fb_button_medium\').click();">Facebook</a></li>
          <li><a href="#" class="login-mr" onclick="$(\'a.mrc__connectButton\').click();">@mail.ru</a></li>
		</ul>
      </div>	-->
      <input id="subbutton" type="button" value="Зарегистрироваться" name="register" onclick="registerAttempt();return false;" class="form-submit">
    </form>
  </div>
  <script>
  $("div.error-message").each(function(index){
	if ($(this).parent().parent().hasClass("error") == false) $(this).hide();
  });
  function registerAttempt(){
	var title = $("#login").val();
	var email = $("#email").val();
	var city = $("#city").val();
	var password = $("#pass1").val();
	var passwordConfirm = $("#pass2").val();
	var captcha = $("#kap").val();
	$.post("./", {title: title, email: email, city: city, password: password, passwordConfirm: passwordConfirm, captcha: captcha, 
			register: true}, function(data){$("#regForm").html(data);}
	)
  }
  </script>
		';
		if ($result) {
			$xhtml = '
			<div id="reg-success" class="modal-window registration-success">
			<div class="modal-content">
			  <h2 class="modal-title">Регистрация</h2>
			  <p>Спасибо за регистрацию!</p>
			  <p>Вы успешно зарегистрированы. Для активации аккаунта воспользуйтесь ссылкой, которая отослана Вам на e-mail.</p>
			</div>
			</div>
			';
		}
		return $xhtml;
	}
}