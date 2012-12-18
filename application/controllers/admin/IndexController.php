<?php
class Admin_IndexController extends Indi_Controller_Admin{
	public function menuAction(){
		ob_start();?>[
		{"text":"Action.js","id":"src\/Action.js","leaf":true,"cls":"file"},
		{"text":"Ajax.js","id":"src\/Ajax.js","leaf":true,"cls":"file"},
		{"text":"container","id":"src\/container","cls":"folder"},
		{"text":"core","id":"src\/core","cls":"folder"}
	]<?die(ob_get_clean());
	}
}