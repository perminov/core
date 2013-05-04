<?php
class Indi_View_Helper_Admin_RenderForm extends Indi_View_Helper_Abstract{
	public function renderForm()
	{
		echo $this->view->formHeader();
		foreach ($this->view->trail->getItem()->fields as $field) {
			if(!$field->getForeignRowByForeignKey('elementId')->hidden) echo $this->view->formField($field);
		} 
		
		echo $this->view->formFooter();
		echo '<script>' . $this->view->trail->getItem()->section->javascriptForm . '</script>';
        $sections = $this->view->trail->getItem()->sections->toArray();
        if (count($sections)) {
            $sectionsDropdown = array();
            $maxLength = 12;
           // $sectionsDropdown[] = array('alias' => '', 'title' => '--Выберите--');
            for ($i = 0; $i < count($sections); $i++){
                $sectionsDropdown[] = array('alias' => $sections[$i]['alias'], 'title' => $sections[$i]['title']);
                $str = preg_replace('/&[a-z]+;/', '&', $sections[$i]['title']);
                $len = mb_strlen($str, 'utf-8');
                if ($len > $maxLength) $maxLength = $len;
            }
        }
        ob_start();?>
        <script>
            $(document).ready(function(){
                var parent = top.window.$('iframe[name="form-frame"]').parent();
                while (parent.attr('id') != 'center-content-body') {
                    parent.css('height', '100%');
                    parent = parent.parent();
                }
            })
        </script>
        <? $xhtml = ob_get_clean();
        if (count($sections)){
        ob_start();?>
        <script>
            var toolbar = {
                xtype: 'toolbar',
                dock: 'top',
                id: 'topbar',
                items: ['->',
                    'Подраздел:  ',
                    top.window.Ext.create('Ext.form.ComboBox', {
                        store: top.window.Ext.create('Ext.data.Store',{
                            fields: ['alias', 'title'],
                            data: <?=json_encode($sectionsDropdown)?>
                        }),
                        valueField: 'alias',
                        hiddenName: 'alias',
                        displayField: 'title',
                        typeAhead: false,
                        width: <?=$maxLength*7+10?>,
                        style: 'font-size: 10px',
                        cls: 'subsection-select',
                        id: 'subsection-select',
                        editable: false,
                        margin: '0 6 2 0',
                        value: '--Выберите--',
                        listeners: {
                            change: function(cmb, newv, oldv){
                                if (this.getValue()) {
                                    top.window.loadContent('<?=$_SERVER['STD']?><?=$GLOBALS['cmsOnlyMode']?'':'/admin'?>/' + cmb.getValue() + '/index/id/' + <?=$this->view->row->id?> + '/');
                                }
                            }
                        }
                    })
                ]
            }
            var topbar = top.window.form.getDockedComponent('topbar');
            if (topbar) top.window.form.removeDocked(topbar);
            top.window.form.addDocked(toolbar);
            var topbar = top.window.form.getDockedComponent('topbar');
			var height = (top.window.$('#center-content-body').height() - topbar.getHeight() - 1);
			if (top.window.$('iframe[name="form-frame"]').height() > height) top.window.$('iframe[name="form-frame"]').css('height', height + 'px');
        </script>
        <? $xhtml .= ob_get_clean();
        }
        echo $xhtml;
	}

}