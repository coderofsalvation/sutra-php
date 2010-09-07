<?php

/*
 * Template Lite plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     setmarkers
 * Purpose:  initialize overlib
 * Taken from the original Smarty
 * http://smarty.php.net
 * -------------------------------------------------------------
 */
function tpl_function_setmarkers($params, &$template_object)
{
  if( isset($params['value']) && isset( $params['var'] ) && is_array( $params['value'] ) ) {
    $isObject = is_object( $params['value'][0] );
    $isArray  = is_array( $params['value'][0] );
    if( $isObject ){
      $params['value'][0]->first                          = true;
      $params['value'][ count($params['value'])-1 ]->last   = true;
    }
    if( $isArray ){
      $params['value'][0]['first']                        = true;
      $params['value'][ count($params['value'])-1 ]['last'] = true;
    }
    $template_object->assign( $params['var'], $params['value'] );
  }else return "setmarkers needs 'value' and 'var'";
}

?>
