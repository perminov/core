<?php
class Admin_EntitiesController extends Indi_Controller_Admin{
	public function postSave(){
		if (!$this->trail->getItem()->row->id) {
			$query = 'CREATE TABLE IF NOT EXISTS `' . $this->post['table'] . '` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
		} else if ($this->trail->getItem()->row->table != $this->post['table'] && $this->trail->getItem()->row->table && $this->post['table']) {
			$query = 'RENAME TABLE  `' . $this->trail->getItem()->row->table . '` TO  `' . $this->post['table'] . '` ;';
		}
		if ($query) $this->db->query($query);
	}
}