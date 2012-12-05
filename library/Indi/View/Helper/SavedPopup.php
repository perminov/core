<?php
class Indi_View_Helper_SavedPopup extends Indi_View_Helper_Abstract{
	public function savedPopup(){
		if ($this->view->saved) {
			ob_start();?>
			<script>
			$(document).ready(function(){
			  $(".modal-window").hide();
			  $("#modal").show();
			  $("#changes-saved").show();

			  $(".close-modal").click(function(){
				$(this).parents(".modal-window").hide();
				$("#modal").hide();
				return false;
			  });
			  
			  $(".ok-btn").click(function(){
				$(this).parents(".modal-window").hide();
				$("#modal").hide();
				return false;
			  });
			});
			</script>
			<div id="changes-saved" class="changes-saved-modal modal-window" style="display: block;">
			<a href="#" class="close-modal"><img src="/i/close.png"/></a>
			<div class="modal-content">
			  <span>Изменения успешно сохранены.</span>
			  <a class="ok-btn" href="ok-btn">ОК</a>
			</div>
			</div>
			<?$xhtml = ob_get_clean();
		}
		return $xhtml;
	}
}