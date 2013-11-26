<?php
class InfoCoverAction extends Action{
	use UserAuthedAction;

	final public function uploadimage(){
		$uploader = ThinkInstance::D('Uploader', 'infosite');
		switch($this->dispatcher->request_method){
		case 'OPTIONS':
		case 'HEAD':
			$this->allow_access();
			break;
		case 'PATCH':
		case 'PUT':
		case 'POST':
			$files = array_pop($_FILES);
			if($files['error'][0] != 0){
				http_response_code(400);
				return;
			}
			$uploader->prepare_image($files['tmp_name'][0]);
			$uploader->limitFormat(['PNG', 'JPEG']);
			$uploader->fixImageSize('150', '200');
			$id = $uploader->saveImage();
			if($id){
				$this->assign('url', UI('preview',['id'=>(string)$id]));
				$this->success();
			}else{
				$this->error(ERR_NO_SQL,'Unknown File Error.');
			}
			break;
		default:
			http_response_code(405);
			$this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}

	/**  */
	public function allow_access(){
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		//header('Content-Disposition: inline; filename="files.json"');
		header('Access-Control-Allow-Origin: ' . $this->get_server_var('HTTP_ORIGIN', '*'));

		header('X-Content-Type-Options: nosniff');
		//header('Access-Control-Allow-Credentials:  true');
		header('Access-Control-Allow-Methods: ' . $this->get_server_var('HTTP_ACCESS_CONTROL_REQUEST_METHODS', '*'));
		header('Access-Control-Allow-Headers: ' . $this->get_server_var('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', '*'));

		header('Vary: Accept');
	}
}
