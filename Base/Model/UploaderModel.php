<?php
/**
 * 处理当前登录的用户（和其他在线用户
 *
 * @author ${USER}
 */
class UploaderModel extends MongooFile{
	/** @var  Imagick */
	protected $_image;
	protected $_file_path;
	protected $_id;

	public function _initialize($collection, $database){
		if($database){
			$this->connection = 'mongo-' . $database;
		}
		if($collection){
			$this->collectionName = $collection;
		}
	}

	/**
	 *
	 * @param $file_path string 文件路径
	 *
	 * @return void
	 */
	function prepare_image($file_path){
		if($this->_image){
			$this->_image->clear();
		}
		$this->_image = new Imagick();
		$this->_image->setResourceLimit(imagick::RESOURCETYPE_MAP, 32);
		$this->_image->setResourceLimit(imagick::RESOURCETYPE_MEMORY, 32);
		$this->_image->readImage($file_path);
		$this->_file_path = $file_path;
	}

	/**
	 *
	 * @param array $metadata
	 *
	 * @return MongoId
	 */
	public function saveImage($metadata = []){
		unlink($this->_file_path);
		$this->_image->stripimage();
		$fname    = $this->_image->getimagesignature();
		$metadata = array_merge($metadata,
								[
								'new'    => true,
								'filename' => $fname,
								'height'   => $this->_image->getimageheight(),
								'width'    => $this->_image->getimagewidth(),
								'ext'      => $this->_image->getformat(),
								]);
		ksort($metadata);
		$id = $this->storeBytes($this->_image->getimageblob(), $metadata);
		if($id){
			$this->_id = $id;
			return $id;
		} else{
			return $this->db->lastError();
		}
	}

	public function getAccessUrl(){
		
	}

	public function paintImage($id){
	}

	public function limitFormat($formats){
		$format = $this->_image->getimageformat();
		if(!is_array($formats)){
			return $formats == $format;
		} else{
			return in_array($format, $formats);
		}
	}

	/**
	 * 宽*高
	 **/
	public function limitImageSize($min_width, $min_height, $max_width, $max_height){
		if($min_width || $max_width){
			$width = $this->_image->getimagewidth();
			if($min_width > $width){
				return 1;
			}
			if($max_width < $width){
				return 2;
			}
		}
		if($min_height || $max_height){
			$height = $this->_image->getimageheight();
			if($min_height > $height){
				return 3;
			}
			if($max_height < $height){
				return 4;
			}
		}
		return 0;
	}

	public function fixImageSize($width, $height){
		$this->_image->adaptiveresizeimage($width, $height, true);
	}

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

	public function limitMaxFileSize($size_in_kb){
		return $this->fix_integer_overflow(filesize($this->_file_path)) <= $size_in_kb*1024;
	}
}
