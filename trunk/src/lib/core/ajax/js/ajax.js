/*
 * Class:     Ajax
 * Author:    Leon van Kammen
 * Date:      01-09-2009
 * Function:  This is an ajax class which makes it easy to perform unobtrusive ajax-calls with anchor tags. 
 *            It is almost similar to normal request, and it will not break your site if you don't have javascript.
 *            Just include ajax.js + fade.js (fading!), and this will make your life easier.
 *
 *            you can also just use ajax.doRequest( "http://", 'foo=value', 'GET", "yourDOMid" ) to do simple ajax requests
 *
 * Tested:    FF 2.0, IE6.0, IE7.0, Opera, Chrome
 * Example: 
 *            Normally you would do :
 *
 *              <a href="/some/page">click here</a>
 *
 *            But now you do :
 *              
 *              <img id="loading" src="loading-off.gif"/> <!-- switches between loading-on.gif & loading-off.gif -->
 *              <div id="myElement"></div>
 *              <a href="/some/page" class="ajax" rel="myElement">click here</a>
 *
 *            that's all there is to it! :]
 *
 *            also, if you also use smarty snippets, stay google friendly like this:
 *
 *            <a href="{$article.title_url_path}" class="ajax" rel="site" onmouseover="this.href += '?snippet=site'">
 *
 * Changelog  [07-07-2009] started from scratch
 *            [02-08-2009] added javascript execution of javascript found in ajax result.
 *            [01-09-2009] rewrite to class
 *            [01-03-2010] several minor fixes, assertions added
 */
if( typeof( window.assert ) != "Function" ) window.assert = function( expr, msg ){ if( !expr ) alert( msg ); }
if( typeof( window.$sutra ) != "Function" ) window.$sutra = function $sutra( id ){ return document.getElementById( id ); }

