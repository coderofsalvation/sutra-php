<?
/** 
 * File:        class.datagris.php
 * Date:        Tue 29 Jun 2010 09:39:22 PM CEST
 *
 * Generates uniform datagrids very easily.
 * 
 * Changelog:
 *
 * 	[Tue 29 Jun 2010 09:39:22 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @version $id$
 * @copyright 2010 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license AGPL
 *
 * Copyright (C) <#year#>  <#name#>
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
 * %license%
 */

class datagrid {

	private $columns    = array();
	private $count      = false;
	private $pagination = null;

	public function __construct(){ 
		$sutra = sutra::get();
		$sutra->tpl->register_function( "datagrid",			array( $this, "smartyDataGrid" ) );
		$sutra->tpl->register_function( "column",				array( $this, "smartyColumn" ) );
		$sutra->tpl->register_function( "columnvalue",	array( $this, "smartyColumnValue" ) );
		$sutra->tpl->register_function( "pagination",   array( $this, "smartyPagination" ) );
    $sutra->event->addListener( "SUTRA_TPL_FETCH", $this, "addFilters" );
	}

	public function smartyDataGrid( $args, $tpl ){
		if( _assert( isset( $args['class'] ) && isset( $args['function'] ), "{datagrid} needs attribute tags 'mod' and 'function'") ){
			$sutra    = sutra::get();
      $input    = array_merge( $_POST, $_GET );
			$class    = $args['class'];
			$class    = is_object( $sutra->mod->$class ) ? $sutra->mod->$class : $sutra->$class;
			$function = $args['function'];
			_assert( is_object($class), "datagrid 'class' ({$args['class']}) variable could not be found!");
		  if( isset($args['searchUrl']) && $args['searchUrl'] ) $sutra->tpl->assign( "searchUrl", $args['searchUrl'] );
      if( isset($input['search']) ) $sutra->tpl->assign("search", $input['search'] );
			if( $this->pagination != null ){
				$this->pagination->recordCount = call_user_method( $function, $class, false, false, true ); 
				$this->pagination->generate();
		    $data     = call_user_method( $function, $class, $this->pagination->queryOffset, $this->pagination->queryAmount );
				 $sutra->tpl->assign("pagination" ,            $this->pagination->displayArray );
				 $sutra->tpl->assign("paginationCurrent",      $this->pagination->currentPage );
				 $sutra->tpl->assign("paginationCount",        $this->pagination->pageCount );
				 $sutra->tpl->assign("paginationItemsPerPage", $this->pagination->itemsPerPage );
		  }else $data     = call_user_method( $function, $sutra->mod->$mod, false, false );
		  $sutra->tpl->assign("columns"    , $this->columns );
			$sutra->tpl->assign("data"       , $data );
			$sutra->tpl->assign("clickUrl"   , $args['clickUrl'] );
			return $sutra->tpl->fetch( isset( $args['tpl'] ) ? $args['tpl'] : "/lib/core/datagrid/tpl/container.tpl" );
		}
	}

	public function smartyColumn( $args, $tpl ){
		_assert( isset($args['width']) && isset( $args['name'] ), "please use name='yourcolumnname' in {column} tags");
		$args['width'] = sprintf( "%02d", $args['width'] );
    $args['autoclick'] = !isset( $args['tpl'] );
		$this->columns[] = $args;
	}

	public function smartyColumnValue( $args, $tpl ){
    $sutra = sutra::get();
		$ok    = _assert( isset( $args['dataProvider'] ) && isset( $args['column'] ), "{columvalue} needs attr's 'column' and 'dataProvider'");
		if( $ok ){
			$isArray 			= false;
			$dataProvider = $args['dataProvider'];
      // if 'tpl' attribute is set, fetch template
      if( isset($args['column']['tpl']) ){
        if( isset( $args['column']['vars'] ) ){
          $vars = explode( "|", $args['column']['vars'] );
          foreach( $vars as $v ) $sutra->tpl->assign( $v, $dataProvider->$v );
        }
        return $sutra->tpl->fetch( $args['column']['tpl'] );
      }
      // if '@' sign is present, get the first element from the array result
			$var          = $args['column']['var'];
			if( $var[0] == "@" && $isArray = true ){
				$vars     = explode( ".", $var );
				$var      = str_replace( "@", "", $vars[0] );
				$finalVar = $vars[1];
			}
      $result       = $dataProvider->$var;
      $result 			= !$result ? "" : ($isArray ? $result[0]->$finalVar : $result);
      // truncate output if 'truncate' attribute is set
			if( $result && isset($args['column']['truncate']) )
				$result     = strlen( $result ) > $args['column']['truncate'] ? substr( $result, 0, $args['column']['truncate'] ) . ".." : $result;
      if( !$result || strlen($result) < 1 )
        $result = isset($args['column']['default'] ) ? $args['column']['default'] : "(empty)";
      // assign the column to the template engine (so the 'tpl' attribute can benefit from this)
      $sutra->tpl->assign( $args['column']['var'], $result );
		  return $result;
		}
	}

	public function smartyPagination( $args, $tpl ){
		_assert( isset($args['itemsPerPage']) && 
						 isset( $args['url'] ),
						 "please use itemsPerPage', 'url' and 'currentPage' attributes in {pagination} tags");
		$sutra = sutra::get();
		$this->pagination = $sutra->pagination;
		$sutra->tpl->assign( "paginationUrl", $args['url'] );
		$this->pagination->itemsPerPage = (int)$args['itemsPerPage'];
		$this->pagination->currentPage  = (int)$args['currentPage'] == 0 ? 1 : (int)$args['currentPage'];
	}

  public function addFilters( $args ){
    if( strstr( $args['file'], "lib/core/datagrid/tpl/container.tpl" ) ){
      $args['filter'][] = "zoeken";
    }
  }

}


?>
