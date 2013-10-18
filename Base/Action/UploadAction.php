<?php
/**
 * @default_method index
 * @class          FileUploadAction
 * @author         GongT
 */
abstract class UploadAction extends Action{
	/*
	 * jQuery File Upload Plugin PHP Class 7.0.0
	 * https://github.com/blueimp/jQuery-File-Upload
	 *
	 * Copyright 2010, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * http://www.opensource.org/licenses/MIT
	 */

	protected $script_url = '';
	protected $mkdir_mode = 0755;
	protected $param_name = 'files';
	// Set the following option to 'POST', if your server does not support
	// DELETE requests. This is a parameter sent to the client:
	protected $delete_type = 'DELETE';
	// Enable to provide file downloads via GET requests to the PHP script:
	//     1. Set to 1 to download files via readfile method through PHP
	//     2. Set to 2 to send a X-Sendfile header for lighttpd/Apache
	//     3. Set to 3 to send a X-Accel-Redirect header for nginx
	// If set to 2 or 3, adjust the upload_url option to the base path of
	// the redirect parameter, e.g. '/files/'.
	protected $download_via_php = false;
	// Read files in chunks to avoid memory limits when download_via_php
	// is enabled, set to 0 to disable chunked reading of files:
	protected $readfile_chunk_size = 10485760; // 10 MiB
	// Defines which files can be displayed inline when downloaded:
	protected $inline_file_types = '/\.(gif|jpe?g|png)$/i';
	// 允许的扩展名（弱验证，查看用户本地的扩展名对不对）正则表达式
	protected $accept_file_extensions = '';
	// The php.ini settings upload_max_filesize and post_max_size
	// take precedence over the following max_file_size setting:
	protected $max_file_size = null;
	protected $min_file_size = 1;
	// Defines which files are handled as image files:
	protected $image_file_types = '/\.(gif|jpe?g|png)$/i';
	// Image resolution restrictions:
	protected $max_width = null;
	protected $max_height = null;
	protected $min_width = 1;
	protected $min_height = 1;
	// Set the following option to false to enable resumable uploads:
	protected $discard_aborted_uploads = true;
	// Set to 0 to use the GD library to scale and orient images,
	// set to 1 to use imagick (if installed, falls back to GD),
	// set to 2 to use the ImageMagick convert binary directly:
	protected $image_library = 1;
	// Uncomment the following to define an array of resource limits
	// for imagick:
	/*
protected $imagick_resource_limits=array(
		imagick::RESOURCETYPE_MAP => 32,
		imagick::RESOURCETYPE_MEMORY => 32
	),
	*/
	// Command or path for to the ImageMagick convert binary:
	protected $convert_bin = 'convert';
	// Uncomment the following to add parameters in front of each
	// ImageMagick convert call (the limit constraints seem only
	// to have an effect if put in front):
	/*
protected $convert_params='-limit memory 32MiB -limit map 32MiB',
	*/
	// Command or path for to the ImageMagick identify binary:
	protected $identify_bin = 'identify';
	protected $image_versions = array(
		// The empty image version key defines options for the original image:
		'' => array(
			// Automatically rotate images based on EXIF meta data:
			'auto_orient' => true
		),
		/*'thumbnail' => array(
			// Uncomment the following to use a defined directory for the thumbnails
			// instead of a subdirectory based on the version identifier.
			// Make sure that this directory doesn't allow execution of files if you
			// don't pose any restrictions on the type of uploaded files, e.g. by
			// copying the .htaccess file from the files directory for Apache:
			//'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
			//'upload_url' => $this->get_full_url().'/thumb/',
			// Uncomment the following to force the max
			// dimensions and e.g. create square thumbnails:
			//'crop' => true,
			'max_width'  => 80,
			'max_height' => 80
		)*/
	);

	protected $image_objects = array();

	/**  */
	abstract protected function get_upload_path();

	/**  */
	abstract protected function get_upload_filename($name, $tmp);

