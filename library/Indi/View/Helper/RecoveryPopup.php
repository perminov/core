<?php
class Indi_View_Helper_RecoveryPopup extends Indi_View_Helper_Abstract{
	public function recoveryPopup(){
		if ($this->view->post['recovery']) {
			$error =  $this->view->error;
			$field = @key($error);
			$post = $this->view->post;
			$result = $this->view->result;
		}
		$xhtml = '
  <div id="precovery" class="modal-window password-recovery">
    <a href="#" class="close-modal"><img src="/'.I.'/close.png" onclick="removeErrors();$(\'#precovery\').hide();$(\'#modal\').hide()"/></a>
    <form action="" method="post">
	  <font color="green" style="position: relative; left: -40px;">' . $result . '</font>
      <h3 class="modal-title">Восстановление пароля</h3>
      <div id="edit-email-recovery-wrapper" class="form-item' . ($field == 'email' ? ' error' : '') . '">
        <label for="email_recovery">Эл. почта</label>
        <div class="form-text-wrapper">
		  <input id="email_recovery" type="text" name="email" value="' . $post['email'] . '">
          <div class="error-message"><span>' . $error[$field] . '</span></div>
		</div>
      </div>
<!--      <div id="edit-bdate-recovery-wrapper" class="form-item' . ($field == 'birth' ? ' error' : '') . '">
        <label for="bdate_recovery">Дата рождения</label>
        <input id="bdate_recovery" type="text" name="birth" value="' . ($post['birth'] ? $post['birth'] : 'гггг-мм-дд') . '" class="form-text grey" onblur="if (this.value == \'\') {this.value = \'гггг-мм-дд\'; this.className = \'form-text grey\'; }" onfocus="if (this.value == \'гггг-мм-дд\') {this.value = \'\'; this.className = \'form-text\';}">
        <div class="error-message"><span>' . $error[$field] . '</span></div>
        <img src="/'.I.'/datepicker-icon.png" class="datepicker-icon" />
      </div>-->
      <div id="edit-kap-wrapper" class="form-item captcha-item' . ($field == 'captcha' ? ' error' : '') . '">
        <label for="kap">Цифры с картинки</label>
        <div id="kpt" style="display:inline;">
          <img id="captchaImg" align="absmiddle" src="/captcha/recovery.php?' . rand(0,100) . '" alt="защитный код" width="100" height="60">
        </div>
        <div class="form-text-wrapper">
		  <input id="kap" class="recCap" type="text" value="" maxlength="30" size="15" name="captcha">
          <div class="error-message"><span>' . $error[$field] . '</span></div>
		</div>
      </div>
      <input id="subbutton" type="submit" value="Восстановить пароль" class="form-submit" name="recovery" onclick="recoveryAttempt(); return false;">
    </form>
  </div>
  <script>
  removeError();
  function recoveryAttempt(){
	var email = $("#email_recovery").val();
	var birth = $("#bdate_recovery").val();
	var captcha = $(".recCap").val();
	$.post("./", {email: email, birth: birth, captcha: captcha, recovery: true}, 
		function(data){
			$("#recPop").html(data);
		}
	)
  }
  </script>
		';
	if ($error) $xhtml .= '
  <script>$(document).ready(function(){
    $(".modal-window").hide();
    $("#modal").show();
    $("#precovery").show();
	});
  </script>
	';
	if ($result) {
		$xhtml = '
		<div id="reс-success" class="modal-window registration-success" style="display: block; height: 150px;">
		<div class="modal-content">
			<a href="#" onclick="$(\'#recPop\').hide();$(\'#enter\').show();" class="close-modal"><img src="/'.I.'/close.png"/></a>
			  <h2 class="modal-title">Восстановление пароля</h2>
			  <p>' . $result . '</p>
			  <span class="continue-button"><a href="#" onclick="$(\'#recPop\').hide();$(\'#enter\').show();">Продолжить</a></span>
		</div>
		</div>';
	}	
		return $xhtml;
	}
}