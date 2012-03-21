<?php
/**
 * @file image.class.php
 * @brief Contains the image class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup forms core
 * @brief Image manipulation class 
 * 
 * Supported image types are **jpg**, **png** and **gif** 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class image {
 
	/**
	 * @brief image identifier 
	 */
	private $_image;

	/**
	 * @brief image type 
	 */
	private $_type;
 
	/**
	 * @brief Loads an image 
	 * 
	 * @param string $filepath image file path 
	 * @return void
	 */
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

	/**
	 * @brief Returns the file type 
	 * 
	 * @return void
	 */
	public function type() {
		return $this->_type;
	}

	/**
	 * @brief Saves image to filepath 
	 * 
	 * @param string $filepath image file path
	 * @param mixed $image_type image type identifier, default IMAGETYPE_JPEG
	 * @param int $compression image compression, deafult 75
	 * @param string $permissions file permissions
	 * @return void
	 */
	public function save($filepath, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
 
		if($image_type == IMAGETYPE_JPEG)
			imagejpeg($this->_image, $filepath, $compression);
		elseif($image_type == IMAGETYPE_GIF)
			imagegif($this->_image, $filepath);
		elseif($image_type == IMAGETYPE_PNG)
			imagepng($this->_image, $filepath);
		if($permissions != null)
			chmod($filepath, $permissions);

	}

	/**
	 * @brief Outputs directly the raw image stream 
	 * 
	 * @param mixed $image_type image type identifier, default IMAGETYPE_JPEG
	 * @return void
	 */
	public function output($image_type=IMAGETYPE_JPEG) {
 
		if($image_type == IMAGETYPE_JPEG )
			imagejpeg($this->_image);
		elseif($image_type == IMAGETYPE_GIF)
			imagegif($this->_image);
		elseif($image_type == IMAGETYPE_PNG)
			imagepng($this->_image);

	}

	/**
	 * @brief Gets image width 
	 * 
	 * @return the width of the image
	 */
	public function getWidth() {
 
		return imagesx($this->_image);

	}
	
	/**
	 * @brief Gets image height 
	 * 
	 * @return the height of the image
	 */
	public function getHeight() {
 
		return imagesy($this->_image);

	}

	/**
	 * @brief Resizes the image at the given height 
	 * 
	 * @param int $height resizing height
	 * @param mixed $opts
	 *   Associative array of options:
	 *   - **enlarge**: bool default false. Allow image enlargement 
	 * @return void
	 */
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
 
	/**
	 * @brief Resizes the image at the given width 
	 * 
	 * @param int $width resizing width
	 * @param mixed $opts
	 *   Associative array of options:
	 *   - **enlarge**: bool default false. Allow image enlargement 
	 * @return void
	 */
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

	/**
	 * @brief Scales the image to the given percentage 
	 * 
	 * @param int $scale scale percentage
	 * @return void
	 */
	public function scale($scale) {
      
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);

	}
 

	/**
	 * @brief Resizes the image to the given width and height 
	 * 
	 * @param int $width image width
	 * @param int $height image height
	 * @return void
	 */
	public function resize($width, $height) {
	
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->_image = $new_image;

	}      
 
}

?>