var ajax = {
  baseurl:    false,
  anchors:    false,
  sessionId:  false,
  fadetime:   250,
  scripts:    false,
  isLoading:  false,
  intervals:  new Array(),
  links:      new Array(),
  http:       new Array(),
  urls:       new Array(),
  callbacks:  new Array(),

  /*
   * init              - searches for all anchors who have : class="ajax" & rel="someid"
   *                     and ajaxifies them.
   * @access public
   * @return void
   */
  init: function( _sessionId, baseurl ){
    ajax.sessionId = _sessionId;
    if( baseurl )  ajax.baseurl = baseurl;
    if( !ajax.baseurl && !baseurl ) // mostly this is declared by the sutra framework (to enable relative paths)
      ajax.baseurl = baseurl = "http://"+document.location.hostname+document.location.pathname;
    ajax.anchors = document.getElementsByTagName("a");
    for( i = 0; i < ajax.anchors.length; i++ ){
      var skip = false;
      if( ajax.anchors[i].className.search("ajax") != -1 ){
        if( ajax.anchors[i].rel == "" ){
          assert( false, "ajax.js: anchor with href='"+ajax.anchors[i].href+"' misses REL-tag");
          continue;
        }
        // check if allready exist
        for( var j = 0; j < ajax.links.length; j++ )
          if( ajax.links[j] == ajax.anchors[i] ) skip = true;
        if( skip ) continue;
        // else add onclick event
        $addEvent( ajax.anchors[i], 'click', function(){
          var url =  new String(this.href).substring( this.href.search("#") ).replace( "#", ajax.baseurl );
          ajax.intervals.push( setTimeout( "window.ajax.doRequest( '"+url+"', '', 'GET', '"+this.rel+"' )", ajax.fadetime ) );
          return false;
        });
        var strippedUrl = (ajax.anchors[i].href.search( ajax.baseurl ) != -1 ) ? 
                                ajax.anchors[i].href.substring( ajax.baseurl.length ):
                                ajax.anchors[i].href;
        strippedUrl =  "#" + strippedUrl;
        ajax.anchors[i].href = strippedUrl;
        ajax.links.push( ajax.anchors[i] );
      }
    }
  },

  /*
   * createRequestObject - get DOM XMLHttpRequest
   */
  createRequestObject: function(){
    return (navigator.appName == 'Microsoft Internet Explorer') ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
  },

  /*
   * doRequest
   * 
   * arguments  : 
   *                url     => string
   *                args    => post or get args
   *                method  => "post" or "get"
   *                elname  => (optional) id name of element to fill up with response
   */
  doRequest: function(url, args, method, elname )
  {
    document.body.style.cursor = 'progress';
    // clear previous intervals, and load anim
    for( i = 0; i < ajax.intervals.length; i++ )
      clearInterval( ajax.intervals[i] );
    ajax.loading(true);
    method              = method ? method : "GET";
    elname              = elname ? elname : "no_element_"+(((1+Math.random())*0x10000)|0).toString(16).substring(1);
    if( elname.match("no_element_") == null ){
      changeOpacity( 100, elname );
      shiftOpacity( elname, ajax.fadetime );
    }
    url                 = ( url[0] == "/" ) ? url.substring( 1 ) : url;
    url                 = ( url.search("http://") == -1 ) ? ajax.baseurl + url : url;
    url                += url.match("\\?") ? "&SESSION_ID="+ajax.sessionId : "?SESSION_ID="+ajax.sessionId;
    ajax.urls[ elname ] = url;
    ajax.http[ elname ] = ajax.createRequestObject();
    ajax.http[ elname ].open( method, url, true);
    ajax.http[ elname ].setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.http[ elname ].onreadystatechange = function(){ 
      if( ajax.http[ elname ].readyState == 4 )
        ajax.intervals.push( setTimeout( "ajax.requestComplete( '"+elname+"','"+url+"' )", ajax.fadetime + 100 ) );
    }
    ajax.http[ elname ].send(args);
  },

  /*
   * requestComplete 
   * 
   * @param elname DOM id of element to fill up
   * @param url  url (is needed to search for result)  
   */
  requestComplete: function( elname, url ){
    // skip old requests
    if( url != ajax.urls[ elname ] ) return;
    document.body.style.cursor  = 'default';
    ajax.isLoading              = false;
    // lets be sure if we should continue
    if( !elname.match("no_element_") ){
      var el = $sutra( elname );
      assert( el, "AJAX: could not find element with id '"+elname+"'");
      if( !el ) return;
      // fill content
      ajax.executeJS( ajax.http[ elname ].responseText );
      if( el.tagName == "INPUT" )
        el.value     = ajax.http[ elname ].responseText;
      else
        el.innerHTML = ajax.http[ elname ].responseText;
      shiftOpacity( elname, ajax.fadetime ); // fade.js
    }
    for( var i in ajax.callbacks )
      if( typeof ajax.callbacks[i] == "function" )
        ajax.callbacks[i]();
    ajax.intervals.push( setTimeout( "ajax.loading()", ajax.fadetime ) );
  },

  addCallback: function ( callback ){
    ajax.callbacks.push( callback ); 
  },

  /*
   * executeJS          executes javascript embedded in ajax content
   *                    WARNING: currently the 'src' attribute must come first in an <script> tag *FIXME* 
   * @param html  $html  
   * @access public
   * @return void
   */
  executeJS: function( html ){
    // process external javascript urls
    for( var i = 0; i < 2; i++ ){
      var external    = ( i == 0 ) ? 
                        String( html.match(/<script[^"]*[^>]*>/i) ) :
                        String( html.match(/<script[^"]*>/i) ) ;
      while( true ){
        var src     = external.match(/src=['"]([^'"]*)['"]/i);
        if( src == null ) break;
        var begin   = external.search( src[1] ) + String( src[1] ).length + 1;
        // create script element    

        var e = document.createElement("script");
        e.src = src[1];
        e.type="text/javascript";
        document.getElementsByTagName("head")[0].appendChild(e); 
        // step forward in text
        external    = external.substring( begin, String(external).length-1 );
      }
    }
    // process inline javascript
    scripts = "";
    var tmp = html.replace(  /<script[^>]*[^>]*>([\s\S]*?)<\/script>/gi,  function(){ scripts += arguments[1] + '\n'; return '';  });
    setTimeout( ajax.executeJSInline, 100 );
  },

  executeJSInline: function(){
    eval( scripts );
  },

  /*
   * loading            toggleloading img
   * 
   * @param state  $state  
   * @access public
   * @return void
   */
  loading: function( state ){
    var el = $sutra("ajax");
    if( el ){
      ajax.isLoading = true;
      var src = new String( el.src );
      el.src = src.replace( "loading-"+ (!state ? "on" : "off") +".gif", "loading-"+ (state ? "on" : "off") +".gif");
      // reparse links!
      if( !state )
        ajax.init( ajax.sessionId );
    }
  }
}

