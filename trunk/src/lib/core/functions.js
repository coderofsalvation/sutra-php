/**  MICRO SUTRA FRAMEWORK BY THE CODER OF SALVATION : minimalistic, nonobtrusive, easily co-exists with prof.frameworks 
/**/ var tplVars  = {}
/**/ if (typeof Event == 'undefined') Event     = new Object();
/**/ Event.domReady                             = { add: function(fn) {if (Event.domReady.loaded) return fn();var observers = Event.domReady.observers;if (!observers) observers = Event.domReady.observers = [];observers[observers.length] = fn;if (Event.domReady.callback) return;Event.domReady.callback = function() {if (Event.domReady.loaded) return; Event.domReady.loaded = true;if (Event.domReady.timer) {clearInterval(Event.domReady.timer);Event.domReady.timer = null;} var observers = Event.domReady.observers;for (var i = 0, length = observers.length; i < length; i++) {var fn = observers[i];observers[i] = null;fn(); }Event.domReady.callback = Event.domReady.observers = null;};var ie = !!(window.attachEvent && !window.opera);var webkit = navigator.userAgent.indexOf('AppleWebKit/') > -1; if (document.readyState && webkit) {Event.domReady.timer = setInterval(function() {var state = document.readyState;if (state == 'loaded' || state == 'complete') {Event.domReady.callback();}}, 50); } else if (document.readyState && ie) { var src = (window.location.protocol == 'https:') ? '://0' : 'javascript:void(0)';document.write('<script type="text/javascript" defer="defer" src="' + src + '" ' +'onreadystatechange="if (this.readyState == \'complete\') Event.domReady.callback();"' +'><\/script>'); } else { if (window.addEventListener) {document.addEventListener("DOMContentLoaded", Event.domReady.callback, false);window.addEventListener("load", Event.domReady.callback, false);} else if (window.attachEvent) {window.attachEvent('onload', Event.domReady.callback);} else {var fn = window.onload;window.onload = function() {Event.domReady.callback();if (fn) fn();}} } }}
/**/ function $assign( varname, value )           { tplVars[ varname ] = value; }
/**/ function $fetch( content )                   { for( key in tplVars ){ reg = new RegExp( "\\{\\$"+key+"\\}", 'g' ); content = content.replace( reg, this.tplVars[key] ); } reg = new RegExp( "\\{\\$[A-Za-z0-9_-]*\\}", 'g' ); content = content.replace( reg, "" ); return content; }
/**/ function $sutra( id )                        { return document.getElementById( id ); }
/**/ function $print_r(theObj, outputEl )         { var html; if( is.Array( theObj ) || is.Object( theObj ) ){  html += "<ul style='margin-left:50px'>";  for(var p in theObj){   if( is.Array( theObj[p] ) || is.Object( theObj[p] ) ){    html += "<li>["+p+"] => "+typeof(theObj)+"</li>";    html += "<ul>";    print_r(theObj[p]);    html += "</ul>";   } else {    html += "<li>["+p+"] => "+theObj[p]+"</li>";   }  }  html += "</ul>"; } var output = document.getElementById( outputEl ); if( output )  output.innerHTML += html; return html;}
/**/ function $_GET( name, url )                  { name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]"); var regexS = "[\\?&]"+name+"=([^&#]*)"; var regex = new RegExp( regexS ); var results = regex.exec( url ? url : window.location.href ); if( results == null ) return ""; else return results[1]; }
/**/ function $hyphenate( str )                   { if ( !str ) return false; str = String(str); str = str.toLowerCase(); return str.replace(/[^a-z0-9]/gi, "-");}
/**/ function $formToString( formId )             { var form  = $sutra( formId ); if( !form ) return "form not found"; var elements = form.elements; var nElements = elements.length; var string = ""; for( var i = 0; i < nElements; i++ ){ var element = elements[i]; if( element == null ) continue; if ( element.type == "checkbox" || element.type == "radio" ) { string += element.name; string += element.checked ? "=true&" : "=false&"; }else if (element.type == 'select-one') { string += element.name + "=" + element.options[ element.selectedIndex ].value + "&"; }else if (element.type == 'text' || element.type == 'password' || element.type == 'hidden' ) { string += element.name + "=" + element.value + "&"; } } return encodeURI(string); }
/**/ function $validateForm ( formId )            { var form  = $sutra( formId ); if( !form ) return "form not found"; var elements = form.elements;     var nElements = elements.length;      var error  = false;     for( var i = 0; i < nElements; i++ ){         var element = elements[i];        if( element == null || !element.className.match("required") ) continue;        if (element.type == 'select-one'){          error &= (element.selectedIndex == 0);          element.style.background = "#FF0000";        }else       if (element.type == 'text' || element.type == 'password' || element.type == 'hidden' ) {             error &= (element.value.length > 0 );           if( element.value.length == 0 ) element.style.background = "#FF0000";         }      } return !error;  }
/**/ function $addEvent(obj,type,fn)              { if (obj.addEventListener) { obj.addEventListener(type,fn,false); return true; } else if (obj.attachEvent) { obj['e'+type+fn] = fn; obj[type+fn] = function() { obj['e'+type+fn]( window.event );}; var r = obj.attachEvent('on'+type, obj[type+fn]); return r; } else { alert("event chaining disabled!"); obj['on'+type] = fn; obj['on'+type] = (function(old) {     return function() {     if (old) old.call(this, arguments);     fn.call( this.arguments );     return false;    }    })( obj['on'+type ].onclick ); return true; } }
/**/ function $checkExtension( file, array_ext )  { var ok = false; var ext = file.substring(file.length-3,file.length); ext = ext.toLowerCase(); for( var i = 0; i < array_ext.length; i++ )  if( ext == array_ext[ i ] )   ok = true; return ok;}
