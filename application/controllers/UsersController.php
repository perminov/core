<?php
class UsersController extends Indi_Controller_Front{
	public function activationAction(){
		$code = $this->getRequest()->getParam('code');
		$user = Indi::model('User')->fetchRow('`activationCode` = "' . substr($code, 0, 15) . '"');
		if ($user) {
			unset($_SESSION['naUserId']);
			if ($user->activated) {
				$msg = 'Ваш аккаунт уже был активирован. Для входа используйте логин и пароль, указанные при регистрации.';
			} else {
				$user->activated = 1;
				$user->lastVisit = date('Y-m-d H:i:s');
				$user->save();
				$msg = 'Ваш аккаунт успешно активирован, в настоящий момент Вы авторизованы на сайте.';
				$_SESSION['userId'] = $user->id;
				$_SESSION['nick'] = $user->title;
			}
			$this->view->msg = $msg;
		} 
	}
	public function enteredAction(){
		if ($_SESSION['userId']) {
			echo $this->view->userEntered();
		}
		die();
	}
	public function unsubscribeAction(){
		d($this->getRequest()->getParams());
		die('asd');
	}
	public function registrationAction(){
	}
}


