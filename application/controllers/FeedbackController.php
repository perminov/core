<?php
class FeedbackController extends Project_Controller_Front{
	public function addAction(){
		$json = array();
		if ($this->post['captcha'] != $_SESSION['captcha']['feedback']) {
			$json['errors'][] = 'Вы ввели неправильный проверочный код';
		} else {
			$data = $this->post;
			$data['date'] = date('Y-m-d');
			Indi::model('Feedback')->createRow($data)->save();
			$json['ok'] = 'Ваше сообщение отправлено';
		}
		die(json_encode($json));
	}
}