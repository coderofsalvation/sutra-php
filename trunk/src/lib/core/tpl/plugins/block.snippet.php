<?
  function tpl_block_snippet($params, $content, &$tpl)
  {
    extract($params);
    if( ( isset( $_GET['snippet'] )   && $_GET['snippet'] == $name  ) || 
        ( isset( $_POST['snippet'] )  && $_POST['snippet'] == $name ) ){
      if( strlen($content ) ){
        ob_end_clean();
        _assert( isset( $name ), "snippetname not set!" );
        $sutra = sutra::get();
        $sutra->close( $sutra->tpl->correctUrls($content) );
      }
    }
    return $content;
  }
?>
