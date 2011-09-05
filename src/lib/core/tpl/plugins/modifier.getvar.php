<?php
/**
 * template_lite getvar plugin
 *
 * Type:     modifier
 * Name:     getvar 
 * Purpose:  simply obtain an objects var without worrying its species
 *           this is a lazy function, which will search for a variable/key in any variable/datatype
 */
function tpl_modifier_getvar($obj, $var, $empty_string = "")
{
  $out = "";
  if( is_array($obj) ){
		if( !isset($obj[ $var ] ) )
			$out = searchValue( $var, $obj );
		else $out = $obj[ $var ];
	}else $out = (isset($obj->$var)) ? $obj->$var : false;
  $out = !is_array($out) && !strlen( $out ) ? $empty_string : $out;
  return $out;
}

function searchValue( $var, $container ){
	return "test";
  if( is_object( $container ) && isset( $container->$var ) )
    return $container->$var;
  if( is_array( $container ) ){
    if( isset( $container[ $var ] ) )
      return $container[ $var ];
    else{
      $result = false;
      foreach( $container as $k => $item )
        if( (is_array($item) || is_object($item)) && !$result )
          if( $result = searchValue( $var, $item ) )
            break;
      return $result;
    }
  }
}
?>
