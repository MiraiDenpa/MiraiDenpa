<?php
class InfoCoverAction extends UploadAction{
	use UserAuthedAction;

	final public function uploadimage(){
		switch($this->dispatcher->request_method){
		case 'OPTIONS':
		case 'HEAD':
			$this->head();
			break;
		case 'PATCH':
		case 'PUT':
		case 'POST':
			$this->head();
			$this->post();
			break;
		default:
			http_response_code(405);
			$this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}

	protected function get_upload_path(){
		return PICTURE_PATH . 'infosite/entrys/';
	}

	protected function get_upload_filename($name, $tmp){
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension

		$mime = finfo_file($finfo, $tmp);
		finfo_close($finfo);
		$ext = mime_to_extension($mime);
		if(!$ext){
			$this->error(ERR_INPUT_TYPE,'unknown file type');
			exit;
		}
		return md5_file($tmp).'.'.$ext;
	}
}
