<?php
/** 
 * File:        class.pagination.php
 * Date:        Mon Sep 19 16:49:34 2011
 *
 * utility class to generate index numbers for pages
 * 
 * Changelog:
 *
 * 	[Mon Sep 19 16:49:34 2011] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @author Johan Adriaans <www.izi-services.nl>
 * @author Leon van Kammen (sutra specific modifications)
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

class pagination
{
  public $recordCount;
  public $pageCount;
  public $currentPage;
  public $itemsPerPage;
  public $queryAmount;
  public $queryOffset;
  public $displayArray = array();

  public function __construct( $recordCount = 0, $items_per_page = 4, $current_page = false )
  {
    $this->recordCount = (int) $recordCount;
    $this->itemsPerPage = (int) $items_per_page;
    $this->currentPage = (int) max( min( $this->pageCount, $current_page ), 1 );
    $this->generate();
  }

  public function generate()
  {
    _assert( is_numeric( $this->itemsPerPage ), "items per page should be a number ");
    _assert( is_numeric( $this->recordCount ), "recordCount should be a number ");
    $leftover          = 0;
    $this->pageCount   = (int) ceil( $this->recordCount /  ($this->itemsPerPage ) );
    $this->queryAmount = $this->itemsPerPage;
    $this->queryOffset = $this->itemsPerPage * ( $this->currentPage - 1 );
    $start = $this->currentPage - 4;
    if ( $start < 1 ) { $leftover = abs( $start ); $start = 1; }
    $end = $this->currentPage + 4 + $leftover;
    if ( $end > $this->pageCount ) {
      $start -= $end - $this->pageCount;
      $end = $this->pageCount;
    }
    $start = $start <= 1 ? 1 : $start;
    for ( $i = $start ; $i <= $end ; $i++ )
      array_push( $this->displayArray, $i );
  }
}

?>
