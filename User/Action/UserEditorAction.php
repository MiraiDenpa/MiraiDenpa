<?php
/**
 * @default_method
 * @class UserEditorAction
 * @author GongT
 */
class UserEditorAction extends Action{
	use UserAuthedAction;
	
	final public function property(){
		
		$this->assign('goback_url', $this->currentApp()->mainurl);
		
		$this->syncLogin();
		$this->display('property');
	}
}
