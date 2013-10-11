<?php
/**
 * @default_method index
 * @class          InfoCallbackAction
 * @author         GongT
 */
class InfoLogoutAction extends Action{
	final public function index(){
		cookie('token', '', -1, '/');
	}
}
