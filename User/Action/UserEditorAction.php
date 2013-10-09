<?php
/**
 * @default_method
 * @class UserEditorAction
 * @author GongT
 */
class UserEditorAction extends Action{
	use UserAuthedAction;
	
	final public function property(){
		$this->display('property');
	}
}
