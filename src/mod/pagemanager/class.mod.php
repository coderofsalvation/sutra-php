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

class pagemanager extends abstractMod{
  
  private $cache;

  public function __construct(){
    parent::init($this);
    sutra::get()->tpl->register_function("assign_menu",     array( &$this, "assignMenu" ) );
  }

  public function getTree( $cache = true ){
    if( !isset( $this->cache['tree'] ) || !$cache ){
      $sutra                = sutra::get();
      $records              =  $sutra->db->getArray( "SELECT * FROM sutra_page ORDER BY `parent_id`, `weight`", false );
      // strip rootnode from url path (SEO friendly)
      foreach( $records as $k => $record )
        if( strstr( $record['title_url_path'], "/website-pages" ) )
          $records[ $k ]['title_url_path'] = str_replace( "/website-pages", "", $record['title_url_path'] );
      // ignore rootnodes if configured
      foreach( $records as $k => $record )
        if( in_array( $record['title_url_path'], explode(",", $this->cfg['system']['ignore_nodes'] ) ) )
          unset( $records[ $k ] );

      // convert records to associative array tree
      $this->cache['tree']  = $sutra->treeManager->getTree( $records );
    }
    return $this->cache['tree'];
  }

  /*
   * assignTree               - assigns tree lib data to smarty
   * 
   * @access public
   * @return void
   */
  public function assignTree( $cache = true ){
    $sutra          = sutra::get();
    $tree           = $this->getTree( $cache );

    // format array so the /lib/core/tree libclass likes it
    $treeFormatted  = array_reverse( $sutra->tree->prepareTreeArray( $tree, "title_menu", "title_url_path" ) );
    $sutra->event->fire( "SUTRA_PAGEMANAGER_ASSIGN_TREE", &$tree );
    $sutra->tpl->assign("data",     $treeFormatted );
    $sutra->tree;   // initialize tree libclass (enables automatic javascript inclusion)
  }

  public function processActions(){
    $sutra          = sutra::get();
    if( !isset( $_GET['action'] ) ) return;
    $action         = $_GET['action'];

    switch( $action ){
      case "down":
      case "up":      $title_url_path = $_GET['id'];
                      _assert( isset($_GET['id']), "Up/Down movement needs id webarg!");
                      $ok             = false;
                      $selectedObj    = false;
                      $tree           = $this->getTree(); 
                      $treeFlat       = $sutra->treeManager->slapTree( $tree );
                      $updates        = $sutra->treeManager->moveNode( $action, "title_url_path", $title_url_path, $treeFlat );
                      // save if we can!
                      if( $updates && ( $ok = true ) )
                        foreach( $updates as $update )
                          $sutra->db->updateArray( "sutra_page", array( 'weight' => $update['weight'] ),    $update['id'] );
                      $sutra->admin->notify( $ok ? "succes_sort" : "error_sort" );
                      break;
      case "add":     
                      _assert( isset( $_GET['title'] ) && isset( $_GET['parent_id'] ), "add needs 'title' + 'parent_id' GET-arg" );
                      $ok             = false;
                      if( $sutra->db->getArray( "SELECT * FROM `sutra_page` where `title` = '". (string)$_GET['title']  ."'" ) ){
                        _popup( $sutra->tpl->translate( "duplicate_page", "/mod/pagemanager/cfg/language_nl.yaml" ) );
                      }else{
                        $parent_id    = isset( $_GET['parent_id'] ) ? (int)$_GET['parent_id'] : $sutra->mod->pagemanager->cfg['system']['default_parent_id'];
                        $url          = $sutra->string->hyphenate( (string)$_GET['title'] );
                        $weight       = $sutra->db->getArray( "select MAX(weight) as `weight` from sutra_page", false );
                        $record       = array(  'title'           => (string)$_GET['title'], 
                                                'title_url'       => $url,
                                                'title_url_path'  => $this->generateUrlPath( $url, $parent_id, "/website-pages" ),
                                                'title_menu'      => (string)$_GET['title'],
                                                'weight'          => isset( $weight[0] ) ? $weight[0]['weight'] + 1 : 999999, 
                                                'visible'         => 1,
                                                'date'            => strftime("%G-%m-%d" ,mktime() ),
                                                'yaml'            => "content_1: Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
                                                'tpl_master'      => 'index.tpl',
                                                'parent_id'       => $parent_id  );
                        $sutra->db->insertArray( "sutra_page", $record );
												$sutra->event->fire( "SUTRA_PAGEMANAGER_ADD", array('record'=>$record) );
                        $ok = true;
                      }
                      $sutra->admin->notify( $ok ? "succes_add" : "error_add" );
                      break;
      case "delete":  
                      _assert( isset( $_GET['id'] ), "delete needs 'id' GET-arg" );
                      $ok    = false;
                      $page  = new dbObject( "sutra_page" );
                      $page->load( 'title_url_path', (string)$_GET['id'] );
                      if( isset($page->id) && $page->locked == 0 && !$sutra->db->getArray( "SELECT `id` FROM `sutra_page` where `parent_id` = '{$page->id}'" ) && ( $ok = true ) )
                          $sutra->db->query( "DELETE FROM `sutra_page` where `title_url_path` = '". (string)$_GET['id']  ."'");
                      $sutra->admin->notify( $ok ? "succes_delete" : ( $page->locked ? "error_locked" : "error_delete") );
                      break;
    }
  }

