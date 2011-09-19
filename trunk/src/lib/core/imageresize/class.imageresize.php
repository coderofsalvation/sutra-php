<?
/** 
 * File:        class.imageresize.php
 * Date:        Tue Dec 21 13:55:11 2010
 *
 * Resizing of images. Class based on based on Emilio Rodriguez' Thumbnail and Watermark Class, and patches made by IZI services.
 * This is a PHP class that can process an image on the fly by either generate 
 * a thumbnail and/or apply an watermark to the image.
 *
 * The processed image can either be displayed in a page, saved to a file, or returned to a variable.
 * It requires the PHP with support for GD library extension in either version 1 or 2. 
 * If the GD library version 2 is available it the class can manipulate the images in true color, 
 * thus providing better quality of the results of resized images.
 *
 * Features description:
 * - Thumbnail: normal thumbnail generation
 * - Watermark: Text or image in PNG format. Suport multiples positions.
 * - Auto-fitting: adjust the dimensions so that the resized image aspect is not distorted
 * - Scaling: enlarge and shrink the image
 * - Format: JPEG, PNG and GIF are supported, but the watermark image can only be in PNG format as it needs to be transparent
 * - Autodetect the GD library version supported by PHP
 * - Calculate quality factor for a specific file size in JPEG format.
 * - Suport bicubic resample algorithm
 * - Tested: PHP 4 / 5 valid
 *
 * Changelog
 *   [emilio] initial version
 *   [pieter] Now supports transparant png's
 *   [pieter] Now supports transparant gif's aswell
 *
 * @link        http://www.izi-services.nl
 * @author      Emilio Rodriguez <emiliort@gmail.com>
 * @author      Pieter Hensen <pieter@izi-services.nl>
 * @author      Leon van Kammen (Coder of Salvation)<info@leon.vankammen.eu>
 * @copyright   AGPL
 *
 * Changelog:
 *
 * 	[Tue Dec 21 13:55:11 2010] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *	 $thumb=new Thumbnail("source.jpg");        // set source image file
 *
 *	 $thumb->size_width(100);                   // set width for thumbnail, or
 *	 $thumb->size_height(300);                  // set height for thumbnail, or
 *	 $thumb->size_auto(200);                    // set the biggest width or height for thumbnail
 *
 *	 $thumb->quality=75;                        //default 75 , only for JPG format
 *	 $thumb->output_format='JPG';               // JPG | PNG | GIF
 *	 $thumb->jpeg_progressive=0;                // set progressive JPEG : 0 = no , 1 = yes
 *	 $thumb->allow_enlarge=false;               // allow to enlarge the thumbnail
 *	 $thumb->CalculateQFactor(10000);           // Calculate JPEG quality factor for a specific size in bytes
 *	 $thumb->bicubic_resample=true;             // [OPTIONAL] set resample algorithm to bicubic
 *
 *	 $thumb->img_watermark='watermark.png';     // [OPTIONAL] set watermark source file, only PNG format
 *	 $thumb->img_watermark_Valing='TOP';        // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
 *	 $thumb->img_watermark_Haling='LEFT';       // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
 *
 *	 $thumb->txt_watermark='Watermark text';    // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
 *	 $thumb->txt_watermark_color='000000';      // [OPTIONAL] set watermark text color, RGB Hexadecimal
 *	 $thumb->txt_watermark_font=1;              // [OPTIONAL] set watermark text font: 1,2,3,4,5
 *	 $thumb->txt_watermark_Valing='TOP';        // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
 *	 $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
 *	 $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
 *	 $thumb->txt_watermark_Vmargin=10;          // [OPTIONAL] set watermark text vertical margin in pixels
 *
 *	 $thumb->process();                         // generate image
 *
 *	 $thumb->show();                            // show your thumbnail, or
 *	 $thumb->save("thumbnail.jpg");             // save your thumbnail to file, or
 *	 $image = $thumb->dump();                   // get the image
 *
 *	 echo ($thumb->error_msg);                  // print Error Mensage
 * </code>
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 *
 * @license 
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

class imageresize {
  /**
    *@access public
    *@var integer Quality factor for JPEG output format, default 75
    **/
  var $quality = 100;
  /**
    *@access public
    *@var string output format, default JPG, valid values 'JPG' | 'PNG'
    **/
  var $output_format = 'JPG';
  /**
    *@access public
    *@var integer set JPEG output format to progressive JPEG : 0 = no , 1 = yes
    **/
  var $jpeg_progressive = 0;
  /**
    *@access public
    *@var boolean allow to enlarge the thumbnail.
    **/
  var $allow_enlarge = false;

  /**
    *@access public
    *@var string [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
    **/
  var $img_watermark = '';
  /**
    *@access public
    *@var string [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
    **/
  var $img_watermark_Valing = 'TOP';
  /**
    *@access public
    *@var string [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
    **/
  var $img_watermark_Haling = 'LEFT';

  /**
    *@access public
    *@var string [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
    **/
  var $txt_watermark = '';
  /**
    *@access public
    *@var string [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
    **/
  var $txt_watermark_color = '000000';
  /**
    *@access public
    *@var integer [OPTIONAL] set watermark text font: 1,2,3,4,5
    **/
  var $txt_watermark_font = 1;
  /**
    *@access public
    *@var string  [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
    **/
  var $txt_watermark_Valing = 'TOP';
  /**
    *@access public
    *@var string [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
    **/
  var $txt_watermark_Haling = 'LEFT';
  /**
    *@access public
    *@var integer [OPTIONAL] set watermark text horizonatal margin in pixels
    **/
  var $txt_watermark_Hmargin = 10;
  /**
    *@access public
    *@var integer [OPTIONAL] set watermark text vertical margin in pixels
    **/
  var $txt_watermark_Vmargin = 10;
  /**
    *@access public
    *@var bool [OPTIONAL] set resample algorithm to bicubic
    **/
  var $bicubic_resample = false;

  /**
    *@access private
    *@var bool [OPTIONAL] if true, the image will be cropped instead of resized
    **/
  var $crop = false;

  /**
    *@access public
    *@var string [OPTIONAL] set maximun memory usage, default 8 MB ('8M'). (use '16M' for big images)
    **/
  var $memory_limit = '64M';

  /**
    *@access public
    *@var string  errors mensage
    **/
  var $error_msg = '';

  /**
    *@access private
    *@var mixed images
    **/
  var $img;

	function __construct(){ }

  /**
    *open source image
    *@access public
    *@param string filename of the source image file
    *@return boolean
    **/
  function setFile($imgfile) {
		if( !_assert( is_file($imgfile), "{$imgfile} is not a file!") ) return;
    ini_set('memory_limit', $this->memory_limit);
		$this->img["imgfile"] = $imgfile;
    $img_info = getimagesize($imgfile);
    
    //detect image format
    switch ($img_info[2]) {
      case 1 :
        //GIF
        $this->img["format"] = "GIF";
        $this->img["src"] = ImageCreateFromGIF($imgfile);
        break;
      case 2 :
        //JPEG
        $this->img["format"] = "JPEG";
        $this->img["src"] = ImageCreateFromJPEG($imgfile);
        break;
      case 3 :
        //PNG
        $this->img["format"] = "PNG";
        $this->img["src"] = ImageCreateFromPNG($imgfile);
        $this->img["des"] = $this->img["src"];
        break;
      default :
        $this->error_msg = "Not Supported File";
        return false;
    } //case
    $this->img["x"] = $img_info[0]; //original dimensions
    $this->img["y"] = $img_info[1];
    $this->img["x_thumb"] = $this->img["x"]; //thumbnail dimensions
    $this->img["y_thumb"] = $this->img["y"];
    $this->img["des"] = $this->img["src"]; // thumbnail = original
    return true;
  }

  /**
    *set height for thumbnail
    *@access public
    *@param integer height
    *@return boolean
    **/
  function size_height($size = 100) {
      //height
  $this->img["y_thumb"] = $size;
    if ($this->img["y"] > 0) {
      $this->img["x_thumb"] = ($this->img["y_thumb"] / $this->img["y"]) * $this->img["x"];
    } else {
      $this->error_msg = "Invalid size : Y";
      return false;
    }
    if (!$this->allow_enlarge && $this->img["x_thumb"] > $this->img["x"])
      $this->img["x_thumb"] = $this->img["x"];
    if (!$this->allow_enlarge && $this->img["y_thumb"] > $this->img["y"])
      $this->img["y_thumb"] = $this->img["y"];
  }

  /**
    *set width for thumbnail
    *@access public
    *@param integer width
    *@return boolean
    **/
  function size_width($size = 100) {
      //width
		$this->img["x_thumb"] = $size;
    if ($this->img["x"] > 0) {
      $this->img["y_thumb"] = ($this->img["x_thumb"] / $this->img["x"]) * $this->img["y"];
    } else {
      $this->error_msg = "Invalid size : x";
      return false;
    }
    if (!$this->allow_enlarge && $this->img["x_thumb"] > $this->img["x"])
      $this->img["x_thumb"] = $this->img["x"];
    if (!$this->allow_enlarge && $this->img["y_thumb"] > $this->img["y"])
      $this->img["y_thumb"] = $this->img["y"];
  }

  /**
    *set the biggest width or height for thumbnail
    *@access public
    *@param integer width or height
    *@return boolean
    **/
  function size_auto($size = 100) {
      //size
  if ($this->img["x"] >= $this->img["y"]) {
      $this->size_width($size);
    } else {
      $this->size_height($size);
    }
    if (!$this->allow_enlarge && $this->img["x_thumb"] > $this->img["x"])
      $this->img["x_thumb"] = $this->img["x"];
    if (!$this->allow_enlarge && $this->img["y_thumb"] > $this->img["y"])
      $this->img["y_thumb"] = $this->img["y"];
  }

  /**
  *set the biggest width and height for thumbnail
  *@access public
  *@param integer width or height
  *@return boolean
  **/
  function size_width_height($width = 100, $height = 100) {
    $ratio = $this->img["x"] / $this->img["y"];
    $target_ratio = $width / $height;

    if ($ratio > $target_ratio) {
      $this->size_width($width);
      if ($this->crop) $this->crop_size = $width;
    } else {
      $this->size_height($height);
      if ($this->crop) $this->crop_size = $height;
    }

    if ($this->crop) $this->calculate_crop_position();

    if (!$this->allow_enlarge && $this->img["x_thumb"] > $this->img["x"])
      $this->img["x_thumb"] = $this->img["x"];
    if (!$this->allow_enlarge && $this->img["y_thumb"] > $this->img["y"])
      $this->img["y_thumb"] = $this->img["y"];
  }

  function calculate_crop_position ()
  {
    if ($this->crop && $this->crop_size > 0) {

      if ($this->crop_size > $this->img["x_thumb"]) {
        $this->crop_y_pos = ($this->img["y"] - $this->img["x"]) / 2;
        $this->crop_source_size = $this->img["x"];
        $this->crop_x_pos = 0;
      } else {
        $this->crop_x_pos = ($this->img["x"] - $this->img["y"]) / 2;
        $this->crop_source_size = $this->img["y"];
        $this->crop_y_pos = 0;
      }

      $this->img["x_thumb"] = $this->crop_size;
      $this->img["y_thumb"] = $this->crop_size;
    }

  }

  /**
    *show your thumbnail, output image and headers
    *@access public
    *@return void
    **/
  function show() {
    //show thumb
    Header("Content-Type: image/".$this->img["format"]);
    if ($this->output_format == "PNG") { //PNG
      imagePNG($this->img["des"]);
    } elseif ( $this->output_format == "JPG" ) {
      imageinterlace($this->img["des"], $this->jpeg_progressive);
      imageJPEG($this->img["des"], "", $this->quality);
    } else {
      imageGIF ( $this->img["des"] );
    }
  }

  /**
    *return the result thumbnail
    *@access public
    *@return mixed
    **/
  function dump() {
    //dump thumb
    return $this->img["des"];
  }

  /**
    *save your thumbnail to file
    *@access public
    *@param string output file name
    *@return boolean
    **/
  function save($save = "") {
		if( strlen($save) == 0 ) $save = $this->img["imgfile"];
    if ($this->output_format == "PNG") { //PNG
      imagePNG($this->img["des"], "$save");
    } elseif ( $this->output_format == "JPG" ) {
      imageinterlace($this->img["des"], $this->jpeg_progressive);
      imageJPEG($this->img["des"], "$save", $this->quality);
    } else {
      imageGIF($this->img["des"], "$save");
    }
    return true;
  }

  /**
    *generate image
    *@access public
    *@return boolean
    **/
  function process() {

    $X_des = $this->img["x_thumb"];
    $Y_des = $this->img["y_thumb"];

    if ($this->checkgd2()) {
      //if (false) {

      $this->img["des"] = ImageCreateTrueColor($X_des, $Y_des);

      if ( $this->output_format == "GIF" ) {
        imagealphablending( $this->img["des"], false); // turn off the alpha blending to keep the alpha channel
        imagesavealpha( $this->img["des"], true ); // Anti-alias the edges
        $background = imagecolorallocatealpha( $this->img["des"], 0, 0, 0, 127 );
        ImageColorTransparent( $this->img["des"], $background); // make the new temp image all transparent
        imagefilledrectangle( $this->img["des"], 0, 0, $X_des, $Y_des, $background );
      }

      if ( $this->output_format == "PNG" ) {
        $background = imagecolorallocate( $this->img["des"], 0, 0, 0 );
        ImageColorTransparent( $this->img["des"], $background); // make the new temp image all transparent
        imagealphablending( $this->img["des"], false); // turn off the alpha blending to keep the alpha channel
        imagesavealpha( $this->img["des"], true ); // Anti-alias the edges
      }

      if ($this->txt_watermark != '') {
        $red = $green = $blue = 0;
        sscanf($this->txt_watermark_color, "%2x%2x%2x", $red, $green, $blue);
        $txt_color = imageColorAllocate($this->img["des"], $red, $green, $blue);
      }

      if (!$this->bicubic_resample) {
        if ($this->crop)
          imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, $this->crop_x_pos, $this->crop_y_pos, $X_des, $Y_des,$this->crop_source_size, $this->crop_source_size);
        else
          imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $X_des, $Y_des, $this->img["x"], $this->img["y"]);
      } else {
        $this->imageCopyResampleBicubic($this->img["des"], $this->img["src"], 0, 0, 0, 0, $X_des, $Y_des, $this->img["x"], $this->img["y"]);
      }

      if ($this->img_watermark != '' && file_exists($this->img_watermark)) {
        $this->img["watermark"] = ImageCreateFromPNG($this->img_watermark);
        $this->img["x_watermark"] = imagesx($this->img["watermark"]);
        $this->img["y_watermark"] = imagesy($this->img["watermark"]);
        imagecopyresampled($this->img["des"], $this->img["watermark"], $this->calc_position_H(), $this->calc_position_V(), 0, 0, $this->img["x_watermark"], $this->img["y_watermark"], $this->img["x_watermark"], $this->img["y_watermark"]);
      }

      if ($this->txt_watermark != '') {
        imagestring($this->img["des"], $this->txt_watermark_font, $this->calc_text_position_H(), $this->calc_text_position_V(), $this->txt_watermark, $txt_color);
      }
    } else {
      $this->img["des"] = ImageCreate($X_des, $Y_des);
      if ($this->txt_watermark != '') {
        $red = $green = $blue = 0;
        sscanf($this->txt_watermark_color, "%2x%2x%2x", $red, $green, $blue);
        $txt_color = imageColorAllocate($this->img["des"], $red, $green, $blue);
      }
      // pre copy image, allocating color of water mark, GD < 2 can't resample colors
      if ($this->img_watermark != '' && file_exists($this->img_watermark)) {
        $this->img["watermark"] = ImageCreateFromPNG($this->img_watermark);
        $this->img["x_watermark"] = imagesx($this->img["watermark"]);
        $this->img["y_watermark"] = imagesy($this->img["watermark"]);
        imagecopy($this->img["des"], $this->img["watermark"], $this->calc_position_H(), $this->calc_position_V(), 0, 0, $this->img["x_watermark"], $this->img["y_watermark"]);
      }
      imagecopyresized($this->img["des"], $this->img["src"], 0, 0, 0, 0, $X_des, $Y_des, $this->img["x"], $this->img["y"]);
      imagecopy($this->img["des"], $this->img["watermark"], $this->calc_position_H(), $this->calc_position_V(), 0, 0, $this->img["x_watermark"], $this->img["y_watermark"]);
      if ($this->txt_watermark != '') {
        imagestring($this->img["des"], $this->txt_watermark_font, $this->calc_text_position_H(), $this->calc_text_position_V(), $this->txt_watermark, $txt_color); // $this->txt_watermark_color);
      }
    }
    $this->img["src"] = $this->img["des"];
    $this->img["x"] = $this->img["x_thumb"];
    $this->img["y"] = $this->img["y_thumb"];

  }

  /**
    *Calculate JPEG quality factor for a specific size in bytes
    *@access public
    *@param integer maximun file size in bytes
    **/
  function CalculateQFactor($size) {
    //based on: JPEGReducer class version 1,  25 November 2004,  Author: huda m elmatsani, Email :justhuda@netscape.net

    //calculate size of each image. 75%, 50%, and 25% quality
    ob_start();
    imagejpeg($this->img["des"], '', 75);
    $buffer = ob_get_contents();
    ob_end_clean();
    $size75 = strlen($buffer);
    ob_start();
    imagejpeg($this->img["des"], '', 50);
    $buffer = ob_get_contents();
    ob_end_clean();
    $size50 = strlen($buffer);
    ob_start();
    imagejpeg($this->img["des"], '', 25);
    $buffer = ob_get_contents();
    ob_end_clean();
    $size25 = strlen($buffer);

    //calculate gradient of size reduction by quality
    $mgrad1 = 25 / ($size50 - $size25);
    $mgrad2 = 25 / ($size75 - $size50);
    $mgrad3 = 50 / ($size75 - $size25);
    $mgrad = ($mgrad1 + $mgrad2 + $mgrad3) / 3;
    //result of approx. quality factor for expected size
    $q_factor = round($mgrad * ($size - $size50) +50);

    if ($q_factor < 25) {
      $this->quality = 25;
    }
    elseif ($q_factor > 100) {
      $this->quality = 100;
    } else {
      $this->quality = $q_factor;
    }
  }

  /**
    *@access private
    *@return integer
    **/
  function calc_text_position_H() {
    $W_mark = imagefontwidth($this->txt_watermark_font) * strlen($this->txt_watermark);
    $W = $this->img["x_thumb"];
    switch ($this->txt_watermark_Haling) {
      case 'CENTER' :
        $x = $W / 2 - $W_mark / 2;
        break;
      case 'RIGHT' :
        $x = $W - $W_mark - ($this->txt_watermark_Hmargin);
        break;
      default :
      case 'LEFT' :
        $x = 0 + ($this->txt_watermark_Hmargin);
        break;
    }
    return $x;
  }

  /**
    *@access private
    *@return integer
    **/
  function calc_text_position_V() {
    $H_mark = imagefontheight($this->txt_watermark_font);
    $H = $this->img["y_thumb"];
    switch ($this->txt_watermark_Valing) {
      case 'CENTER' :
        $y = $H / 2 - $H_mark / 2;
        break;
      case 'BOTTOM' :
        $y = $H - $H_mark - ($this->txt_watermark_Vmargin);
        break;
      default :
      case 'TOP' :
        $y = 0 + ($this->txt_watermark_Vmargin);
        break;
    }
    return $y;
  }

  /**
    *@access private
    *@return integer
    **/
  function calc_position_H() {
    $W_mark = $this->img["x_watermark"];
    $W = $this->img["x_thumb"];
    switch ($this->img_watermark_Haling) {
      case 'CENTER' :
        $x = $W / 2 - $W_mark / 2;
        break;
      case 'RIGHT' :
        $x = $W - $W_mark;
        break;
      default :
      case 'LEFT' :
        $x = 0;
        break;
    }
    return $x;
  }

  /**
    *@access private
    *@return integer
    **/
  function calc_position_V() {
    $H_mark = $this->img["y_watermark"];
    $H = $this->img["y_thumb"];
    switch ($this->img_watermark_Valing) {
      case 'CENTER' :
        $y = $H / 2 - $H_mark / 2;
        break;
      case 'BOTTOM' :
        $y = $H - $H_mark;
        break;
      default :
      case 'TOP' :
        $y = 0;
        break;
    }
    return $y;
  }

  /**
    *@access private
    *@return boolean
    **/
  function checkgd2() {
    // TEST the GD version
    if (extension_loaded('gd2') && function_exists('imagecreatetruecolor')) {
      return false;
    } else {
      return true;
    }
  }

  function imageCopyResampleBicubic($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
    $scaleX = ($src_w -1) / $dst_w;
    $scaleY = ($src_h -1) / $dst_h;
    $scaleX2 = $scaleX / 2.0;
    $scaleY2 = $scaleY / 2.0;
    $tc = imageistruecolor($src_img);

    for ($y = $src_y; $y < $src_y + $dst_h; $y ++) {
      $sY = $y * $scaleY;
      $siY = (int) $sY;
      $siY2 = (int) $sY + $scaleY2;

      for ($x = $src_x; $x < $src_x + $dst_w; $x ++) {
        $sX = $x * $scaleX;
        $siX = (int) $sX;
        $siX2 = (int) $sX + $scaleX2;

        if ($tc) {
          $c1 = imagecolorat($src_img, $siX, $siY2);
          $c2 = imagecolorat($src_img, $siX, $siY);
          $c3 = imagecolorat($src_img, $siX2, $siY2);
          $c4 = imagecolorat($src_img, $siX2, $siY);

          $r = (($c1 + $c2 + $c3 + $c4) >> 2) & 0xFF0000;
          $g = ((($c1 & 0xFF00) + ($c2 & 0xFF00) + ($c3 & 0xFF00) + ($c4 & 0xFF00)) >> 2) & 0xFF00;
          $b = ((($c1 & 0xFF) + ($c2 & 0xFF) + ($c3 & 0xFF) + ($c4 & 0xFF)) >> 2);

          imagesetpixel($dst_img, $dst_x + $x - $src_x, $dst_y + $y - $src_y, $r + $g + $b);
        } else {
          $c1 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX, $siY2));
          $c2 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX, $siY));
          $c3 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX2, $siY2));
          $c4 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX2, $siY));

          $r = ($c1['red'] + $c2['red'] + $c3['red'] + $c4['red']) << 14;
          $g = ($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) << 6;
          $b = ($c1['blue'] + $c2['blue'] + $c3['blue'] + $c4['blue']) >> 2;

          imagesetpixel($dst_img, $dst_x + $x - $src_x, $dst_y + $y - $src_y, $r + $g + $b);
        }
      }
    }
  }
}
?>
