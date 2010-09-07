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

class filemanager extends abstractMod{

  private $cache;

  public function __construct(){
    parent::init($this);
    $cache = array();
  }

  /**
   * getTree                 - gets a tree (associative array of files) from a directory
   * 
   * @param bool $onlydirs  pass true if you only want dirnames
   * @access public
   * @return void
   */
  public function getTree( $onlydirs = false){
    $cacheKey                     = $onlydirs ? "tree" : "treeDirs";
    if( !isset( $this->cache[ $cacheKey ] )){
      $sutra                      = sutra::get();
      $dir                        = "{$sutra->_path}/{$this->cfg['system']['upload_path']}";
      $files                      = $sutra->files->getTree( $dir, true, array(".swp",".svn"), true, false, $onlydirs );
      $tree                       = $sutra->treeManager->getTree( $files );
      $this->cache[ $cacheKey ]   = array( array( "id" => 0, "parent_id" => -1, "name_short" => $this->cfg['system']['root_name'], "weight" => 0, "children" => $tree, "path" => "" ) );
    }
    return $this->cache[ $cacheKey ];
  }

  /**
   * assignTree               - assigns tree lib data to smarty
   * 
   * @access public
   * @return void
   */
  public function assignTree(){
    $sutra          = sutra::get();
    $tree           = $this->getTree();
    // format array so the /lib/core/tree libclass likes it
    $treeFormatted  = array_reverse( $sutra->tree->prepareTreeArray( $tree, "name_short", "path" ) );
    $sutra->tpl->assign("data",     $treeFormatted );
    $sutra->tpl->assign("rootUrl",  "http://{$sutra->_url}{$sutra->mod->filemanager->cfg['system']['upload_path']}" );
    $sutra->tree;   // initialize tree libclass (enables automatic javascript inclusion)
  }

  public function processActions(){
    if( !isset( $_GET['action'] ) ) return;
    $sutra          = sutra::get();
    $action         = $_GET['action'];
    switch( $action ){
      case "delete":    if( $sutra->upload->delete( array($_GET['id']), $sutra->mod->filemanager->cfg['system']['upload_path'] ) )
                          $sutra->admin->notify( "succes_delete" );
                        else
                          $sutra->admin->notify( "error_delete" );
                        break;
    }

  }

}
?>
