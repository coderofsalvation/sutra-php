<?
/** 
 * File:        class.mod.php
 * Date:        16-08-2009
 *
 * represents a class module
 * 
 * Changelog:
 *
 * 	[Sat Aug 15 22:54:27 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   $sutra->mod->page
 * </code>
 *
 * @package $utra Framework 
 */

class webpage extends abstractMod{

  public function __construct(){
    parent::init($this);
    sutra::get()->tpl->register_function("assign_page",     array( &$this, "assignCurrentPage" ) );
  }

  /**
   * getTree            - get tree of all content pages
   * 
   * @access public
   * @return void
   */
  public function getTree(){
    $sutra          = sutra::get();
    $records        = $sutra->db->getArray( "SELECT * FROM sutra_page", false );
    // convert records to associative array tree
    $tree           = $sutra->treeManager->getTree( $records );
    return $tree;
  }

  /**
   * populatePage               - this can merge 2 page objects (for example: the current $sutra->page object & a page array from the db.
   *                              It also makes sure that default values are present in case of missing values.
   * @param mixed $page_src 
   * @param mixed $page_dest 
   * @access public
   * @return void
   */
  public function populatePage( $page_src, $page_dest ){
    _assert( ( is_object($page_dest) || is_array( $page_dest) ) && ( is_object( $page_src ) || is_array( $page_src ) ), "populatePage: 2 args should be arrays or objects" );
    $sutra  = sutra::get();
    $page   = array_merge( (array)$page_src, (array)$page_dest );
    if( strlen( $page['tpl_master'] ) == 0 )
      $page['tpl_master']         = $this->cfg['system']['page_tpl_default']; 
    if( strlen( $page['meta_description'] ) == 0 )
      $page['meta_description']   = $this->cfg['general']['default_meta_description'];
    if( strlen( $page['meta_keywords'] ) == 0 )
      $page['meta_keywords']      = $this->cfg['general']['default_meta_keywords'];
    return (object)$page;
  }

  /**
   * assignCurrent Page          = smarty function {assign_page} = assigns current page to template engine
   * 
   * @param mixed $params 
   * @access public
   * @return void
   */
  public function assignCurrentPage( $params ){
    $sutra       = sutra::get();
    $page        = $sutra->db->getObject( "SELECT * FROM `sutra_page` where `title_url_path` = '{$_SESSION['title_url_path']}'" );
    $page        = $this->ensurePage( $page );
    $sutra->tpl->assign( "page",    (array)$page );
  }

  /**
   * ensurePage       - makes sure we always have a page, if not then this function returns the default page 
   * 
   * @param mixed $page 
   * @access public
   * @return void
   */
  public function ensurePage( $page ){
    $sutra          = sutra::get();
    //_assert( $page || !$sutra->url->get() , "page is not valid..maybe url in session is invalid, rolling back to default url");
    if( !$page ){
      //_popup( $sutra->tpl->translate( "page" ) . " " .$sutra->tpl->translate( "not_found") );
      $title_url_path = $_SESSION['title_url_path'] = $sutra->mod->webpage->cfg['general']['default_url'];
      $page           = $sutra->db->getObject( "SELECT * FROM `sutra_page` where `title_url_path` = '{$title_url_path}'" );
    }
    return $page;
  }

  /**
   * prepareTreeArray         - massage array in a way so it'll work for the tree javascript lib ( /lib/core/tree )
   * 
   * @param array$tree 
   * @access public
   * @return array
   */
  public function prepareTreeArray( $tree ){
    if( !is_array( $tree ) ) return $tree;
    foreach( $tree as $k => $value ){
      if( is_array( $value['children'] ) && count( $value['children'] ) ){
        $children   = $this->prepareTreeArray( $value['children'] ); 
        $tree[ $k ] = array( $value[ 'title_menu' ], $value[ 'title_url_path'] );
        foreach( $children as $child )
          $tree[ $k ][] = $child;
      }else{
        $tree[ $k ] = array( $value[ 'title_menu' ], $value[ 'title_url_path' ] );
      }
    }
    sort($tree);
    return $tree;
  }

  /**
   * savePage 
   * 
   * @param mixed $input $_POST or $_GET array with values (or just a plain sql array)
   * @param mixed $title_url_path if given, this page will be updated
   * @param mixed $page_id  if given, this page will be updated
   * @access public
   * @return void
   */
  function savePage( $input, $title_url_path = false, $page_id = false ){
    $sutra           = sutra::get();
    $inputYaml       = array();
    $use_session     = ( $title_url_path == false );
    $title_url_path  = $use_session ? $_SESSION['title_url_path'] : $title_url_path;
    $sql_url         = "SELECT * FROM `sutra_page` where `title_url_path` = '{$title_url_path}'";
    $sql_id          = "SELECT * FROM `sutra_page` where `id`             = '{$page_id}'";
    // load
    if( $use_session && !_assert( strlen( $_SESSION['title_url_path']), "no page url found in session, cannot save because of that!") )
      return;
    $page            = $sutra->db->getObject( !$page_id ? $sql_url : $sql_id );
    // separate yaml vars from db-columns, unknown columns become yaml-colums
    foreach( $input as $column => $value )
      if( !key_exists( $column, $page ) && ( $inputYaml[ $column ] = $value ) )
          unset( $input[ $column ] );
    // make sure we have no empty required values
    $page        = $sutra->mod->webpage->populatePage( $page, $input );
    $page->date  = isset($input['date']['Year']) ? $input['date']['Year']."-".$input['date']['Month']."-".$input['date']['Day'] : $page->date;
    $page->yaml  = array_merge( $page->yaml, $inputYaml );
    unset( $page->yaml['action'] );

    // save
    $page->title_url_path = $sutra->mod->pagemanager->generateUrlPath( $page->title_url, $page->parent_id, "/website-pages" );
    $sutra->event->fire( "SUTRA_PAGE_SAVE_PRE", &$page );
    $sutra->db->saveObject( "sutra_page", $page );
    $sutra->event->fire( "SUTRA_PAGE_SAVE_POST", &$page );
    $sutra->tpl->assign( "save_succes", "{$sutra->tpl->translate( "page" )} {$sutra->tpl->translate( "saved_succesfull" )}" );
    return $page;
  }
}
?>
