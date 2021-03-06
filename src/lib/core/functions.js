/** 
 * File:        functions.js
 * Date:        Mon Sep 19 17:02:28 2011
 *
 * simple javascript functions ( works perfectly together with jquery/mootools etc)
 * it purpose: basic domready / mobile / js stuff.
 * 
 * Changelog:
 *
 * 	[Mon Sep 19 17:02:28 2011] 
 *		first sketch from scratch
 *
 * @todo description
 *
 *
 * @version $id$
 * @copyright 2011 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license 
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

/**  MICRO SUTRA FRAMEWORK BY THE CODER OF SALVATION : minimalistic, nonobtrusive, easily co-exists with prof.frameworks 
/**/ var tplVars   =                              {}
/**/ var domLoaded = function (callback)          {  /* Internet Explorer */  /*@cc_on  @if (@_win32 || @_win64)    document.write('<script id="ieScriptLoad" defer src="//:"><\/script>');    document.getElementById('ieScriptLoad').onreadystatechange = function() {      if (this.readyState == 'complete') {        callback();      }    };    return;  @end @*/  /* Mozilla, Chrome, Opera */  if (document.addEventListener) {    document.addEventListener('DOMContentLoaded', callback, false);    return;  }  /* Safari, iCab, Konqueror */  if (/KHTML|WebKit|iCab/i.test(navigator.userAgent)) {    var DOMLoadTimer = setInterval(function () {      if (/loaded|complete/i.test(document.readyState)) {        callback();        clearInterval(DOMLoadTimer);      }    }, 10);    return;  }  /* Other web browsers */  window.onload = callback;};
/**/ if( typeof( window.$ ) != "Function" ) window.$ = function $( id ){ var el   = document.getElementById( id ); var tags = ["div","img","span","form","b","a","i","u","td","table"]; if( el && el.id != id && el.name == id ){ for( i in tags ){ var els = document.getElementsByTagName( tags[i] ); for( j in els ) if( els[j].id == id ) return els[j]; } } return el;}
/**/ function $assign( varname, value )           { tplVars[ varname ] = value; }
/**/ function $fetch( content )                   { for( key in tplVars ){ reg = new RegExp( "\\{\\$"+key+"\\}", 'g' ); content = content.replace( reg, this.tplVars[key] ); } reg = new RegExp( "\\{\\$[A-Za-z0-9_-]*\\}", 'g' ); content = content.replace( reg, "" ); return content; }
/**/ function $sutra( id )                        { return $( id ); }
/**/ function $print_r(theObj, outputEl )         { var html; if( is.Array( theObj ) || is.Object( theObj ) ){  html += "<ul style='margin-left:50px'>";  for(var p in theObj){   if( is.Array( theObj[p] ) || is.Object( theObj[p] ) ){    html += "<li>["+p+"] => "+typeof(theObj)+"</li>";    html += "<ul>";    $print_r(theObj[p]);    html += "</ul>";   } else {    html += "<li>["+p+"] => "+theObj[p]+"</li>";   }  }  html += "</ul>"; } var output = document.getElementById( outputEl ); if( output )  output.innerHTML += html; return html;}
/**/ function $_GET( name, url )                  { name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]"); var regexS = "[\\?&]"+name+"=([^&#]*)"; var regex = new RegExp( regexS ); var results = regex.exec( url ? url : window.location.href ); if( results == null ) return ""; else return results[1]; }
/**/ function $hyphenate( str )                   { if ( !str ) return false; str = String(str); str = str.toLowerCase(); return str.replace(/[^a-z0-9]/gi, "-");}
/**/ function $formToString( formId )             { var form  = $sutra( formId ); if( !form ) return "form not found"; var elements = form.elements; var nElements = elements.length; var string = ""; for( var i = 0; i < nElements; i++ ){   var element = elements[i];   if( element == null ) continue;  if ( element.type == "checkbox" || element.type == "radio" ) {    string += element.name;    string += element.checked ? "=true&" : "=false&";  }else if (element.type == 'select-one') {     string += element.name + "=" + encodeURI( element.options[ element.selectedIndex ].value ) + "&";  }else if ( String(element.tagName).toLowerCase() == "textarea" ) {    string += element.name + "=" + encodeURI( element.value ) + "&";  }else if (element.type == 'text' || element.type == 'password' || element.type == 'hidden' ) {    string += element.name + "=" + encodeURI( element.value ) + "&";  } } return encodeURI(string); }
/**/ function $validateForm ( formId )            { var form  = $sutra( formId ); if( !form ) return "form not found"; var elements = form.elements;     var nElements = elements.length;      var ok = true;for( var i = 0; i < nElements; i++ ){           var element = elements[i];          if( element == null || !element.className.match("required") ) continue;          if (element.type == 'select-one' || element.type == "select"){              if(element.selectedIndex == 0){       ok = false;       element.style.background = "#FF0000";     }    }else if (element.type == 'text' || element.type == 'password' || element.type == 'hidden' ) {       if(element.value.length == 0 ){       element.style.background = "#FF0000";                ok = false;     }   }} return ok;}/**/ function $addEvent(obj,type,fn)              { if (obj.addEventListener) { obj.addEventListener(type,fn,false); return true; } else if (obj.attachEvent) { obj['e'+type+fn] = fn; obj[type+fn] = function() { obj['e'+type+fn]( window.event );}; var r = obj.attachEvent('on'+type, obj[type+fn]); return r; } else { alert("event chaining disabled!"); obj['on'+type] = fn; obj['on'+type] = (function(old) {     return function() {     if (old) old.call(this, arguments);     fn.call( this.arguments );     return false;    }    })( obj['on'+type ].onclick ); return true; } }
/**/ function $checkExtension( file, array_ext )  { var ok = false; var ext = file.substring(file.length-3,file.length); ext = ext.toLowerCase(); for( var i = 0; i < array_ext.length; i++ )  if( ext == array_ext[ i ] )   ok = true; return ok;}
/**/ function $captcha(formId)                    { var a = prompt("SPAMBOT protection:\n\nHow much is 1 + 2 ?");    if( !a || a.length == 0 ) return false;    var e = document.createElement("input");    e.type = "hidden"; e.name = "magic"; e.value = a;    $sutra(formId).appendChild( e );    setTimeout( "$sutra(formId).submit()", 10 );  }
/**/ function $in_array (needle,haystack,argStrict){  var key = '', strict = !!argStrict;   if (strict) {    for (key in haystack) {      if (haystack[key] === needle) {        return true;      }    }  } else {    for (key in haystack) {      if (haystack[key] == needle) {        return true;      }    }  }   return false; }
/**/ function $hasClass( className, classNames )  { if( classNames == undefined ) return false; var names = classNames.split(' '); return $in_array( className, names ); }
/**/ function $getPosX(e)                         {  var x = 0;  while (e) {    x += e.offsetLeft;    e = e.offsetParent;  }  return x;}
/**/ function $getPosY(e)                         {  var x = 0;  while (e) {    x += e.offsetTop;    e = e.offsetParent;  }  return x;}
/**/ function _assert( expr, description )        { if( !expr ){ var err = "SUTRA ASSERTION FAIL: "+description; if( window.console ) console.log(err); alert(err); return false; }else return true; }
/**/ function _trace( mixed )                     { if( window.console ){ if( !is.String(mixed) && console.dir != undefined ) console.dir(mixed); else console.log("SUTRA TRACE: "+mixed); } return mixed }

// autofire crossbrowser onload
// eval is evil, but we do this because IE8 loads external & inline javascripts parallel
var on = { domready: function(){ eval("onDomReady();");} } 
domLoaded( on.domready );
