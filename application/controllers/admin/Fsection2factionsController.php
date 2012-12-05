<?php
class Admin_Fsection2factionsController extends Indi_Controller_Admin{
	public function postSave(){
		$this->updateSeoXml();
	}
	public function updateSeoXml(){

		ini_set('max_execution_time', 60 * 60 * 2);
		
		$update = '';
		$fsection = Misc::loadModel('Fsection')->fetchRow('`id` = "' . $this->post['fsectionId'] . '"')->alias;
		$faction = Misc::loadModel('Faction')->fetchRow('`id` = "' . $this->post['factionId'] . '"')->alias;
		$dependentXml = array(
			'hotels_index' => 'wh,dh,ch',
			'directions_details' => 'w',
			'countries_details' => 'd,c'
		);

		$this->prevValues = array('blink' => $this->row->blink, 'rename' => $this->row->rename, 'alias' => $this->row->alias);
		$this->nextValues = array('blink' => $this->post['blink'], 'rename' => $this->post['rename'], 'alias' => $this->post['alias']);
		
		foreach ($this->prevValues as $key => $value) {
			if ($this->nextValues[$key] != $value) {
				$update = $dependentXml[$fsection . '_' . $faction]; 
				break;
			}
		}
		
		if (!$update) return;

		$dr = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/');
		$update = explode(',', $update);

		// ��������� seo xml ������ ��� ����� ���� �� ���� ��������� ����� ������
		if (in_array('w', $update)) {
			ob_start();
			readfile($dr . '/data/swf/ammap_data.xml');
			$xml = Indi_Uri::sys2seo(ob_get_clean());
			$fp = fopen($dr . '/data/swf/ammap_data_seo.xml', 'w');
			fwrite($fp, $xml);
			fclose($fp);
		}
		
		// ��������� seo xml ������ ��� ����� ���� �� �������� ������
		if (in_array('wh', $update)) {
			ob_start();
			readfile($dr . '/data/swf/ammap_data_hotels.xml');
			$xml = Indi_Uri::sys2seo(ob_get_clean());
			$fp = fopen($dr . '/data/swf/ammap_data_hotels_seo.xml', 'w');
			fwrite($fp, $xml);
			fclose($fp);
		}
		
		$directions = Misc::loadModel('Direction')->fetchAll();
		$uploadPath = $dr . '/' . Indi_Image::getUploadPath() . '/direction/';
		// ��������� seo xml ������� ��� ������ ����� �� ���� ��������� ����� ������
		if (in_array('d', $update)) {
			foreach ($directions as $direction) {
				ob_start();
				readfile($uploadPath . $direction->id . '_mapSettings.xml');
				$xml = Indi_Uri::sys2seo(ob_get_clean());
				$fp = fopen($uploadPath . $direction->id . '_mapSettings_seo.xml', 'w');
				fwrite($fp, $xml);
				fclose($fp);
			}
		}

		// ��������� seo xml ������� ��� ������ ����� �� �������� ������
		if (in_array('dh', $update)) {
			foreach ($directions as $direction) {
				ob_start();
				readfile($uploadPath . $direction->id . '_mapSettings_hotels.xml');
				$xml = Indi_Uri::sys2seo(ob_get_clean());
				$fp = fopen($uploadPath . $direction->id . '_mapSettings_hotels_seo.xml', 'w');
				fwrite($fp, $xml);
				fclose($fp);
			}
		}
		
		// ��������� seo xml ������� ��� ����� �� ���� ��������� ����� ������
		$countries = Misc::loadModel('Country')->fetchAll();
		$uploadPath = $dr . '/' . Indi_Image::getUploadPath() . '/country/';
		if (in_array('c', $update)) {
			foreach ($countries as $country) {
				ob_start();
				readfile($uploadPath . $country->id . '_mapSettings.xml');
				$xml = Indi_Uri::sys2seo(ob_get_clean());
				$fp = fopen($uploadPath . $country->id . '_mapSettings_seo.xml', 'w');
				fwrite($fp, $xml);
				fclose($fp);
			}
		}

		// ��������� seo xml ������� ��� ����� �� �������� ������
		if (in_array('ch', $update)) {
			foreach ($countries as $country) {
				ob_start();
				readfile($uploadPath . $country->id . '_mapSettings_hotels.xml');
				$xml = Indi_Uri::sys2seo(ob_get_clean());
				$fp = fopen($uploadPath . $country->id . '_mapSettings_hotels_seo.xml', 'w');
				fwrite($fp, $xml);
				fclose($fp);
			}
		}
		$this->db->query('UPDATE `fconfig` SET `value`= UNIX_TIMESTAMP() WHERE `alias` = "xmlTimestamp"');
	}
}