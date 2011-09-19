<?php
/** 
 * File:        functions.php
 * Date:        Mon Sep 19 17:02:28 2011
 *
 * global utility functions
 * 
 * Changelog:
 *
 * 	[Mon Sep 19 17:02:28 2011] 
 *		first sketch from scratch
 *
 * @todo description
 *
 *
 * @version $id$
 * @copyright 2011 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
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

include_once( dirname( __FILE__)."/../../sutra.class.php" );

/** 
 * _assert - nice assertion function to check incoming and outgoing params in functions
 * 
 * @param bool $expr something to assert (has to be true!)
 * @return string $msg assertion message
 */ 
function _assert( $expr, $msg ){
  $msg = "Assertion failed!\n\n {$msg}";
  if( !$expr ){
    ob_start();
    debug_print_backtrace();
    $backtrace = ob_get_contents();
    ob_end_clean();
    $backtrace = str_replace( sutra::get()->_path, "", $backtrace );
    $msg .= "\n\n";
    //$msg = $msg.str_replace(array(" called"," at"),array(" \n\tcalled"," \n\tat"), $backtrace );
    trigger_error( $msg, E_USER_WARNING );
  }
  return $expr;
}

/**
 * _popup     - popup which displays content of mixed variable (handy debugging)
 */
function _popup( $arg )
{
  ob_start();
  print_r( $arg );
  $out = ob_get_contents();
  ob_end_clean();
  $out = addslashes( $out );
  $out = str_replace("\n","\\n",$out);
  $out = str_replace("\r","",$out);
  print "<script type='text/javascript'> alert('".$out."'); </script>";
  return $arg;
}

/* _log - logs strings into file, and truncates filesize automatically
 *
 */
function _log( $input, $section = false, $file = false, $size = -1 ){
	if( $size == -1 && sutra::get()->yaml->cfg['global']['logfilesize'] )
		$size = sutra::get()->yaml->cfg['global']['logfilesize'];
  if( !$file )
    $file = sutra::get()->_path ."/data/log.txt";
  // format output
  if( is_array( $input ) || is_object( $input ) ){
    ob_start();
    print_r($input);
    $input = ob_get_contents();   
    ob_end_clean();
  }
  date_default_timezone_set("Europe/Amsterdam"); // to prevent date() warning (error handler is not actived yet)
  $input    = date( DATE_RFC822, time())." SUTRA > ". ($section ? "({$section}) " : "") . $input;
  // write line
  $f        = fopen( $file, "a+" );
  fwrite( $f, $input."\n" );
  fclose( $f );
  // cut log
	if( $size > 0 ){
		$fsize    = filesize( $file );
		if( $fsize < $size )
				return;
		$f = fopen($file, "r+");
		fseek( $f, $fsize - $size );
		$log      = '';
		fgets($f,1024); /* Skip incomplete first line */
		while( ($chunk=fread($f,4096)) )
				$log  .= $chunk;
		fseek( $f, 0 );
		ftruncate( $f, strlen($log) );
		fwrite( $f, $log );
		fclose($f);
  }
  return $input;
}

?>