	/**  */
	protected function get_query_separator($url){
		return strpos($url, '?') === false? '?' : '&';
	}

	/*
	protected function get_download_url($file_name, $version = null, $direct = false){
		if(!$direct && $this->download_via_php){
			$url = $this->script_url . $this->get_query_separator($this->script_url) . 'file=' . rawurlencode($file_name
					);
			if($version){
				$url .= '&version=' . rawurlencode($version);
			}
			return $url . '&download=1';
		}
		if(empty($version)){
			$version_path = '';
		} else{
			$version_url = $this->image_versions[$version]['upload_url'];
			if($version_url){
				return $version_url . $this->get_user_path() . rawurlencode($file_name);
			}
			$version_path = rawurlencode($version) . '/';
		}
		return $this->upload_url . $this->get_user_path() . $version_path . rawurlencode($file_name);
	}*/

	/**  */
	protected function set_additional_file_properties($file){
		$file->deleteUrl             = '';
		$file->deleteType            = $this->delete_type;
		$file->deleteWithCredentials = false;
	}

	/**
	 * Fix for overflowing signed 32 bit integers,
	 * works for sizes up to 2^32-1 bytes (4 GiB - 1):
	 */
	protected function fix_integer_overflow($size){
		if($size < 0){
			$size += 2.0*(PHP_INT_MAX + 1);
		}
		return $size;
	}

	/**  */
	protected function get_file_size($file_path, $clear_stat_cache = false){
		if($clear_stat_cache){
			clearstatcache(true, $file_path);
		}
		return $this->fix_integer_overflow(filesize($file_path));
	}

	/*
	protected function is_valid_file_object($file_name){
		$file_path = $this->get_upload_path($file_name);
		if(is_file($file_path) && $file_name[0] !== '.'){
			return true;
		}
		return false;
	}*/

	/*
	protected function get_file_object($file_name){
		if($this->is_valid_file_object($file_name)){
			$file       = new stdClass();
			$file->name = $file_name;
			$file->size = $this->get_file_size($this->get_upload_path($file_name)
			);
			$file->url  = $this->get_download_url($file->name);
			foreach($this->image_versions as $version => $options){
				if(!empty($version)){
					if(is_file($this->get_upload_path($file_name, $version))){
						$file->{$version . 'Url'} = $this->get_download_url($file->name,
																			$version
						);
					}
				}
			}
			$this->set_additional_file_properties($file);
			return $file;
		}
		return null;
	}*/

	/*
	protected function get_file_objects($iteration_method = 'get_file_object'){
		$upload_dir = $this->get_upload_path();
		if(!is_dir($upload_dir)){
			return array();
		}
		return array_values(array_filter(array_map(array($this, $iteration_method),
												   scandir($upload_dir)
										 )
							)
		);
	}*/

	/*
	protected function count_file_objects(){
		return count($this->get_file_objects('is_valid_file_object'));
	}*/

	/**  */
	function get_config_bytes($val){
		$val  = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		switch($last){
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
		}
		return $this->fix_integer_overflow($val);
	}

