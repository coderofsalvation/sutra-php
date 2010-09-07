<?php

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
    $this->pageCount = (int) ceil( $this->recordCount /  ($this->itemsPerPage ) );
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
