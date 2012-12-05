<?php
class Indi_View_Helper_UserLogin extends Indi_View_Helper_Abstract{
	public function userLogin(){
		if ($this->view->post['login']) {
			$error =  $this->view->error;
			$field = @key($error);
			$post = $this->view->post;
		}
		$xhtml = '
    <div class="modal-content">
      <form class="modal-login-form" action="" method="post" onsubmit="return false;">
        <div id="edit-email-enter-wrapper" class="form-item' . ($field == 'title' ? ' error' : '') . '">
          <label for="email_enter">Логин</label>
          <div class="form-text-wrapper">
			<input id="email_enter" type="text" name="email" value="' . ($post['title'] ? $post['title'] : $_COOKIE['login']) . '">
			<div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>	
        </div>
        <div id="edit-pass3-wrapper" class="form-item' . ($field == 'password' ? ' error' : '') . '">
          <label for="pass3">Пароль</label>
          <div class="form-text-wrapper">
			<input id="pass3" type="password" name="password" value="' . $_COOKIE['password'] . '">
			<div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>	
          <a class="password-recovery-link" href="#">Забыли пароль</a>
          <a class="registration-link" href="/users/registration/">Регистрация</a>
        </div>
        <!--Начало изменений -->
        <div id="edit-remember-me">
          <input type="checkbox" name="remember" id="remember"'. ($_COOKIE['login'] ? ' checked="checked"' : '') . '/>
          <label for="rememberme">Запомнить меня</label>
        </div>
        <!--Конец изменений -->
        <input id="subbutton" type="submit" value="Войти" name="login" onclick="loginAttempt();return false;" class="form-submit">
      </form>
	  <script>
	  $("div.error-message").each(function(index){
		if ($(this).parent().parent().hasClass("error") == false) $(this).hide();
	  });
	  </script>
      <!--<div class="modal-social-login">
        <h2 class="block-title">Войти как пользователь</h2>
        <ul class="inline-list">
          <li><a href="#" class="login-vk" onclick="VK.Auth.login(vkAuth);">Вконтакте</a></li>
          <li><a href="#" class="login-fb" onclick="$(\'a.fb_button_medium\').click();">Facebook</a></li>
          <li><a href="#" class="login-mr" onclick="$(\'a.mrc__connectButton\').click();">@mail.ru</a></li>
        </ul>
      </div>-->
    </div>
  </div>
  <script>
   function loginAttempt(){
	var title = $("#email_enter").val();
	var password = $("#pass3").val();
	var remember = $("#remember").attr("checked");
	$.post("./", {title: title, password: password, login: true, remember: remember}, function(data){$("#authDepending").html(data);}
	)
  }
  </script>
 ';
		return $xhtml;
	}
}