	/**  */
	protected function validate($uploaded_file, $file, $error, $index){
		if($error){
			$this->error($error);
			return false;
		}
		$content_length = $this->fix_integer_overflow(intval($this->get_server_var('CONTENT_LENGTH')
													  )
		);
		if($this->accept_file_extensions && !preg_match($this->accept_file_extensions, $file->name)){
			$this->error(ERR_INPUT_TYPE, 'not allow file type ' . $file->name);
			return false;
		}
		if($uploaded_file && is_uploaded_file($uploaded_file)){
			$file_size = $this->get_file_size($uploaded_file);
		} else{
			$file_size = $content_length;
		}
		if($this->max_file_size && ($file_size > $this->max_file_size || $file->size > $this->max_file_size)
		){
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
	protected function upcount_name_callback($matches){
		$index = isset($matches[1])? intval($matches[1]) + 1 : 1;
		$ext   = isset($matches[2])? $matches[2] : '';
		return ' (' . $index . ')' . $ext;
	}

	/**  */
	protected function upcount_name($name){
		return preg_replace_callback('/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
									 array($this, 'upcount_name_callback'),
									 $name,
									 1
		);
	}

	/**  */
	protected function get_unique_filename($name, $type = null, $index = null, $content_range = null){
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
	protected function get_file_name($temp, $name){
		$name = $this->trim_file_name($name);
		$ret  = $this->get_upload_path() . '/' . $this->get_upload_filename($name, $temp);
		$ret  = str_replace(['//', '../', './'], ['/', '/', '/'], $ret);
		return $ret;
	}

	/**  */
	protected function handle_form_data($file, $index){
		// Handle form data, e.g. $_REQUEST['description'][$index]
	}

	/*
	protected function get_scaled_image_file_paths($file_name, $version){
		$file_path = $this->get_upload_path($file_name);
		if(!empty($version)){
			$version_dir = $this->get_upload_path(null, $version);
			if(!is_dir($version_dir)){
				mkdir($version_dir, $this->mkdir_mode, true);
			}
			$new_file_path = $version_dir . '/' . $file_name;
		} else{
			$new_file_path = $file_path;
		}
		return array($file_path, $new_file_path);
	}*/

	/**  */
	protected function gd_get_image_object($file_path, $func, $no_cache = false){
		if(empty($this->image_objects[$file_path]) || $no_cache){
			$this->gd_destroy_image_object($file_path);
			$this->image_objects[$file_path] = $func($file_path);
		}
		return $this->image_objects[$file_path];
	}

	/**  */
	protected function gd_set_image_object($file_path, $image){
		$this->gd_destroy_image_object($file_path);
		$this->image_objects[$file_path] = $image;
	}

	/**  */
	protected function gd_destroy_image_object($file_path){
		$image = $this->image_objects[$file_path];
		return $image && imagedestroy($image);
	}

	/**  */
	protected function gd_imageflip($image, $mode){
		if(function_exists('imageflip')){
			return imageflip($image, $mode);
		}
		$new_width  = $src_width = imagesx($image);
		$new_height = $src_height = imagesy($image);
		$new_img    = imagecreatetruecolor($new_width, $new_height);
		$src_x      = 0;
		$src_y      = 0;
		switch($mode){
		case '1': // flip on the horizontal axis
			$src_y      = $new_height - 1;
			$src_height = -$new_height;
			break;
		case '2': // flip on the vertical axis
			$src_x     = $new_width - 1;
			$src_width = -$new_width;
			break;
		case '3': // flip on both axes
			$src_y      = $new_height - 1;
			$src_height = -$new_height;
			$src_x      = $new_width - 1;
			$src_width  = -$new_width;
			break;
		default:
			return $image;
		}
		imagecopyresampled($new_img,
						   $image,
						   0,
						   0,
						   $src_x,
						   $src_y,
						   $new_width,
						   $new_height,
						   $src_width,
						   $src_height
		);
		return $new_img;
	}

	/**  */
	protected function gd_orient_image($file_path, $src_img){
		if(!function_exists('exif_read_data')){
			return false;
		}
		$exif = exif_read_data($file_path);
		if($exif === false){
			return false;
		}
		$orientation = intval($exif['Orientation']);
		if($orientation < 2 || $orientation > 8){
			return false;
		}
		switch($orientation){
		case 2:
			$new_img = $this->gd_imageflip($src_img,
										   defined('IMG_FLIP_VERTICAL')? IMG_FLIP_VERTICAL : 2
			);
			break;
		case 3:
			$new_img = imagerotate($src_img, 180, 0);
			break;
		case 4:
			$new_img = $this->gd_imageflip($src_img,
										   defined('IMG_FLIP_HORIZONTAL')? IMG_FLIP_HORIZONTAL : 1
			);
			break;
		case 5:
			$tmp_img = $this->gd_imageflip($src_img,
										   defined('IMG_FLIP_HORIZONTAL')? IMG_FLIP_HORIZONTAL : 1
			);
			$new_img = imagerotate($tmp_img, 270, 0);
			imagedestroy($tmp_img);
			break;
		case 6:
			$new_img = imagerotate($src_img, 270, 0);
			break;
		case 7:
			$tmp_img = $this->gd_imageflip($src_img,
										   defined('IMG_FLIP_VERTICAL')? IMG_FLIP_VERTICAL : 2
			);
			$new_img = imagerotate($tmp_img, 270, 0);
			imagedestroy($tmp_img);
			break;
		case 8:
			$new_img = imagerotate($src_img, 90, 0);
			break;
		default:
			return false;
		}
		$this->gd_set_image_object($file_path, $new_img);
		return true;
	}

	/**  */
	protected function gd_create_scaled_image($file_name, $version, $options){
		if(!function_exists('imagecreatetruecolor')){
			error_log('Function not found: imagecreatetruecolor');
			return false;
		}
		list($file_path, $new_file_path) = $this->get_scaled_image_file_paths($file_name, $version);
		$type = strtolower(substr(strrchr($file_name, '.'), 1));
		switch($type){
		case 'jpg':
		case 'jpeg':
			$src_func      = 'imagecreatefromjpeg';
			$write_func    = 'imagejpeg';
			$image_quality = isset($options['jpeg_quality'])? $options['jpeg_quality'] : 75;
			break;
		case 'gif':
			$src_func      = 'imagecreatefromgif';
			$write_func    = 'imagegif';
			$image_quality = null;
			break;
		case 'png':
			$src_func      = 'imagecreatefrompng';
			$write_func    = 'imagepng';
			$image_quality = isset($options['png_quality'])? $options['png_quality'] : 9;
			break;
		default:
			return false;
		}
		$src_img        = $this->gd_get_image_object($file_path,
													 $src_func,
													 !empty($options['no_cache'])
		);
		$image_oriented = false;
		if(!empty($options['auto_orient']) && $this->gd_orient_image($file_path,
																	 $src_img
				)
		){
			$image_oriented = true;
			$src_img        = $this->gd_get_image_object($file_path,
														 $src_func
			);
		}
		$max_width  = $img_width = imagesx($src_img);
		$max_height = $img_height = imagesy($src_img);
		if(!empty($options['max_width'])){
			$max_width = $options['max_width'];
		}
		if(!empty($options['max_height'])){
			$max_height = $options['max_height'];
		}
		$scale = min($max_width/$img_width,
					 $max_height/$img_height
		);
		if($scale >= 1){
			if($image_oriented){
				return $write_func($src_img, $new_file_path, $image_quality);
			}
			if($file_path !== $new_file_path){
				return copy($file_path, $new_file_path);
			}
			return true;
		}
		if(empty($options['crop'])){
			$new_width  = $img_width*$scale;
			$new_height = $img_height*$scale;
			$dst_x      = 0;
			$dst_y      = 0;
			$new_img    = imagecreatetruecolor($new_width, $new_height);
		} else{
			if(($img_width/$img_height) >= ($max_width/$max_height)){
				$new_width  = $img_width/($img_height/$max_height);
				$new_height = $max_height;
			} else{
				$new_width  = $max_width;
				$new_height = $img_height/($img_width/$max_width);
			}
			$dst_x   = 0 - ($new_width - $max_width)/2;
			$dst_y   = 0 - ($new_height - $max_height)/2;
			$new_img = imagecreatetruecolor($max_width, $max_height);
		}
		// Handle transparency in GIF and PNG images:
		switch($type){
		case 'gif':
		case 'png':
			imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
			imagealphablending($new_img, false);
			imagesavealpha($new_img, true);
			break;
		}
		$success = imagecopyresampled($new_img,
									  $src_img,
									  $dst_x,
									  $dst_y,
									  0,
									  0,
									  $new_width,
									  $new_height,
									  $img_width,
									  $img_height
				   ) && $write_func($new_img, $new_file_path, $image_quality);
		$this->gd_set_image_object($file_path, $new_img);
		return $success;
	}

	/**  */
	protected function imagick_get_image_object($file_path, $no_cache = false){
		if(empty($this->image_objects[$file_path]) || $no_cache){
			$this->imagick_destroy_image_object($file_path);
			$image = new Imagick();
			if(!empty($this->imagick_resource_limits)){
				foreach($this->imagick_resource_limits as $type => $limit){
					$image->setResourceLimit($type, $limit);
				}
			}
			$image->readImage($file_path);
			$this->image_objects[$file_path] = $image;
		}
		return $this->image_objects[$file_path];
	}

	/**  */
	protected function imagick_set_image_object($file_path, $image){
		$this->imagick_destroy_image_object($file_path);
		$this->image_objects[$file_path] = $image;
	}

	/**  */
	protected function imagick_destroy_image_object($file_path){
		$image = $this->image_objects[$file_path];
		return $image && $image->destroy();
	}

	/**  */
	protected function imagick_orient_image($image){
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
	protected function imagick_create_scaled_image($file_name, $version, $options){
		list($file_path, $new_file_path) = $this->get_scaled_image_file_paths($file_name, $version);
		$image = $this->imagick_get_image_object($file_path,
												 !empty($options['no_cache'])
		);
		if($image->getImageFormat() === 'GIF'){
			// Handle animated GIFs:
			$images = $image->coalesceImages();
			foreach($images as $frame){
				$image = $frame;
				$this->imagick_set_image_object($file_name, $image);
				break;
			}
		}
		$image_oriented = false;
		if(!empty($options['auto_orient'])){
			$image_oriented = $this->imagick_orient_image($image);
		}
		$new_width  = $max_width = $img_width = $image->getImageWidth();
		$new_height = $max_height = $img_height = $image->getImageHeight();
		if(!empty($options['max_width'])){
			$new_width = $max_width = $options['max_width'];
		}
		if(!empty($options['max_height'])){
			$new_height = $max_height = $options['max_height'];
		}
		if(!($image_oriented || $max_width < $img_width || $max_height < $img_height)){
			if($file_path !== $new_file_path){
				return copy($file_path, $new_file_path);
			}
			return true;
		}
		$crop = !empty($options['crop']);
		$x    = 0;
		$y    = 0;
		if($crop){
			if(($img_width/$img_height) >= ($max_width/$max_height)){
				$new_width = 0; // Enables proportional scaling based on max_height
				$x         = ($img_width/($img_height/$max_height) - $max_width)/2;
			} else{
				$new_height = 0; // Enables proportional scaling based on max_width
				$y          = ($img_height/($img_width/$max_width) - $max_height)/2;
			}
		}
		$success = $image->resizeImage($new_width,
									   $new_height,
									   isset($options['filter'])? $options['filter'] : imagick::FILTER_LANCZOS,
									   isset($options['blur'])? $options['blur'] : 1,
									   $new_width && $new_height // fit image into constraints if not to be cropped
		);
		if($success && $crop){
			$success = $image->cropImage($max_width,
										 $max_height,
										 $x,
										 $y
			);
			if($success){
				$success = $image->setImagePage($max_width, $max_height, 0, 0);
			}
		}
		$type = strtolower(substr(strrchr($file_name, '.'), 1));
		switch($type){
		case 'jpg':
		case 'jpeg':
			if(!empty($options['jpeg_quality'])){
				$image->setImageCompression(Imagick::COMPRESSION_JPEG);
				$image->setImageCompressionQuality($options['jpeg_quality']);
			}
			break;
		}
		if(!empty($options['strip'])){
			$image->stripImage();
		}
		return $success && $image->writeImage($new_file_path);
	}

	/**  */
	protected function imagemagick_create_scaled_image($file_name, $version, $options){
		list($file_path, $new_file_path) = $this->get_scaled_image_file_paths($file_name, $version);
		$resize = $options['max_width'] . (empty($options['max_height'])? '' : 'x' . $options['max_height']);
		if(!$resize && empty($options['auto_orient'])){
			if($file_path !== $new_file_path){
				return copy($file_path, $new_file_path);
			}
			return true;
		}
		$cmd = $this->convert_bin;
		if(!empty($this->convert_params)){
			$cmd .= ' ' . $this->convert_params;
		}
		$cmd .= ' ' . escapeshellarg($file_path);
		if(!empty($options['auto_orient'])){
			$cmd .= ' -auto-orient';
		}
		if($resize){
			// Handle animated GIFs:
			$cmd .= ' -coalesce';
			if(empty($options['crop'])){
				$cmd .= ' -resize ' . escapeshellarg($resize . '>');
			} else{
				$cmd .= ' -resize ' . escapeshellarg($resize . '^');
				$cmd .= ' -gravity center';
				$cmd .= ' -crop ' . escapeshellarg($resize . '+0+0');
			}
			// Make sure the page dimensions are correct (fixes offsets of animated GIFs):
			$cmd .= ' +repage';
		}
		if(!empty($options['convert_params'])){
			$cmd .= ' ' . $options['convert_params'];
		}
		$cmd .= ' ' . escapeshellarg($new_file_path);
		exec($cmd, $output, $error);
		if($error){
			error_log(implode('\n', $output));
			return false;
		}
		return true;
	}

	/**  */
	protected function get_image_size($file_path){
		if($this->image_library){
			if(extension_loaded('imagick')){
				try{
					$image = new Imagick();
					if($image->pingImage($file_path)){
						$dimensions = array($image->getImageWidth(), $image->getImageHeight());
						$image->destroy();
						return $dimensions;
					}
				} catch(ImagickException $e){
				}
				return false;
			}
			if($this->image_library === 2){
				$cmd = $this->identify_bin;
				$cmd .= ' -ping ' . escapeshellarg($file_path);
				exec($cmd, $output, $error);
				if(!$error && !empty($output)){
					// image.jpg JPEG 1920x1080 1920x1080+0+0 8-bit sRGB 465KB 0.000u 0:00.000
					$infos      = preg_split('/\s+/', $output[0]);
					$dimensions = preg_split('/x/', $infos[2]);
					return $dimensions;
				}
				return false;
			}
		}
		if(!function_exists('getimagesize')){
			error_log('Function not found: getimagesize');
			return false;
		}
		return getimagesize($file_path);
	}

	/**  */
	protected function create_scaled_image($file_name, $version, $options){
		if($this->image_library === 2){
			return $this->imagemagick_create_scaled_image($file_name, $version, $options);
		}
		if($this->image_library && extension_loaded('imagick')){
			return $this->imagick_create_scaled_image($file_name, $version, $options);
		}
		return $this->gd_create_scaled_image($file_name, $version, $options);
	}

	/**  */
	protected function destroy_image_object($file_path){
		if($this->image_library && extension_loaded('imagick')){
			return $this->imagick_destroy_image_object($file_path);
		}
		return false;
	}

	/**  */
	protected function is_valid_image_file($file_path){
		if(!preg_match($this->image_file_types, $file_path)){
			return false;
		}
		if(function_exists('exif_imagetype')){
			return exif_imagetype($file_path);
		}
		$image_info = $this->get_image_size($file_path);
		return $image_info && $image_info[0] && $image_info[1];
	}

	private function get_temp_name($name){
		return PICTURE_PATH . 'tmp/' . $_COOKIE['token'] . md5($name);
	}

	/**  */
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null){
		$file        = new stdClass();
		$file->error = ERR_NO_ERROR;
		$file_path   = $this->get_temp_name($name);
		$file->size  = $this->fix_integer_overflow(intval($size));
		$file->type  = $type;
		if($this->validate($uploaded_file, $file, $error, $index)){
			$this->handle_form_data($file, $index);

			$append_file = $content_range && is_file($file_path) && $file->size > $this->get_file_size($file_path);
			if($uploaded_file && is_uploaded_file($uploaded_file)){
				// multipart/formdata uploads (POST method uploads)
				if($append_file){
					$success = file_put_contents($file_path,
												 fopen($uploaded_file, 'r'),
												 FILE_APPEND
					);
				} else{
					$success = move_uploaded_file($uploaded_file, $file_path);
				}
			} else{
				// Non-multipart uploads (PUT method support)
				$success = file_put_contents($file_path,
											 fopen('php://input', 'r'),
											 $append_file? FILE_APPEND : 0
				);
			}
			if(!$success){
				Think::halt('文件权限错误：' . $file_path);
			}
			$file_size = $this->get_file_size($file_path, $append_file);
			if($file_size === $file->size){
				$upload_dir = $this->get_upload_path();
				if(!is_dir($upload_dir)){
					mkdir($upload_dir, $this->mkdir_mode, true);
				}
				$save_path = $this->get_file_name($file_path, $name);
				$success   = rename($file_path, $save_path);
				if(!$success){
					Think::halt('文件权限错误：' . $save_path);
				}
				$file->name = str_replace(PICTURE_PATH, '', $save_path);
				$file->url  = PICTURE_URL . '/' . $file->name;
				/*if($this->is_valid_image_file($file_path)){
					// 处理图片
				}*/
			} else{
				$file->size = $file_size;
				if(!$content_range && $this->discard_aborted_uploads){
					unlink($file_path);
					$file->error = ERR_USER_ABORT;
				}
			}
			$this->set_additional_file_properties($file);
		}
		return $file;
	}

	/*
	protected function readfile($file_path){
		$file_size  = $this->get_file_size($file_path);
		$chunk_size = $this->readfile_chunk_size;
		if($chunk_size && $file_size > $chunk_size){
			$handle = fopen($file_path, 'rb');
			while(!feof($handle)){
				echo fread($handle, $chunk_size);
				ob_flush();
				flush();
			}
			fclose($handle);
			return $file_size;
		}
		return readfile($file_path);
	}*/

	/**  */
	protected function get_server_var($id, $default = ''){
		return isset($_SERVER[$id])? $_SERVER[$id] : $default;
	}

	/**  */
	protected function generate_response($content, $print_response = true){
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

	/*  
	protected function get_version_param(){
		return isset($_GET['version'])? basename(stripslashes($_GET['version'])) : null;
	}*/

	/**  */
	protected function get_singular_param_name(){
		return substr($this->param_name, 0, -1);
	}

	/* 
	protected function get_file_name_param(){
		$name = $this->get_singular_param_name();
		return isset($_GET[$name])? basename(stripslashes($_GET[$name])) : null;
	}*/

	/*
	protected function get_file_names_params(){
		$params = isset($_GET[$this->param_name])? $_GET[$this->param_name] : array();
		foreach($params as $key => $value){
			$params[$key] = basename(stripslashes($value));
		}
		return $params;
	}*/

	/*
	protected function get_file_type($file_path){
		switch(strtolower(pathinfo($file_path, PATHINFO_EXTENSION))){
		case 'jpeg':
		case 'jpg':
			return 'image/jpeg';
		case 'png':
			return 'image/png';
		case 'gif':
			return 'image/gif';
		default:
			return '';
		}
	}*/

	/*
	protected function download(){
		switch($this->download_via_php){
		case 1:
			$redirect_header = null;
			break;
		case 2:
			$redirect_header = 'X-Sendfile';
			break;
		case 3:
			$redirect_header = 'X-Accel-Redirect';
			break;
		default:
			header('HTTP/1.1 403 Forbidden');
			return;
		}
		$file_name = $this->get_file_name_param();
		if(!$this->is_valid_file_object($file_name)){
			header('HTTP/1.1 404 Not Found');
			return;
		}
		if($redirect_header){
			header($redirect_header . ': ' . $this->get_download_url($file_name,
																	 $this->get_version_param(),
																	 true
				   )
			);
			return;
		}
		$file_path = $this->get_upload_path($file_name, $this->get_version_param());
		// Prevent browsers from MIME-sniffing the content-type:
		header('X-Content-Type-Options: nosniff');
		if(!preg_match($this->inline_file_types, $file_name)){
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
		} else{
			header('Content-Type: ' . $this->get_file_type($file_path));
			header('Content-Disposition: inline; filename="' . $file_name . '"');
		}
		header('Content-Length: ' . $this->get_file_size($file_path));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($file_path)));
		$this->readfile($file_path);
	}
 	*/

	/**  */
	protected function head(){
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

	/*
	protected function get($print_response = true){
		if($print_response && isset($_GET['download'])){
			$this->download();
			return '';
		}
		$file_name = $this->get_file_name_param();
		if($file_name){
			$response = array(
				$this->get_singular_param_name() => $this->get_file_object($file_name)
			);
		} else{
			$response = array(
				$this->param_name => $this->get_file_objects()
			);
		}
		return $this->generate_response($response, $print_response);
	}*/

	/**  */
	protected function post($print_response = true){
		if(isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE'){
			return $this->delete($print_response);
		}
		$upload = isset($_FILES[$this->param_name])? $_FILES[$this->param_name] : null;
		// Parse the Content-Disposition header, if available:
		$file_name = $this->get_server_var('HTTP_CONTENT_DISPOSITION')? rawurldecode(preg_replace('/(^[^"]+")|("$)/',
																								  '',
																								  $this->get_server_var('HTTP_CONTENT_DISPOSITION'
																								  )
																					 )
		) : null;
		// Parse the Content-Range header, which has the following form:
		// Content-Range: bytes 0-524287/2000000
		$content_range = $this->get_server_var('HTTP_CONTENT_RANGE')? preg_split('/[^0-9]+/',
																				 $this->get_server_var('HTTP_CONTENT_RANGE'
																				 )
		) : null;
		$size          = $content_range? $content_range[3] : null;
		$files         = array();
		if($upload && is_array($upload['tmp_name'])){
			// param_name is an array identifier like "files[]",
			// $_FILES is a multi-dimensional array:
			foreach($upload['tmp_name'] as $index => $value){
				$files[] = $this->handle_file_upload($upload['tmp_name'][$index],
													 $file_name? $file_name : $upload['name'][$index],
													 $size? $size : $upload['size'][$index],
													 $upload['type'][$index],
													 $upload['error'][$index]? ERR_HTTP_UPLOAD : false,
													 $index,
													 $content_range
				);
			}
		} else{
			// param_name is a single object identifier like "file",
			// $_FILES is a one-dimensional array:
			$type = isset($upload['type'])? $upload['type'] : $this->get_server_var('CONTENT_TYPE');
			if(!$size){
				$size = isset($upload['size'])? $upload['size'] : $this->get_server_var('CONTENT_LENGTH');
			}
			$error   = isset($upload['error'])? $upload['error'] : null;
			$tmp     = isset($upload['tmp_name'])? $upload['tmp_name'] : null;
			$fn      = $file_name? $file_name : (isset($upload['name'])? $upload['name'] : null);
			$files[] = $this->handle_file_upload($tmp, $fn, $size, $type, $error, null, $content_range);
		}
		$this->generate_response(array($this->param_name => $files), $print_response);
	}
	/*
	protected function delete($print_response = true){
		$file_names = $this->get_file_names_params();
		if(empty($file_names)){
			$file_names = array($this->get_file_name_param());
		}
		$response = array();
		foreach($file_names as $file_name){
			$file_path = $this->get_upload_path($file_name);
			$success   = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
			if($success){
				foreach($this->image_versions as $version => $options){
					if(!empty($version)){
						$file = $this->get_upload_path($file_name, $version);
						if(is_file($file)){
							unlink($file);
						}
					}
				}
			}
			$response[$file_name] = $success;
		}
		return $this->generate_response($response, $print_response);
	} */
}
