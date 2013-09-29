<?php
/**
 * @default_method index
 * @class          InfoUploadAction
 * @author         GongT
 */
class InfoUploadAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	final public function index(){
		$this->display('index');
	}
}
