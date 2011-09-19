<?
/**
 * Classname    : class.imagefont.php
 * Function     :
 * Date         : Fri Dec 08 12:18:41 CET 2006
 * Company      : IZI services
 * Usage        : text-image rendering
 *
 * Changelog    : 01-09-2006 made basic layout
 * Changelog    : 16-02-2007 cleanup + auto size
 * Changelog    : 30-10-2010 added smarty function
 *
 * @author    Leon van Kammen <leon@izi-services.nl>
 * @author    Johan Adriaans <johan@izi-services.nl>
 * @copyright	Leon van Kammen / Johan adriaans 2006
 * @version	  1.1
 * <example>
 *   {imagefont tag="h1" text="leon du star" font="Arista.ttf" fontsize="34" bgcolor1="FFFFFF" fgcolor1="222222" reflection=true fade_start=30 fade_end=0 fade_height=20}
 *   {imagefont tag="h1" text=`$page.title|ucfirst` font="Arial.ttf" fontsize="21" bgcolor1="FFFFFF" fgcolor1="888888" pitch_width=1.2 truncate=20}
 * </example>
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

class imagefont{

  /**
    *@access public
    *@var string where the ttfs are located
    **/
  var $ttf_path = "../../../data/ttf";

  /**
    *@access public
    *@var string this is the container for the text
    **/
  var $text;

  /**
    *@access public
    *@var int height
    **/
  var $image;

  /**
    *@access public
    *@var image types (default is GIF)
    **/
  var $imagetype_supported 	= array("GIF","JPG","PNG");

  var $imagetype 		= "GIF";
  /**
    *@access public
    *@var float pitch these vars to achieve better automatic generated widths/heights
    *    for your font
    **/

  var $pitch_width		= 1.1;

  var $pitch_height	  = 0.77;
  
  var $height = 0;
  var $width  = 0;

  function __construct()
  {
    sutra::get()->tpl->register_function( "imagefont", array( &$this, "tplImageFont"));
  }

  function tplImageFont( $params, $tpl ){
    $sutra = sutra::get();
    if( !strlen($params['text']) ) return "";
    $transparent = !isset($params['reflection']);
    $filetype = ($transparent) ? 'png' : 'gif';
    $cache_folder = realpath( $sutra->_path."/data/cache");
    $code = md5( implode( ' ', $params ) );
    $file = "{$cache_folder}/{$code}.{$filetype}";
    if ( !is_file( $file ) ) {
      $this->pitch_width  = isset($params['pitch_width']) ? $params['pitch_width'] : $this->pitch_width;
      $this->pitch_height = isset($params['pitch_height']) ? $params['pitch_height'] : $this->pitch_height;
      _log(array($this->pitch_width,$this->pitch_height));
      $params['text'] = str_replace( "+", " ", $params['text'] );
      $params['fgcolor1'] = strtolower( $params['fgcolor1'] );
      $params['fgcolor1'] = preg_replace( "/[^0-9abcdef]/i", "", $params['fgcolor1'] );
      $params['text']     = isset($params['truncate']) && (strlen($params['text']) > $params['truncate']) ?
                            substr( $params['text'], 0, $params['truncate'] ).".." : $params['text'];
      $this->setText( $params['text'] );
      $this->setImageType( $filetype );
      $this->process( $params['font'], 
                    $params['fontsize'], 
                    false, 
                    false, 
                    "#" . $params['fgcolor1'], 
                    "#" . $params['bgcolor1'],
                    0,
                    0,
                    0,
                    $transparent,
                    false,
                    isset($params['fgcolor2']),
                    "#" . (isset($params['fgcolor2']) ? $params['fgcolor2'] : "000000"),
                    "#" . (isset($params['bgcolor2']) ? $params['bgcolor2'] : "FFFFFF"),
                    isset($params['fgcolor3']),
                    "#" . (isset($params['fgcolor3']) ? $params['fgcolor3'] : "555555"),
                    "#" . (isset($params['bgcolor3']) ? $params['bgcolor3'] : "FFFFFF") );
      $this->save( $file );
    }
    $output  = "<{$params['tag']} id='{$code}'>{$params['text']}</{$params['tag']}>\n";
    $output .= "<script type='text/javascript'>";
    $output .= "document.getElementById('{$code}').innerHTML = '';\n";
    $output .= "var img = document.createElement('img');\n";
    if( isset($params['reflection'])  ){
      _assert( is_object($sutra->reflection), "reflection lib not installed! please install" );
      $output .= "img.src = 'http://{$sutra->_url}lib/core/reflection/reflect_v21.php?img=../../../data/cache/{$code}.{$filetype}&cache=true&merge=1&bgc={$params['bgcolor1']}";
      $output .= isset($params['fade_start']) ? "&fade_start={$params['fade_start']}%" : "";
      $output .= isset($params['fade_end'])   ? "&fade_end={$params['fade_end']}%" : "";
      $output .= isset($params['fade_height'])   ? "&height={$params['fade_height']}" : "";
      $output .= "';\n";
    }else
      $output .= "img.src = 'http://{$sutra->_url}data/cache/{$code}.{$filetype}';\n";
    $output .= "document.getElementById('{$code}').appendChild( img );\n";
    $output .= "</script>";
    return $output;
    //
    //header("Content-type: image/" . strtolower($filetype) );
    //readfile( $file );
    //exit;
  }

  function setImageType( $type )
  {
    $type = strtoupper($type);
    if( in_array( $type, $this->imagetype_supported) )
      $this->imagetype = $type;
  }

  function getImageTypes()
  {
    return $this->imagetype_supported;
  }

  function setText( $text )
  {
    $this->text = $text;
  }

  function process( 	$ttf_file, 		                   // use which ttf-file?
                      $fontsize, 		                   // how big?
                      $width=false, 		               // which width/height?
                      $height=false,
                      $color = "#888888",	             // [OPTIONAL] which color?
                      $bgcolor = "#000000",	           // [OPTIONAL] which color?
                      $xoffset = 0,		                 // [OPTIONAL] where x,y to start
                      $yoffset = 0,
                      $rotation = 0,	                 // [OPTIONAL] rotation angle in degrees
                			$transparent = false,            // [OPTIONAL] transparency?
                			$transparent_flip = false,       // [OPTIONAL] switch between transparent bg/fg
                			$rollover = false,               // [OPTIONAL] create css rollover image? (=2x width)
                			$rollover_color = "#FF00FF",     // [OPTIONAL] rollover foreground color
                			$rollover_bgcolor = "#00FF00",   // 
                			$rollover2 = false,              // [OPTIONAL] create 2nd 2nd css rollover image? (=2x width)
                			$rollover2_color = "#FF44FF",    // [OPTIONAL] rollover foreground color
                			$rollover2_bgcolor = "#FFFF00") {// 
    if( strlen($color) > 7 || strlen($this->text) == 0 ) return;
    $color = $this->getColor($color);
    $bgcolor = $this->getColor($bgcolor);
    $rollover_color = $this->getColor($rollover_color);
    $rollover2_color = $this->getColor($rollover2_color);
    $rollover_bgcolor = $this->getColor($rollover_bgcolor);
    $rollover2_bgcolor = $this->getColor($rollover2_bgcolor);
    // set fontpath
    $fontpath = dirname(__FILE__)."/{$this->ttf_path}/";
    //_log("fontpath = ".$fontpath);
    //putenv('GDFONTPATH=' . realpath($fontpath));

    // calculate the dimensions
    $size_array = imagettfbbox( $fontsize, $rotation, $fontpath . '/' . $ttf_file, $this->text);
    $this->height = (!$height)? ($fontsize+($fontsize*$this->pitch_height)) : $height;
    if( $rollover && ! $rollover2 )
      $this->height *= 2;
    if( $rollover && $rollover2 )
      $this->height *= 3;
    
    $this->width = !$width ? ($size_array[2] + 1)*$this->pitch_width : $width;
    $this->image = imagecreatetruecolor($this->width, $this->height );
		$this->drawImage( $color, 
		                  $bgcolor, 
		                  $transparent, 
		                  $transparent_flip, 
		                  $fontsize, 
		                  $rotation,
		                  $xoffset,
		                  $yoffset,
                      $fontpath . '/' . $ttf_file,
                      true );
    if( $rollover )   
    $this->drawImage( $rollover_color, 
                      $rollover_bgcolor, 
                      $transparent, 
                      $transparent_flip, 
                      $fontsize, 
                      $rotation,
                      $xoffset,
                      $this->height/(($rollover2) ? 3 : 2),
                      $fontpath . '/' . $ttf_file,
                      true );
    if( $rollover2 ) 
    $this->drawImage( $rollover2_color, 
                      $rollover2_bgcolor, 
                      $transparent, 
                      $transparent_flip, 
                      $fontsize, 
                      $rotation,
                      $xoffset,
                      ($this->height/3)*2,
                      $fontpath . '/' . $ttf_file,
                      true );
  }
  
  function drawImage( $color, 
                      $bgcolor, 
                      $transparent, 
                      $transparent_flip, 
                      $fontsize, 
                      $rotation, 
                      $xoffset, 
                      $yoffset, 
                      $ttf_file,
                      $drawbg = true  ){
		if( $transparent ){
			if( !$transparent_flip ){
	    	$bgcolor_alloc = imagecolorallocatealpha($this->image, $bgcolor[0],$bgcolor[1],$bgcolor[2],127);
	    	$color_alloc = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
			}else{
	    	$bgcolor_alloc = imagecolorallocate($this->image, $bgcolor[0],$bgcolor[1],$bgcolor[2]);
	    	$color_alloc = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2],127);
			}
    	imagealphablending($this->image, false); 
    	imagesavealpha($this->image, true);
		}else{
	    $bgcolor_alloc = imagecolorallocate($this->image, $bgcolor[0],$bgcolor[1],$bgcolor[2]);
  	  $color_alloc = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
		}
    // create background
    if( $drawbg ) imagefilledrectangle($this->image, $xoffset, $yoffset, $this->width, $this->height, $bgcolor_alloc);

    // draw text
    $result = imagettftext(	$this->image, $fontsize, $rotation, $xoffset, $yoffset + $fontsize, $color_alloc, $ttf_file, $this->text );
    
    
  }

  /*
   * if $htmlcolor = "#335533" or "335533" output will be array(48,80,48)
   */
  function getColor( $htmlcolor )
  {
      $color = array(
        ( strlen($htmlcolor) == 7 ) ? hexdec("{$htmlcolor[1]}{$htmlcolor[2]}") : hexdec("{$htmlcolor[0]}{$htmlcolor[1]}"),
        ( strlen($htmlcolor) == 7 ) ? hexdec("{$htmlcolor[3]}{$htmlcolor[4]}") : hexdec("{$htmlcolor[2]}{$htmlcolor[3]}"),
        ( strlen($htmlcolor) == 7 ) ? hexdec("{$htmlcolor[5]}{$htmlcolor[6]}") : hexdec("{$htmlcolor[4]}{$htmlcolor[5]}"),
      );
      return $color;
  }

  function show()
  {
    header("Content-type: image/" . strtolower($this->imagetype) );
    if( $this->imagetype == "PNG" ) imagepng($this->image);
    if( $this->imagetype == "JPG" ) imagejpeg($this->image);
    if( $this->imagetype == "GIF" ) imagegif($this->image);
    imagedestroy($this->image);
  }

  function save($filename)
  {
    if( $this->imagetype == "PNG" ) imagepng($this->image,$filename);
    if( $this->imagetype == "JPG" ) imagejpeg($this->image,$filename);
    if( $this->imagetype == "GIF" ) imagegif($this->image,$filename);
    imagedestroy($this->image);
  } 

  function getTTFFonts()
  {
    $ignore = array(); // optional (put hidden fonts here)
    $dir = dirname(__FILE__)."/".$this->ttf_path;

    if( file_exists( $dir ) ) {
      $handle = opendir($dir);
      if($handle) {
        while(false !== ($file = readdir($handle)))
          if($file != '.' && $file != '..' && !in_array($file, $ignore))
            $files[] = $file;
        if( count($files) > 0) sort($files);
      }
    closedir($handle);
    }
    return $files;
  }
  
}
?>
