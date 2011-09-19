/* CrossBrowser Stylable Tooltips
 *
 * Author   : www.guyfromchennai.com 
 *            Additional changes by Leon (izi-services.com)
 * Function : provides easy crossbrowser tooltips
 * Example  :
 *
 *    <html>
 *    <head>
 *      <title>Crossbrowser stylable tooltips</title>
 *      <script language="javascript" type="text/javascript" src="tooltip.js"></script>
 *      <style>
 *        .toolTip,
 *        .toolTip tr,
 *        .toolTip td,
 *        .toolTip th,
 *        .toolTip table        { margin:0; padding:0; filter:alpha(opacity=90); -moz-opacity: 90.0; opacity: 90.0; background-color: #FFF; color: #000 }
 *        .toolTip td           { padding: 0.5em; font-family: sans-serif, Verdana, Arial; font-size: 11px }
 *        .toolTip              { border: 1px solid #b1b1b1; }
 *      </style>
 *    </head>
 *    <body onload="tooltip.init();" bgcolor="#CCCCCC">
 *      <!-- non ajax version -->
 *      <a href="#" title="Simpele tooltip">normal title tooltips still work</a><br>
 *      <!-- ajax version -->
 *      <a href="#" class="tooltip" title="Dit....is een tooltip!" rel="http://yousite.com/gettooltipcontent">but now they are stylable!</a>
 *    </body>
 *    </html>
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

//browser detection
var agt=navigator.userAgent.toLowerCase();
var is_major = parseInt(navigator.appVersion);
var is_minor = parseFloat(navigator.appVersion);

var is_nav  = ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)
            && (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)
            && (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1));
var is_nav4 = (is_nav && (is_major == 4));
var is_nav6 = (is_nav && (is_major == 5));
var is_nav6up = (is_nav && (is_major >= 5));
var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));


var tooltip = {

  // tooltip layout
  toolTipDiv:  null,
  ajaxLoader:  "lib/core/tooltip/gfx/ajaxloader.gif",

  init: function( onlyAnchors){
    if( !document.getElementById) return;
    if( !onlyAnchors ){
      this.toolTipDiv                  = document.getElementById("toolTip") ? document.getElementById("toolTip") : document.createElement('div');
      this.toolTipDiv.style.left       = "0";
      this.toolTipDiv.style.right      = "0";
      this.toolTipDiv.style.position   = "absolute";
      this.toolTipDiv.id               = "toolTip";
      if( !document.getElementById("toolTip") )
        document.body.appendChild( this.toolTipDiv );
      this.toolTipDiv = this.toolTipDiv.style;

      if(is_ie || is_nav6up)
      {
        this.toolTipDiv.visibility = "visible";
        this.toolTipDiv.display = "none";
        document.onmousemove = tooltip.moveToMousePos;
      }
    }

    // now provide a tooltip for  all anchors with title tag
    var anchors = document.getElementsByTagName("a");
    for(var i = 0; i < anchors.length; i++) {
      if( anchors[i].title.length > 0 ){
        anchors[i].onmouseover = ( anchors[i].rel.length > 0 && anchors[i].className.search("tooltip") != -1 ) ?  
                                    new Function("tooltip.toolTip( '"+document.location.href+anchors[i].rel+"', true )") :
                                    new Function("tooltip.toolTip( '"+ anchors[i].title +"' )");
        anchors[i].onmouseout  = new Function("tooltip.toolTip()");
        anchors[i].title = "";
      }
    }
    if ( !ajax )
      alert("(tooltip.js) need ajax function : doRequest(url, args, method, elname ) <- modify this call according to your js framework!");
  },

  moveToMousePos: function(e)
  {
    if(!is_ie){
      x = e.pageX;
      y = e.pageY;
    }else{
      x = event.x + document.body.scrollLeft;
      y = event.y + document.body.scrollTop;
    }
    this.toolTipDiv = document.getElementById("toolTip").style;
    this.toolTipDiv.left = (x+25)+'px';
    this.toolTipDiv.top = (y+25)+'px';
    return true;
  },


  toolTip: function( data, isUrl )
  {
    if( this.toolTip.arguments.length == 0) // if no arguments are passed then hide the tootip
    {
      if(is_nav4)
        this.toolTipDiv.visibility = "hidden";
      else
        this.toolTipDiv.display = "none";
    }
    else // show
    {
      var content     = '<table border="0" cellspacing="0" cellpadding="0" class="toolTip"><tr><td>' +
                        '<table border="0" cellspacing="1" cellpadding="0"><tr><td id="toolTipText">';
      if( isUrl ){
        setTimeout( 'ajax.doRequest( "'+data+'", false, "GET", "toolTipText" )', 250 );
        content      += '<img id="ajaxloader" src="'+this.ajaxLoader+'"/>';
      }else content  += data;
      content        += '</td></tr></table>';
      content        += '</td></tr></table>';
      
      if(is_nav4){
        this.toolTipDiv.document.write(content);
        this.toolTipDiv.document.close();
        this.toolTipDiv.visibility = "visible";
      } else if(is_ie || is_nav6up){
        document.getElementById("toolTip").innerHTML = content;
        this.toolTipDiv.display='block'
      }
    }
  }
}  
