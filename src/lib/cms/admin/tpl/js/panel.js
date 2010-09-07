var admin = {

  strings:      Array(),
  cache:        Array(),
  dev:          false,
  edit:         false,
  editor:       false,
  resetStatus:  false,
  ro:           (navigator.appName == 'Microsoft Internet Explorer') ? new ActiveXObject('Microsoft.XMLHTTP') : ro = new XMLHttpRequest(),

  toggleMouseListeners: function( state ){
    document.onmouseover = state ? admin.inspectMouseOver : null; 
    document.onmouseout  = state ? admin.inspectMouseOut  : null;
    document.onmousedown = state ? admin.inspectMouseDown : null;
    window.onscroll      = admin.autoScroll;
  },

  init: function(){
    admin.cache[ 'border' ] = Array();
  },

  autoScroll: function(){
    tween( $sutra('panel'), 'top', 'current', (admin.cross_scrollTop()+15)+"px", 150);
    if( $sutra('popup') != undefined )
      tween( $sutra('popup'), 'top', 'current', (admin.cross_scrollTop()+55)+"px", 150);
  },

  getWidgetFromEvent: function(e){
    // IE is retarded and doesn't pass the event object 
    if (e == null) e      = window.event;
    // IE uses srcElement, others use inspectTarget.
    var target            = e.target != null ? e.target : e.srcElement;
    var id                = String( target.id );
    return id.match(/widget_/) ? target : false;
  },

  toggleEditableContent: function( state ){
    if( !admin.editor ) return;
    var divs          = document.getElementsByTagName("div");
    for( var i = 0; i < divs.length; i++ ){
      var className = String( divs[i].className );
      if( className.match(/editable/) ){
        if( state )
          admin.editor.addInstance( divs[i] );
        else
          admin.editor.removeInstance( divs[i] );
      }
    }
  },

  inspectMouseOver: function(e){
    var target            = admin.getWidgetFromEvent(e);
    if( target ){
      admin.cache[ 'border' ][ String( target.id ) ] = target.style.border;
      target.style.border   = "1px dotted blue";
    }
  },

  inspectMouseOut: function(e){
    var target            = admin.getWidgetFromEvent(e);
    if( target ){
      target.style.border   = admin.cache[ 'border' ][ String( target.id ) ];
    }
  },

  inspectMouseDown: function(e){
    var target            = admin.getWidgetFromEvent(e);
    if( target ){
      if( admin.edit ) admin.toggleEdit();
      admin.setStatus( admin.strings[ 'STATUS_WIDGET_IS_LOADING' ] );
      admin.show( "popupPage", true, true );
    }
  },

  show: function( elname, show, erase ){
    var el = $sutra( elname );
    if( !el ) return;
    el.style.display = show ? "block" : "none";
    if( erase ) el.innerHTML = "";
  },

  saveContent: function( content, id, instance ){
    var el = $sutra( id );
    var page_id = false;
    var params = el.className.split( " " );
    for( i in params )
      if( (params[i] == parseInt( params[i] )) && (page_id = params[i]) )
        break;
		content = admin.cleanMSWord( content, true );
    page_id = page_id ? "&page_id="+page_id : "";
    ajax.doRequest( 'admin', 'event=SUTRA_ADMIN_SAVE_CONTENT&'+id+'='+escape(content)+page_id, "POST" );
    alert('tekst opgeslagen');
    admin.toggleEdit();
  },

  cleanMSWord: function( specialchartext, toHtml ){
		specialchartext = escape(specialchartext);
		if( toHtml ){
			specialchartext = specialchartext.replace(/%u201C/g, "&ldquo;");
			specialchartext = specialchartext.replace(/%u201D/g, "&rdquo;");
			specialchartext = specialchartext.replace(/%u2018/g, "&lsquo;");
			specialchartext = specialchartext.replace(/%u2019/g, "&rsquo;");
			specialchartext = specialchartext.replace(/%u2026/g, "&hellip;");
		} else {
			specialchartext = specialchartext.replace(/%u201C/g, "\"");
			specialchartext = specialchartext.replace(/%u201D/g, "\"");
			specialchartext = specialchartext.replace(/%u2018/g, "'");
			specialchartext = specialchartext.replace(/%u2019/g, "'");
			specialchartext = specialchartext.replace(/%u2026/g, "...");
		}
		specialchartext = specialchartext.replace(/%u2013/g, "&ndash;");
		specialchartext = specialchartext.replace(/%u2013/g, "&ndash;");
		specialchartext = specialchartext.replace(/%u2014/g, "&mdash;");
		specialchartext = specialchartext.replace(/%A9/g, "&copy;");
		specialchartext = specialchartext.replace(/%AE/g, "&reg;");
		specialchartext = specialchartext.replace(/%u2122/g, "&trade;");
    specialchartext = specialchartext.replace(/%uF04A/g, ":)" );
		specialchartext = unescape(specialchartext);
		return specialchartext;
	},

  closePopup: function(){
    admin.show( 'popupContent', true, true  ); 
    admin.show( 'popupPage',    false, false ); 
  },

  setStatus: function( message ){
    if( admin.resetStatus ) clearTimeout( admin.resetStatus );
    var el            = $sutra("panelStatus");
    if( el ){
      el.innerHTML      = "<p id='statusMessage'>"+message+"</p>";
      admin.resetStatus = setTimeout( "admin.setStatus('')", 2600 );
    }
  },

  clickModule: function( name ){
    if( admin.edit )
      admin.toggleEdit();
    admin.show( 'popupPage', true, false);
    admin.show( 'popupClose', false, false );
    admin.setStatus( name );
  },

  toggleDev: function(){
    admin.dev = !admin.dev;
    admin.toggleImage('iconDev', 'icon.dev' );
    if( admin.dev )
      alert( admin.strings[ 'ALERT_DEV' ]);
    window.ajax.doRequest( "/togglecache" );
  },

  toggleEdit: function(){
    admin.edit = !admin.edit;
    admin.toggleMouseListeners( admin.edit );
    admin.toggleImage( 'iconEdit', 'icon.edit' );
    admin.show( 'popupPage', false, false); 
    admin.show( 'popupClose', false, false );
    admin.show( 'panelStatus', !admin.edit, true );
     // create editor if needed
    if( admin.edit && !admin.editor ){
      admin.editor = new nicEditor( {fullPanel : true, onSave: admin.saveContent} );
      admin.editor.setPanel('nicedit');
    }
    // toggle stuff
    admin.show( 'nicedit', admin.edit, false );
    admin.toggleEditableContent( admin.edit );
  },

  /* toggleImage        - switches between 'yourimage-on.gif' and 'yourimage-off.gif' 
   *
   * @param elname (string) id of the img
   * @param prefix (string) string to put before the '-on.gif' and '-off.gif'
   */
  toggleImage: function( elname, prefix ){
    var el = $sutra( elname );
    if( el ){
      var src   = new String( el.src );
      var state = !src.match(/-on/);
      el.src = src.replace( prefix+"-"+ (!state ? "on" : "off") +".gif", prefix + "-"+ (state ? "on" : "off") +".gif");
    }
  },

  /* toggleNames       - switches between 2 views according to name attribute (shows/hides certain input elements so we don not scare user)
   *
   */
  toggleNames: function( name, state, fade ){
    var els = document.getElementsByName( name);
    for( var i = 0; i < els.length; i++ ){
      if( !els[i].id )
        els[i].id = ((new Date()).getTime() + "" + Math.floor(Math.random() * 1000000)).substr(0, 18);
      if( fade )
        opacity( els[i].id, state ? 0 : 100, state ? 100 : 0, 400 );
      else els[i].style.display = state ? ( els[i].tagName == "DIV" ? "block" : "inline" ) : "none";
    }
  },

  close: function(){
    ajax.doRequest( window.baseurl + "?logout" );
    $sutra( "panel" ).innerHTML = "";
    $sutra( "popup" ).innerHTML = "";
  },

  cross_clientHeight: function() {
    return admin.cross_filterResults (
      window.innerHeight ? window.innerHeight : 0,
      document.documentElement ? document.documentElement.clientHeight : 0,
      document.body ? document.body.clientHeight : 0
    );
  },

  cross_scrollTop: function() {
    return admin.cross_filterResults (
      window.pageYOffset ? window.pageYOffset : 0,
      document.documentElement ? document.documentElement.scrollTop : 0,
      document.body ? document.body.scrollTop : 0
    );
  },

  cross_filterResults: function(n_win, n_docel, n_body) {
    var n_result = n_win ? n_win : 0;
    if (n_docel && (!n_result || (n_result > n_docel)))
      n_result = n_docel;
    return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
  }

}

admin.init();
