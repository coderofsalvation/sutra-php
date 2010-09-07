<?
/** 
 * File:        abstract.mod.php
 * Date:        16-08-2009
 *
 * represents a abstract module
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
class abstractMod{
  private $_child;
  private $_className;
  private $_widgets;
  public function __construct(){}

  public function init( $child = false ){
    if( $child ){
      $this->_child     = &$child;
      $this->_className = $className = get_class($child);
      // init cfg & std. ass-sign class to template :)
      $this->cfg;
      sutra::get()->tpl->assign( $this->_className, (array) $child );
    }
  }

  public function __get( $var ){
    switch( $var ){
      case "cfg":
        $modules = sutra::get()->mod->getModules();
        $this->_child->cfg = $cfg = $modules[ $this->_className ];
        return $cfg;
        break;

      case "widgets": 
        // get from cache
        if( $this->_widgets ) return $this->_widgets;
        // or create cache 
        $className   = $this->_className . "Widgets";
        $widgetsFile = sutra::get()->_path . "/mod/{$this->_className}/class.widgets.php";
        _assert( is_file( $widgetsFile ), "widgets file {$widgetsFile} not found! does your module's classname match the directory name?");
        require_once( $widgetsFile );
        return ( $this->_widgets = new $className );
      default: break;
    }
  }
}
?>
