<?php
/**
 * @default_method __
 *
 * @author ${USER}
 */
class FallbackAction extends Action{
	final function __call($mtd,$arg){
		var_dump($this->dispatcher->action_name,$mtd,$arg);
	} 
}
