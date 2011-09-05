<?
/** 
 * File:        class.acl.php
 * Date:        Mon Mar 22 20:09:01 2010
 *
 *  ACL (Acces Control List) is basically a mechanism of limiting functionality per role/group.
 *  With this ACL structure below you can blacklist OR whitelist stuff, OR do both 
 *
 *  this is a yaml or array (see init() ) :
 *
 *      groups: 
 *        root:
 *          copy: +all                    # 'all' is a special keyword, it means acl permissions are allowed
 *          permissions:
 *        admin:
 *          copy: +root                   # inherit all permissions from root
 *          permissions: -roleB, -roleA   # exlude roleA & roleB
 *        groupA: 
 *          copy: +all,-admin             # inherit all, but drop all admin's permissions
 *          permissions: 
 *        groupB:                         # a user of this group only has permission for roleA & roleB
 *          copy:
 *          permissions: roleA, roleB 
 *
 *  <example>
 *     $sutra         = sutra::get();
 *     // this is a basic installation
 *     $dummyGroup    = array( "copy" => false, "permissions" => false );
 *     $dummyGroups   = array( "root" => $dummyGroup, "admin" => $dummyGroup, "member" => $dummyGroup, "user" => $dummyGroup );
 *     $sutra->acl->init( $dummyGroups );
 *      if( ! $sutra->acl->isAllowed( "roleA" ) $sutra->close( "Sorry not allowed for this user!" );
 * </example>
 */
class acl{

  public  $permissions;
  public  $groups;

  public function __construct(){
    $sutra              = sutra::get();
    $this->groups       = array();
    $this->permissions  = array();
    $this->init( $sutra->yaml->cfg['acl']['groups'] );
    if( isset( $sutra->tpl ) ) 
      $sutra->tpl->register_block( "isAllowed", array( &$this, "tpl_isAllowed" ) );
    if( isset( $sutra->cli ) ) $sutra->event->addListener( "SUTRA_ACL_REQUEST", &$sutra->cli, "onACL" );
  }

  private function buildPermissions( $groups ){
    $sutra        = sutra::get();
    $permissions  = array( "enabled" => array(), "disabled" => array() );
    // collect all permission keywords
    foreach( $groups as $groupname => $group ){
      if( strlen( $group['permissions'] ) ){
        $permissions = $sutra->string->parseToggleString( $group['permissions'], "," );
        $this->permissions = array_merge( $this->permissions, $permissions['enabled'] );
        $this->permissions = array_merge( $this->permissions, $permissions['disabled'] );
      }else $permissions = array('enabled' => array(),'disabled' => array());
      $this->groups[ $groupname ]['_permissions']  = $permissions;
    }
    $this->permissions = array_unique( $this->permissions );
  }

  private function buildGroups( $groups, $permissions ){
    $sutra        = sutra::get();
    // assign permissions according to flags
    foreach( $groups as $groupname => $group ){
      if( strlen( $group['copy'] ) ){
        $copies  = $sutra->string->parseToggleString( $group['copy'], "," );
        $state   = array( "enabled", "disabled" );
        // take a coffee, this one will boggle your mind
        // for every permission of a copied group, add permission if it does not conflict with permit-list of current group
        for( $i = 0; $i < 2; $i++ ){
          foreach( $copies[ $state[0] ]  as $copy ){
            if( isset( $groups[ $copy ] ) ){
              foreach( $groups[ $copy ]['_permissions'][ $state[0] ] as $permission )
                if( !in_array( $permission, $groups[ $groupname ]['_permissions'][ $state[1] ] ) )
                  $groups[ $groupname ]['_permissions'][ $state[0] ][] = $permission;
              $groups[ $groupname ]['_permissions'][ $state[0] ] = array_unique( $groups[ $groupname ]['_permissions'][ $state[0] ] );
            }else if( $copy == "all" ) $groups[ $groupname ]['_permissions'][ $state[0] ] = $permissions;
          }
          $state = array_reverse( $state );
        }
      } 
    }
    $this->groups = $groups;
  }

  public function init( $groups = false){
    $sutra        = sutra::get();
    if( !$groups ){
      $dummyGroup   = array( "copy" => false, "permissions" => false );
      $groups       = array( "root" => $dummyGroup, "admin" => $dummyGroup, "member" => $dummyGroup, "user" => $dummyGroup );
    }
    $this->groups = $groups;
    $this->buildPermissions( $this->groups );
    $this->buildGroups( $this->groups, $this->permissions );
  }

  /**
   * areAllowed  - check if multiple permissions exist on current session
   * 
   * @param mixed $permissionsCommaSeparated - commaseperated string with permissions
   * @param mixed $redirectHTML - html which is printed if any permission fails (calls $sutra->close())
   * @access public
   * @return void
   */
  public function areAllowed( $permissions, $redirectHTML = false ){
    $allowed        = true;
    $permissionsArr = explode(",", $permissions );
    foreach( $permissionsArr as $permission )
      $allowed &= $this->isAllowed( $permission );
    if( $redirectHTML && !$allowed )
      sutra::get()->close( $redirectHTML );
    return $allowed;
  }

  public function isAllowed( $permission ){
    $sutra = sutra::get();
    if( !_assert( is_object( $sutra->user ), "could not retrieve user, strange things happening!" ) ) return false;
    $group = $sutra->user->group;
    _assert( array_key_exists( $group, $this->groups ), "unknown group '{$group}' :( please add/correct this group in '/data/cfg.yaml.php'" );  
    _assert( in_array( $permission, $this->permissions ), "unknown permission '{$permission}' for group '{$group}' :( please add/copy/correct this permission in '/data/cfg.yaml.php'" );
    $ok = in_array( $permission, $this->groups[ $group ]['_permissions']['enabled'] );
    if( isset( $sutra->cli ) )
      $sutra->event->fire( "SUTRA_ACL_REQUEST", array( "permission" => $permission, "ok" => $ok ) );
    return $ok;
  }

  public function tpl_isAllowed( $params, $content, &$tpl ){ 
    $sutra = sutra::get();
    if( strlen($content) ){
      if( isset( $sutra->cli ) )
        $sutra->event->fire( "SUTRA_ACL_REQUEST", array( "permission" => $params['permission'], "src" => $tpl->_file ) );
      if( isset( $params['permission'] ) && $this->isAllowed( $params['permission'] ) ) return $content;
      if( isset( $params['group'] ) ) return in_array( $sutra->user->group, explode(",", $params['group'] ) ) ? $content : "";
    }
  }

}

?>
