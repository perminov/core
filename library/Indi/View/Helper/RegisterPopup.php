<?php
class Indi_View_Helper_RegisterPopup extends Indi_View_Helper_Abstract{
	public function registerPopup(){
		if ($this->view->post['register']) {
			$error =  $this->view->error;
			$field = @key($error);
			$post = $this->view->post;
			$result = $this->view->result;
		}
		ob_start();?>
<script>
  $(".close-modal").click(function(){
    $(this).parents(".modal-window").hide();
    $("#modal").hide();
    return false;
  });
	var index=0;
	var selected=false;
	var prevValue = '';	
	function suggest(value, object, event){
		if (prevValue != value && event.keyCode != '13') {
			if (value.length > 2) {
//				$('#'+object).attr('name','');
				$('#'+object).val('');
				$.post("/users/suggest/", {value : value, object: object},
					function(data) {
						$('#suggest'+object).html(data);
						if (data.match('li')) {
							$('#suggest'+object).show();
							$('#no-autosearch-results'+object).hide();
							if (object == 'Hotel') {
								$('#cityForHotelDiv').hide();
							}
							selectedIndex=0;
							index = 0;
							keyboard(object, 0, 40);
						} else {
							$('#suggest'+object).hide();
							$('#no-autosearch-results'+object).show();
							if (object == 'Hotel') {
								$('#cityForHotelDiv').show();
							}
						}
					}
				);
			} else $('#suggest'+object).hide();
			index = 0;
			prevValue = value;			
		}
	}
	function selectPlace(location, type, id, object){
		$('#objectTitle'+object).val(location);
		selected=location;
		$('#suggest'+object).hide();
		$('#objectTitle'+object).focus();
//		$('#'+object).attr('name',type);
		$('#'+object).val(id);
//		if (object == 'City') object = 'Hotel';
//		$('#'+object+'Form').submit();
	}
	selectedIndex=0;
	function keyboard(object, event){
		var code = arguments[2] ? arguments[2] : event.keyCode;
		var oneSelected = false;
		if (code == '13') {
			if ($('#suggest'+object).css('display') == 'none') {
				//$('#'+(object=='City'?'Hotel':object)+'Form').submit();
			} else {
				var oneSelected=false;
				$('#suggestions'+object+' li').each(function(liIndex){
					if ($(this).css('background-color') == 'rgb(238, 238, 238)' || $(this).css('background-color') == '#eeeeee') {
						oneSelected=true;
					}
				});
				if (oneSelected) {
					$('#suggestions'+object+' li').each(function(liIndex){
						if ($(this).css('background-color') == 'rgb(238, 238, 238)' || $(this).css('background-color') == '#eeeeee') {
							selectPlace($(this).text(), $(this).attr('type'), $(this).attr('value'), object);
						}
					});				
				}
			}
		} else if (code == '40' || code == '38') {
			if (code == '40' && $('#suggest'+object).css('display') == 'none') {
				if (selected) {
					suggest(selected, object);
					selected=false;
				} else if ($('#objectTitle'+object).val()){
					suggest($('#objectTitle'+object).val());
				}
			} else {
				if (code == '40'){
					index = index+1;
				} else  if (code == '38'){
					index = index-1;
				}
				size = $('#suggestions'+object+' li').size();
				$('#suggestions'+object+' li').each(function(liIndex){
					if (index > 0) {
						if(liIndex == (index-1)%size) {
							$(this).css('background-color','#eeeeee');
							selectedIndex=liIndex;
						} else {
							$(this).css('background-color','#ffffff');
						}
					} else {
						if(liIndex+1 == size-Math.abs(index%size)) {
							selectedIndex=liIndex;
							$(this).css('background-color','#eeeeee');
						} else {
							$(this).css('background-color','#ffffff');
						}
					}
				});
			}
		} else if (event.keyCode == '27') {
			$('#suggest'+object).hide();
		}
		$('#objectTitle'+object).focus();
	}
	</script>

		<?

		$js = ob_get_clean();
		$xhtml = $js . '
  <div id="reg" class="modal-register modal-window" onclick="removeErrorTitle()">
    <form action="" method="post">
      <a href="#" class="close-modal"><img src="/'.I.'/close.png"/ onclick="removeErrors()"></a>
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
          <label for="email_reg">E-mail</label>
          <div class="form-text-wrapper">
            <input id="email_reg" type="text" name="email" value="' . $post['email'] . '">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
		  </div>
        </div>
        <div id="edit-objectTitleCity-wrapper" class="form-item' . ($field == 'city' ? ' error' : '') . '">
          <label for="objectTitleCity">Город</label>
          <div class="form-text-wrapper">
            <div class="error-message"><span>' . $error[$field] . '</span></div>
			<input type="text" id="objectTitleCityReg" name="city" autocomplete="off" onkeyup="suggest(this.value, \'CityReg\', event)" onkeydown="keyboard(\'CityReg\', event)" class="form-text" style="z-index: 0;" value="' . $post['city'] . '"/>
			<input type="hidden" id="CityReg" name="cityId" value="' . $post['cityId'] . '"/>
			<div class="autocomplete-wrapper" style="display: none;" id="suggestCityReg">
			</div>
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
          <label for="kap">Проверка</label>
          <div id="kpt" style="display:inline;">
            <img align="absmiddle" src="/captcha/register.php?' . rand(0, 100) . '" alt="" width="100" height="60" id="pic">
          </div>
          <!--Начало изменений -->
          <div id="captha-actions">
            <div class="refresh-wrapper">
              <a href="#" class="refresh-captcha" onclick="$(\'#pic\').attr(\'src\',\'/captcha/register.php?12\'); return false;"><span>Обновить</span></a>
            </div>
		    <div class="form-text-wrapper">
			  <input id="kap" type="text" value="" maxlength="30" size="15" name="captcha">
			  <div class="error-message"><span>' . $error[$field] . '</span></div>
		    </div>
          </div>
          <!--Конец изменений -->
        </div>
	  </div>
	  
      <div class="social-login">
        <h2 class="block-title">Через социальную сеть</h2>
        <ul>
          <li><a href="#" class="login-vk" onclick="VK.Auth.login(vkAuth);">Вконтакте</a></li>
          <li><a href="#" class="login-fb" onclick="$(\'a.fb_button_medium\').click();">Facebook</a></li>
          <li><a href="#" class="login-mr" onclick="$(\'a.mrc__connectButton\').click();">@mail.ru</a></li>
		</ul>
      </div>	
      <div class="form-item1' . ($field == 'agreement' ? ' error' : '') . ' user-agreement">
        <input id="sogl" type="checkbox" name="agreement"' . ($post['agreement'] && $post['agreement'] != 'false' ? ' checked="checked"' : '') . ' style="max-width:14px;"/>
        <div class="error-message" style="margin-left: 95px;position: absolute; top: 347px;left: 290px;"><span>' . $error[$field] . '</span></div>
        <span>Я принимаю условия <a href="/terms/" target="_balnk">пользовательского соглашения</a> и ознакомлен с <a href="/policy/" target="_blank">политикой конфиденциальности</a>.</span>
      </div>
      <input id="subbutton" type="button" value="Зарегистрироваться" name="register" onclick="registerAttempt();return false;" class="form-submit">
    </form>
  </div>';

  $xhtml .='
  <script>
  var saveBG;
  function removeErrors(){
/*	$("div.form-item").each(function(index){
		$(this).removeClass("error");
		$(this).find("input").css("background-image","url(/'.I.'/modal-register-form-text.png)");
	});*/
    $(".modal-window").hide();
    $("#modal").hide();
  }
  function removeErrorTitle(){
/*	$("div.form-item").each(function(index){
		if ($(this).hasClass("error")){
			$(this).find(".error-message").hide();
		}
	});*/
  }
  </script>
  <script>
  removeError();
  function registerAttempt(){
	var title = $("#login").val();
	var email = $("#email_reg").val();
	var city = $("#objectTitleCityReg").val();
	var cityId = $("#CityReg").val();
	var password = $("#pass1").val();
	var passwordConfirm = $("#pass2").val();
	var captcha = $("#kap").val();
	var agreement = $("#sogl").attr("checked");
	$.post("./", {title: title, email: email, city: city, password: password, passwordConfirm: passwordConfirm, captcha: captcha, 
			register: true, agreement: agreement, cityId: cityId }, function(data){$("#regPop").html(data);}
	)
  }
  </script>
		';
		if ($result) {
			$xhtml = '
  <script>$(document).ready(function(){
    $(".modal-window").hide();
    $("#modal").show();
    $("#reg-success").show();
	});
  </script>
			';
			$xhtml .= '
			<div id="reg-success" class="modal-window registration-success">
			<div class="modal-content"><a href="#" class="close-modal"><img src="/'.I.'/close.png"/ onclick="removeErrors()"></a>';
			if (!$_SESSION['naUserId']){
				$xhtml .= '<a href="#" onclick="window.location.reload()" class="close-modal"><img src="/'.I.'/close.png"/></a>';
			} else if (strpos($_SERVER['REQUEST_URI'], 'add')){
				$xhtml .= '<a href="#" onclick="$(\'#oldsubmit\').remove();$(\'#newsubmit\').show();$(\'#regPop\').hide();$(\'#modal\').hide();" class="close-modal"><img src="/'.I.'/close.png"/></a>';
			}

			$xhtml .= '
			  <h2 class="modal-title">Регистрация</h2>
			  <p>Спасибо за регистрацию!</p>
			  <p>Вы успешно зарегистрированы на Туроплане. Для активации аккаунта воспользуйтесь ссылкой, которая отослана Вам на e-mail.</p>';
				if (!$_SESSION['naUserId']){
					$xhtml .= '<span class="continue-button"><a href="#" onclick="window.location.reload()">Продолжить</a></span>';
				} else if (strpos($_SERVER['REQUEST_URI'], 'add')){
					$xhtml .= '<span class="continue-button"><a href="#" onclick="$(\'#oldsubmit\').remove();$(\'#newsubmit\').show();$(\'#regPop\').hide();$(\'#modal\').hide();">Вернуться к отзыву</a></span>';
				}
			$xhtml .= '
			</div>
			</div>
			';
		} else if ($error){
			$xhtml .= '
		
  <script>$(document).ready(function(){
    $(".modal-window").hide();
    $("#modal").show();
    $("#reg").show();
	});
  </script>
			';
		}
		return $xhtml;
	}
}