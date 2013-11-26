<?php
/**
 * @default_method index
 * @class          FileUploadAction
 * @author         GongT
 */
abstract class SmallFileUploadAction extends Action{
	protected $max_file_size = null;
	protected $min_file_size = 1;
	protected $max_width = null;
	protected $max_height = null;
	protected $min_width = 1;
	protected $min_height = 1;

	/**
	 * Fix for overflowing signed 32 bit integers,
	 * works for sizes up to 2^32-1 bytes (4 GiB - 1):
	 */
	private function fix_integer_overflow($size){
		if($size < 0){
			$size += 2.0*(PHP_INT_MAX + 1);
		}
		return $size;
	}

	/**  */
	private function get_file_size($file_path, $clear_stat_cache = false){
		if($clear_stat_cache){
			clearstatcache(true, $file_path);
		}
		return $this->fix_integer_overflow(filesize($file_path));
	}

	/**  */
	private function validate($uploaded_file, $file, $error, $index){
		if($error){
			$this->error($error);
			return false;
		}
		if($uploaded_file && is_uploaded_file($uploaded_file)){
			$file_size = $this->get_file_size($uploaded_file);
		} else{
			$file_size = $this->fix_integer_overflow(intval($this->get_server_var('CONTENT_LENGTH')));
		}
		if($this->max_file_size && ($file_size > $this->max_file_size || $file->size > $this->max_file_size)){
			$this->error(ERR_RANGE_SIZE_FILE, 'file too large');
			return false;
		}
		if($this->min_file_size && $file_size < $this->min_file_size){
			$this->error(ERR_RANGE_SIZE_FILE, 'file too small');
			return false;
		}
		$max_width  = $this->max_width;
		$max_height = $this->max_height;
		$min_width  = $this->min_width;
		$min_height = $this->min_height;
		$img_height = 0;
		if(($max_width || $max_height || $min_width || $min_height)){
			list($img_width, $img_height) = $this->get_image_size($uploaded_file);
		}
		if(!empty($img_width)){
			if($max_width && $img_width > $max_width){
				$this->error(ERR_RANGE_SIZE_IMAGE, 'too wide');
				return false;
			}
			if($max_height && $img_height > $max_height){
				$this->error(ERR_RANGE_SIZE_IMAGE, 'too high');
				return false;
			}
			if($min_width && $img_width < $min_width){
				$this->error(ERR_RANGE_SIZE_IMAGE, 'too narrow');
				return false;
			}
			if($min_height && $img_height < $min_height){
				$this->error(ERR_RANGE_SIZE_IMAGE, 'too short');
				return false;
			}
		}
		return true;
	}

	/**  */
	protected function trim_file_name($name){
		// Remove path information and dots around the filename, to prevent uploading
		// into different directories or replacing hidden system files.
		// Also remove control characters and spaces (\x00..\x20) around the filename:
		$name = trim(basename(stripslashes($name)), ".\x00..\x20");
		// Use a timestamp for empty filenames:
		if(!$name){
			$name = str_replace('.', '-', microtime(true));
		}
		return $name;
	}

	/**  */
	private function imagick_get_image_object($file_path){
		$image = new Imagick();
		$image->setResourceLimit(imagick::RESOURCETYPE_MAP, 32);
		$image->setResourceLimit(imagick::RESOURCETYPE_MEMORY, 32);
		$image->readImage($file_path);
		return $image;
	}

	/**  */
	private function imagick_orient_image(Imagick $image){
		$orientation = $image->getImageOrientation();
		$background  = new ImagickPixel('none');
		switch($orientation){
		case imagick::ORIENTATION_TOPRIGHT: // 2
			$image->flopImage(); // horizontal flop around y-axis
			break;
		case imagick::ORIENTATION_BOTTOMRIGHT: // 3
			$image->rotateImage($background, 180);
			break;
		case imagick::ORIENTATION_BOTTOMLEFT: // 4
			$image->flipImage(); // vertical flip around x-axis
			break;
		case imagick::ORIENTATION_LEFTTOP: // 5
			$image->flopImage(); // horizontal flop around y-axis
			$image->rotateImage($background, 270);
			break;
		case imagick::ORIENTATION_RIGHTTOP: // 6
			$image->rotateImage($background, 90);
			break;
		case imagick::ORIENTATION_RIGHTBOTTOM: // 7
			$image->flipImage(); // vertical flip around x-axis
			$image->rotateImage($background, 270);
			break;
		case imagick::ORIENTATION_LEFTBOTTOM: // 8
			$image->rotateImage($background, 270);
			break;
		default:
			return false;
		}
		$image->setImageOrientation(imagick::ORIENTATION_TOPLEFT); // 1
		return true;
	}

	/**  */
	private function get_image_size($file_path){
		try{
			$image = new Imagick();
			if($image->pingImage($file_path)){
				$dimensions = array($image->getImageWidth(), $image->getImageHeight());
				$image->destroy();
				return $dimensions;
			} else{
				return false;
			}
		} catch(ImagickException $e){
			return getimagesize($file_path);
		}
	}

	/**  */
	private function get_temp_name($name){
		return PICTURE_PATH . 'tmp/' . $_COOKIE['token'] . md5($name);
	}

	/**  */
	protected function handle_upload_file($path){
	}

	/**  */
	private function get_server_var($id, $default = ''){
		return isset($_SERVER[$id])? $_SERVER[$id] : $default;
	}

	/**  */
	private function generate_response($content, $print_response = true){
		if($print_response){
			$files = isset($content[$this->param_name])? $content[$this->param_name] : null;
			if($this->get_server_var('HTTP_CONTENT_RANGE')){
				if($files && is_array($files) && is_object($files[0]) && $files[0]->size){
					header('Range: 0-' . ($this->fix_integer_overflow(intval($files[0]->size)) - 1));
				}
			}
			foreach($files as &$file){
				if($file->error == ERR_NO_ERROR){
					$file->message = "上传成功";
				} else{
					$e             = new Error($file->error);
					$file->message = $e->getMessage();
					$file->extra   = '';
					$file->code    = $e->getCode();
					$file->name    = $e->getName();
					$file->info    = $e->getInfo();
					$file->where   = $e->getWhere();
				}
			}
			$this->assign($content);
			$this->display('!data');
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
