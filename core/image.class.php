<?php

class image {
 
	private $_image, $_type;
 
	public function load($filepath) {
	
		$image_info = getimagesize($filepath);
		$this->_type = $image_info[2];
	   
		if($this->_type == IMAGETYPE_JPEG)
			$this->_image = imagecreatefromjpeg($filepath);
		elseif($this->_type == IMAGETYPE_GIF)
			$this->_image = imagecreatefromgif($filepath);
		elseif($this->_type == IMAGETYPE_PNG)
			$this->_image = imagecreatefrompng($filepath);
	}

	public function type() {
		return $this->_type;
	}

	public function save($filepath, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
 
		if($image_type == IMAGETYPE_JPEG)
			imagejpeg($this->_image,$filepath,$compression);
		elseif($image_type == IMAGETYPE_GIF)
			imagegif($this->_image,$filepath);
		elseif($image_type == IMAGETYPE_PNG)
			imagepng($this->_image,$filepath);
		if($permissions != null)
			chmod($filepath,$permissions);

	}

	public function output($image_type=IMAGETYPE_JPEG) {
 
		if($image_type == IMAGETYPE_JPEG )
			imagejpeg($this->_image);
		elseif($image_type == IMAGETYPE_GIF)
			imagegif($this->_image);
		elseif($image_type == IMAGETYPE_PNG)
			imagepng($this->_image);

	}

	public function getWidth() {
 
		return imagesx($this->_image);

	}

	public function getHeight() {
 
		return imagesy($this->_image);

	}

	public function resizeToHeight($height, $opts=null) {
 
		$enlarge = gOpt($opts, "enlarge", false);

		if($enlarge || $height<$this->getHeight()) {
			$ratio = $height / $this->getHeight();
			$width = $this->getWidth() * $ratio;
		}
		else {
			$height = $this->getHeight();
			$width = $this->getWidth();
		}

		$this->resize($width,$height);

	}
 
	public function resizeToWidth($width, $opts=null) {
	
		$enlarge = gOpt($opts, "enlarge", false);

		if($enlarge || $width<$this->getWidth()) {
			$ratio = $width / $this->getWidth();
			$height = $this->getheight() * $ratio;
		}
		else {
			$height = $this->getHeight();
			$width = $this->getWidth();
		}

		$this->resize($width,$height);

	}

	public function scale($scale) {
      
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);

	}
 

	public function resize($width, $height) {
	
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->_image = $new_image;

	}      
 
}

?>
