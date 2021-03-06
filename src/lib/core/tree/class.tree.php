<?
/** 
 * File:        <#file#>.php
 * Date:        Wed 10 Jun 2009 12:11:53 PM CEST
 *
 * Library that easily converts an associative array into a clickable tree thru smarty
 * 
 * Changelog:
 *
 * 	[Wed 10 Jun 2009 12:11:53 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   PHP
 *   ===
 *   sutra::get()->tree;   // initialize tree libclass
 *   $data = array( 
 *             array( "root", "/",
 *               array("item1", "http://www.google.com"),
 *               array("item2", "http://www.altavista.com"),
 *               array("sub1", null,
 *                   array("item3", "http://www.google.com"),
 *                   array("item4", "http://www.altavista.com"),
 *               ) 
 *             ),
 *             array("sub1", null,
 *                 array("item3", "http://www.google.com"),
 *                 array("item4", "http://www.altavista.com"),
 *             ) 
 *           );
 *   sutra::get()->tpl->assign("data",$data);
 *
 *  SMARTY
 *  ======
 *  {tree data=$data}  {* you can switch themes by adding tpl="windows"/"doxygen" *}
 * </code>
 *
 * @package sutra 
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
class tree{

  private $json;

  public function __construct(){}

  /** 
   * display        - displays a tree by including/generating some javascripts 
   * 
   * @param string $var description 
   * @return mixed The new value 
   */ 

  function display( $params )
  {
    $sutra = sutra::get();
    assert( is_array($params['data']) );
    if( empty( $params['tpl'] ) )
      $params['tpl'] = "sutra";
    if( empty( $params['id'] ) )
      $params['id']  = "tree";
    $jdata = $sutra->json->encode( $params['data'] );
    $html  = "<div id='tree'></div>\n";
    $html .= "<script type='text/javascript'>";
    $html .= "var baseurl = 'http://".$sutra->_url."';\n";
    $html .= file_get_contents( $sutra->_path."/lib/core/tree/js/tree-src.js" );
    $html .= file_get_contents( $sutra->_path."/lib/core/tree/tpl/{$params['tpl']}/tree_tpl.js" );
    $html .= "new tree( {$jdata}, TREE_TPL, '{$params['id']}'";
    $html .= ( isset( $params['ajaxTarget'] )   ? ", '{$params['ajaxTarget']}'"  : ", false" );
    $html .= ( isset( $params['onClick']    )   ? ", '{$params['onClick']}'"     : ", false" );
    $html .= ( isset( $params['onDelete']   )   ? ", '{$params['onDelete']}'"    : ", false" );
    $html .= ( isset( $params['onSort']     )   ? ", '{$params['onSort']}'"      : ", false" );
    $html .= ( isset( $params['onMouseOver'] )  ? ", '{$params['onMouseOver']}'" : ", false" );
    $html .= ( isset( $params['onMouseOut'] )   ? ", '{$params['onMouseOut']}'"  : ", false" );
    $html .= ( isset( $params['rootUrl']    )   ? ", '{$params['rootUrl']}'"     : ", 'http://{$sutra->_url}'" );
    $html .= ( isset( $params['target']     )   ? ", '{$params['target']}'"      : ", false" );
    $html .= ( isset( $params['parentClick'] )  ? ", '{$params['parentClick']}'" : ", false" );
    $html .= " );";
    $html .= "</script>";
    $html .= "<link rel='stylesheet' href='http://{$sutra->_url}lib/core/tree/css/tree.css' type='text/css' media='screen, projection'/>";
    return $html;
  }

  /**
   * prepareTreeArray         - massage tree (associative array) in a way so it'll serve as perfect input for the tree template function
   * 
   * @param array $tree       associative array like array( "root" => array ( "id" => 3
   *                                                                           "children" => array( 
   *                                                                                                 array( "id"   => 4,
   *                                                                                                        "foo"  => "blah"
   *                                                                                                 ),
   *                                                                                                 // and so on
   *                                                                                               )
   *                                                                          )
   *                                                       )            
   * @access public
   * @return array
   */
  public function prepareTreeArray( $tree, $label_key, $href_key ){
    if( !is_array( $tree ) ) return $tree;
    foreach( $tree as $k => $value ){
      if( is_array( $value['children'] ) && count( $value['children'] ) ){
        $children   = $this->prepareTreeArray( $value['children'], $label_key, $href_key ); 
        $tree[ $k ] = array( $value[ $label_key ], $value[ $href_key ] );
        foreach( $children as $child )
          $tree[ $k ][] = $child;
      }else{
        $tree[ $k ] = array( $value[ $label_key ], $value[ $href_key ] );
      }
    }
    return $tree;
  }


}


?>
