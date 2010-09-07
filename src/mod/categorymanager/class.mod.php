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

class categorymanager extends abstractMod{
  
  private $cache;

  public function __construct(){
    parent::init($this);
  }

  public function getTree( $cache = true ){
    if( !isset( $this->cache['tree'] ) || !$cache ){
      $sutra                = sutra::get();
      $records              =  $sutra->db->getArray( "SELECT * FROM sutra_category ORDER BY `parent_id`, `weight`", false );
      // convert records to associative array tree
      $this->cache['tree']  = $sutra->treeManager->getTree( $records );
    }
    return $this->cache['tree'];
  }

  /**
   * assignTree               - assigns tree lib data to smarty
   * 
   * @access public
   * @return void
   */
  public function assignTree( $cache = true ){
    $sutra          = sutra::get();
    $tree           = $this->getTree( $cache );

    // format array so the /lib/core/tree libclass likes it
    $treeFormatted  = array_reverse( $sutra->tree->prepareTreeArray( $tree, "title", "title_url_path" ) );
    $sutra->event->fire( "SUTRA_CATEGORYMANAGER_ASSIGN_TREE", &$tree );
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
                      $treeFlat       = $sutra->treeManager->slapTree( $tree, 3, "title" );
                      $updates        = $sutra->treeManager->moveNode( $action, "title_url_path", $title_url_path, $treeFlat );
                      // save if we can!
                      if( $updates && ( $ok = true ) )
                        foreach( $updates as $update )
                          $sutra->db->updateArray( "sutra_category", array( 'weight' => $update['weight'] ),    $update['id'] );
                      $sutra->admin->notify( $ok ? "succes_sort" : "error_sort" );
                      break;
      case "add":     
                      _assert( isset( $_GET['title'] ) && isset( $_GET['parent_id'] ), "add needs 'title' + 'parent_id' GET-arg" );
                      $ok             = false;
                      if( $sutra->db->getArray( "SELECT * FROM `sutra_category` where `title` = '". (string)$_GET['title']  ."'" ) ){
                        _popup( $sutra->tpl->translate( "duplicate_page", "/mod/categorymanager/cfg/language_nl.yaml" ) );
                      }else{
                        $parent_id    = isset( $_GET['parent_id'] ) ? (int)$_GET['parent_id'] : $sutra->mod->categorymanager->cfg['system']['default_parent_id'];
                        $url          = $sutra->string->hyphenate( (string)$_GET['title'] );
                        $weight       = $sutra->db->getArray( "select MAX(weight) as `weight` from sutra_category", false );
                        $record       = array(  'title'           => (string)$_GET['title'], 
                                                'title_url'       => $url,
                                                'title_url_path'  => $this->generateUrlPath( $url, $parent_id ),
                                                'weight'          => isset( $weight[0] ) ? $weight[0]['weight'] + 1 : 999999, 
                                                'parent_id'       => $parent_id  );
                        $sutra->db->insertArray( "sutra_category", $record );
                        $ok = true;
                      }
                      $sutra->admin->notify( $ok ? "succes_add" : "error_add" );
                      break;
      case "delete":  
                      _assert( isset( $_GET['id'] ), "delete needs 'id' GET-arg" );
                      $ok    = false;
                      $page  = $sutra->db->getArray( "SELECT `id` FROM `sutra_category` where `title_url_path` = '{$_GET['id']}'" );
                      if( $page && !$sutra->db->getArray( "SELECT `id` FROM `sutra_category` where `parent_id` = '{$page[0]->id}'" ) && ( $ok = true ) )
                          $sutra->db->query( "DELETE FROM `sutra_category` where `title_url_path` = '". (string)$_GET['id']  ."'");
                      $sutra->admin->notify( $ok ? "succes_delete" : "error_delete" );
                      break;
    }
  }

  /**
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
    $records      = $sutra->db->getArray( "SELECT * FROM `sutra_category` ORDER BY `parent_id`, `weight`", false );
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

  public function countMatchInTree( $column, $value, $tree ){
    $match = 0;
    foreach( $tree as $node ){
      if( isset( $node[ $column ] ) && $node[ $column ] == $value )
        $match++;
      if( isset( $node['yaml'] )  && isset( $node['yaml'][$column] ) && $node['yaml'][$column] == $value )
        $match++;
      if( isset( $node['children'] ) && count( $node['children'] ) ) 
        $match += $this->countMatchInTree( $column, $value, $node['children'] );
    }   
    return $match;
  }

}
?>
