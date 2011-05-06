<?
/**
 * login                        - listens to url '/admin' or '/login' to login (triggers popup)
 *                                if login is correct (matches password in db table 'sutra_user')
 *                                session variable $_SESSION['user_id'] is set.
 *                                POST/GET-var 'logout' = resets this session var
 *                                POST/GET-var 'username' & 'password' can be passed anytime
 * @uses urlListener
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author Tobias Schlitt <toby@php.net> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class login{

  public function __construct(){
    sutra::get()->event->addListener( "SUTRA_URL", $this, "executeUrl" );
  }

  public function executeUrl( $url ){
    $sutra  = sutra::get();
    $input = array_merge( $_POST, $_GET );
    if( isset( $input['username'] ) || isset( $input['password'] ) )
      $this->loginUser( $input['username'], $input['password'] );
    if( array_key_exists( "logout", $input ) ){
      _popup("logout!");
      $sutra->event->fire( "SUTRA_USER_LOGOFF" );
      unset( $_SESSION['user_id'] );
    }
    if ( is_array($url) && ( in_array( "admin", $url ) || in_array( "login", $url )) && !isset( $_SESSION['user_id'] ) )
      $this->popup();
    if( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] ){
      $sutra->user->load( $_SESSION['user_id'] );
    }
  }

  private function loginUser( $username, $password = false ){
    $sutra  = sutra::get();
    $user   = new dbObject( "sutra_user" );
    $user->load( "username", $username );
    if( $password && isset($user->password) && $user->password == md5( $password ) && is_object($sutra->user) ){
      $user->copyTo( &$sutra->user );
      $_SESSION['user_id'] = $user->id;
      $sutra->session->close();
      // disabled because of incorrect tooltip display, and maybe more
      // $sutra->ajax->getUrl( "http://{$sutra->_url}?snippet=panel", "panel" );
      // $sutra->ajax->getUrl( "http://{$sutra->_url}?snippet=popup", "popup" );
      // $sutra->close( $sutra->tpl->translate( "user_logged_in" ) );
      $_SESSION['title_url_path'] = str_replace( array("/admin","/login"), "", $_SESSION['title_url_path'] );
      $url = "http://{$sutra->_url}" . substr( $_SESSION['title_url_path'], 1);
      $sutra->close( "<script type='text/javascript'>document.location.href = '{$url}';</script>");
    }else{
      unset( $_SESSION['user_id'] );
      $sutra->tpl->assign("small", true );
      $sutra->close( $sutra->tpl->fetch( "/lib/cms/login/tpl/login.tpl" ) );
    }
    $sutra->event->fire( "SUTRA_USER_LOGIN" );
  }

  public function popup(){
    $sutra    = sutra::get();
    $sutra->tpl->assign("small", true );
    if( strstr( $sutra->browser->get(), "Internet Explorer" ) )
      $content = "<br><br>The admin functionaliteiten zijn alleen getest op Mozilla Firefox<br><br>Download het <a href='http://www.mozilla.com/'>hier</a>, en probeer opnieuw.";
    else 
      $content  = $sutra->tpl->fetch( "/lib/cms/login/tpl/login.tpl" );
    $sutra->popup->show( $content, false, true );
  }
}
?>
