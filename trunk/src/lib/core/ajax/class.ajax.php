<?
/** 
 * File:        class.ajax.php
 * Date:        <#date#>
 *
 * Makes ajax request easy (client and browser side)
 * It enhances the scriptengine with automatic includes of javascript
 * 
 * Changelog:
 *
 * 	[Thu Jun 11 21:54:37 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   There are several ways to handle requests, post forms.
 *   Here are the different types of communication showed (as used in the cms-package) :
 *   
 *   BASIC AJAX REQUEST
 *   ==================
 *   
 *   +-----------------------------+     +--------------------------------------------------------+
 *   | [browser]                   |---->| [javascript]                                           |
 *   |   <div id="response"></div> |     |   ajax.doRequest( "http://yoursite.com", "response" ); |
 *   +-----------------------------+     +--------------------------------------------------------+
 *                 /'\                                              |
 *                  |                                               |
 *                  `------------put html into DOM id -----------(SERVER)
 *   
 *   
 *   
 *   SNIPPET AJAX REQUEST
 *   ====================
 *   
 *   Suppose the url 'http://yoursite.com/somepage' would fetch the following template :
 *   
 *                                                         +-----------------------------------------------+
 *                                                         | <html>                                        |
 *      PHP: $sutra->tpl->fetch("sometemplate.tpl")   -->  |   <div id="response">                         | 
 *                                                         |     {snippet name="response"}Hello!{/snippet} |
 *                                                         |   </div>                                      |
 *                                                         | </html>                                       |
 *                                                         +-----------------------------------------------+
 *   
 *   Then, it could update itself thru ajax, without refreshing the page or doing additional coding :
 *   
 *   +-----------------------------+     +-----------------------------------------------------------------------------+
 *   | [browser]                   |---->| [javascript]                                                                |
 *   |   <div id="response"></div> |     |   ajax.doRequest( "http://yoursite.com/foo?snippet=response", "response" ); |
 *   +-----------------------------+     +-----------------------------------------------------------------------------+
 *                 /'\                                              |
 *                  |                                               |
 *                  `------------put "Hello!" into DOM id -------(SERVER)
 *   
 *   
 *   RECURSIVE AJAX REQUEST
 *   ======================
 *   
 *   +-----------------------------+     +--------------------------------------------------------+
 *   | [browser]                   |---->| [javascript]                                           |
 *   |   <div id="popup"></div>    |     |   ajax.doRequest( "http://yoursite.com", "response" ); |
 *   |   <div id="site"></div>     |     |                                                        |
 *   +-----------------------------+     +--------------------------------------------------------+
 *        /'\      /'\                                              |
 *         |        |                                               |
 *         |        |                                   PHP: echo "Hi!!"
 *         |        |                                   PHP: $sutra->ajax->getUrl("http://www.google.com", "site" );
 *         |        |                                           \ OUTPUTS: <script> ajax.doRequest( ... ) </script>
 *         |        |                                               |
 *         |        |                                               |
 *         |        `-------1) put "Hi!!" into DOM id 'popup'---- (SERVER)
 *         |                2) do google ajax request
 *         |                             |
 *         |                             `----------------------> (SERVER)
 *         |                                                        |
 *         |                                                        |
 *          `------------------- put google into DOM id 'site' -----        
 *   
 * </code>
 *
 * @package Sutra framework 
 */

class ajax{

  // session id, to let the asynchronous calls share the same session
  public  $sessionId;

  // state variable which resembles if lib is enabled
  public  $active;

  // shows whether a section section is requested
  private $section;

  public function __construct(){ 
    $this->active     = false;
    $this->section    = false;
    sutra::get()->tpl->register_block( "ajaxform", array( &$this, "tpl_block_ajaxform" ) );
    sutra::get()->event->addListener( "SUTRA_URL", $this, "executeUrl" );
  }

  /**
   * executeUrl       - (interface function @ lib/cms/iface.urlListener.hp )
   *                    this makes this lib part of the url routing.
   *                    this function is called by sutra before any output started.
   * 
   * @param array $url 
   * @access public
   * @return void
   */
  public function executeUrl( $url ){
    //if( empty( $_GET['ajaxsection'] ) && empty( $_POST['ajaxsection'] ) ) return;
    //sutra::get()->mod->fireEvent( "SUTRA_EXECUTE_AJAX" );
    //$sutra            = sutra::get();
    // enable ajax lib (smarty tags) if url starts with 'ajax'
    //if( isset( $sutra->url->getAction(true)->action ) &&  $sutra->url->getAction(true)->action == "ajax" )
      //$this->enable();
  }

  /**
   * isolate          - does nothing if sutra's url-lib doesnt sense an ajax url call like :
   *                      http://yoursite.com/ajax/mypage
   *                      http://yoursite.com/ajax/mypage/mysection
   *                    However, if so, it will break out of the smarty mechanism and just print out the ajax content.
   *                    NOTE: be sure to ALWAYS do all operations BEFORE printing..period.
   * @param mixed $params 
   * @param mixed $content 
   * @param mixed $smarty 
   * @param mixed $repeat 
   * @access public
   * @return void
   */
  public function isolate( $params, $content, &$tpl ){
    //if( $this->active ){
      //if( !array_key_exists( "section", $params ) || ( $this->section !== false && $params['section'] == $this->section ) ){
        //sutra::get()->mod->fireEvent( "SUTRA_EXECUTE_AJAX" );
   //     ob_end_clean();     // break out of template's head.tpl ob_start 
   //     ob_end_clean();     // break out of template's index.tpl ob_start 
        //sutra::get()->close( $content );
      //}
    //}
    //return $content;
  }

  public function enable( $state = true ){
    $this->active = $state;
  }

  /**
   * getUrl                    - this outputs javascript which causes ajax refreshes
   * 
   * @param string $DOMid  id of the container where the content will be put into (for ex. 'myId' when using <div id='myId'></div>)
   * @param string $url url to use
   * @access public
   * @return void
   */
  public function getUrl( $url, $DOMid ){
    $url = ( !strstr($url,"http://") && $url[0] != "/" ) ? "/{$url}" : $url;
    print "<script type='text/javascript'> var ajax = ( parent != undefined ) ? parent.ajax : window.ajax; ajax.doRequest( '{$url}', false, 'GET', '{$DOMid}' ); </script>";
  }

  /**
   * template_lite {ajaxform}{/ajaxform} block plugin
   *
   * Type:     block function
   * Name:     ajax
   * Purpose:  wraps an ajax form around the content, so page does not need refreshing
   */
  //function tpl_block_ajaxform($params, $content, &$tpl)
  //{
    //if( strlen($content ) ){
      //if( !isset( $params['action'] ) )       $params['action']     = "";
      //if( !isset( $params['method'] ) )       $params['method']     = "POST";
      //if( !isset( $params['id'] )     )       $params['id']         = time();
      //if( !isset( $params['responseId'] ) )   $params['responseId'] = "popupContent";
      //if( !isset( $params['onload'] ) )       $params['onload']     = "";
      //$iframeId = time();
      //$html     = "<iframe style='height:0px;width:0px;border:none' src='about:blank' id='{$iframeId}' name='{$iframeId}' onload='setTimeout( window.ajax.copyIframe, 500, this.id, \"{$params['responseId']}\" )'></iframe>\n";
      //$html    .= "<form target='{$iframeId}' action='{$params['action']}' method='{$params['method']}' enctype='multipart/form-data' id='{$params['id']}' onsubmit='return false;'>\n";
      //$html    .= $content;
      //$html    .= "</form>";
      //return $html;
    //}
  //}
}

?>
