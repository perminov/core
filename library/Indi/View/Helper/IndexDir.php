<?php
class Indi_View_Helper_IndexDir extends Indi_View_Helper_Abstract{
	public function indexDir(){
		ob_start();?>
		Вверх/Вниз
		<input
			type="checkbox"
			onclick="$('#indexDir').val(this.checked?'ASC':'DESC'); $('#indexParams').submit();"
			<?=$this->view->indexParams['dir'] == 'ASC' ? 'checked="checked"':''?>
			style="position: relative; top: 2px;">

		<?return ob_get_clean();
	}
}