  /*
   * generateUrlPath                - generates a url path like '/myparent/andhischildren/andmyurl'
   * 
   * @param mixed $url 
   * @param mixed $parent_id 
   * @param mixed $stripfrompath   strips string from path
   * @access public
   * @return void
   */
  public function generateUrlPath( $title_url, $parent_id, $strip_from_path = false ){
    $sutra        = sutra::get();
    $records      = $sutra->db->getArray( "SELECT * FROM `sutra_page` ORDER BY `parent_id`, `weight`", false );
    $tree         = $sutra->treeManager->getTree( $records );
    $treeSlapped  = $sutra->treeManager->slapTree( $tree, 3, "title_url" );
    $path         = false;
    foreach( $treeSlapped as $node )
      if( $node['id'] == $parent_id )
        $path     = "{$node['title_url_path']}/{$title_url}";
    _assert( $path, "title_url not found in tree" );
    if( $strip_from_path ){
      $path = str_replace( $strip_from_path, "", $path );
      str_replace("///", "/", $path );
    }
    return $path;
  }

  /*
   * assignMenu            - smarty function {assign_menu} = assigns current page to template engine
   * 
   * @param mixed $params 
   * @access public
   * @return void
   */
  public function assignMenu( $params ){
    $sutra       = sutra::get();
    _assert( isset( $params['parent_id'] ), "{assign_menu} misses parent_id attribute!" );
    $parent_id   = $params['parent_id'];
    $var         = $params['var'];
    $sql         = "SELECT `id`,`title`,`title_menu`,`title_url`,`title_url_path` FROM `sutra_page` where `parent_id` = '{$parent_id}' AND `visible` != 0 ORDER BY `weight`";
    $sutra->event->fire( "SUTRA_GET_MENU_SQL", &$sql );
    $menu        = $sutra->db->getArray( $sql, false );
    $url         = $sutra->url->get();
    $sutra->event->fire( "SUTRA_GET_MENU", &$menu );

    // mark active if url is current url
    $currentPage = ( is_array($url) && count($url) ) ? array_pop($url) : $sutra->mod->webpage->cfg['general']['default_url'];
    if( !_assert( $currentPage, "no page found something is really wrong") ) return;
    foreach( $menu as $key => $item )
      $menu[ $key ]['active'] = ( $currentPage == $item['title_url'] );
    $sutra->tpl->assign( $var, $menu );
  }


}
?>
