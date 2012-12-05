<?php
class Indi_View_Helper_PasswordPopup extends Indi_View_Helper_Abstract{
	public function passwordPopup(){
		if ($this->view->post['changePassword']) {
			$error =  $this->view->error;
			$field = @key($error);
			$post = $this->view->post;
			$result = $this->view->result;
		}
		ob_start();?>
<script>
$(document).ready(function(){
  $(".close-modal").click(function(){
    $(this).parents(".modal-window").hide();
    $("#modal").hide();
    return false;
  });
});
</script>
  <div id="change-password" class="change-password-modal modal-window"<?=$result?' style="display: none;"':''?>>
    <a href="#" class="close-modal"><img src="/i<?=$this->view->imposition?>/close.png" onclick=""/></a>
    <form action="" method="post" id="passwordForm">
      <h3 class="modal-title">Смена пароля<br/><a href="/myprofile/"><?=$_SESSION['nick']?></a></h3>
      <div id="edit-current-password-wrapper" class="form-item<?=$field == 'currentPassword' ? ' error' : ''?>">
        <label for="curpass">Текущий пароль</label>
        <div class="form-text-wrapper">
          <input id="curpass" type="password" name="currentPassword" value="<?=$post['currentPassword']?>">
          <div class="error-message"><span><?=$error[$field]?></span></div>
        </div>
      </div>
      <div id="edit-new-password-wrapper" class="form-item<?=$field == 'newPassword' ? ' error' : ''?>">
        <label for="newpass">Новый пароль</label>
        <div class="form-text-wrapper">
          <input id="newpass" type="password" name="newPassword" value="<?=$post['newPassword']?>">
          <div class="error-message"><span><?=$error[$field]?></span></div>
        </div>
      </div>
      <div id="edit-new-password-rep-wrapper" class="form-item<?=$field == 'newPasswordConfirm' ? ' error' : ''?>">
        <label for="newpassrep">Подтвердите пароль</label>
        <div class="form-text-wrapper">
          <input id="newpassrep" type="password" name="newPasswordConfirm" value="<?=$post['newPasswordConfirm']?>">
          <div class="error-message"><span><?=$error[$field]?></span></div>
        </div>
      </div>
	  <input type="hidden" name="changePassword" value="1"/>
      <input id="changePassword" type="submit" value="Изменить пароль" class="form-submit" onclick="passwordAttempt(); return false;">
    </form>
  </div>
  <script>
  function passwordAttempt(){
	$.post("./", $('#passwordForm').serialize(), function(data){$("#pasPop").html(data);});
  }
  </script>
		<?
		$xhtml = ob_get_clean();
		if ($result) {
			$xhtml .= '
			<div id="rec-success" class="modal-window registration-success">
				<div class="modal-content">
					<center>
					<a href="#" onclick="$(\'#rec-success\').hide();$(\'#modal\').hide();" class="close-modal"><img src="/'.I.'/close.png"/></a>
					<h2 class="modal-title" style="text-align: center;">Смена пароля<br/><a href="/myprofile/">' . $_SESSION['nick'] . '</a></h2>
					<p>Вы успешно сменили пароль на Туроплане. ' . $result . '</p>
					<span class="continue-button" style="width: 100px;"><a href="#" onclick="$(\'#rec-success\').hide();$(\'#modal\').hide();">Продолжить</a></span>
					</center>
				</div>
			</div>
			';
			$xhtml .= '
			<script>$(document).ready(function(){
			$(".modal-window").hide();
			$("#modal").show();
			$("#rec-success").show();
			});
			</script>
			';
		} else if ($error){
			$xhtml .= '
  <script>
  $(document).ready(function(){
    $(".modal-window").hide();
    $("#modal").show();
    $("#change-password").show();
  });
  </script>
			';
		}
		return $xhtml;
	}
}