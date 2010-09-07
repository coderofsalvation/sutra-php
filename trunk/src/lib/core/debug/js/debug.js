/*
 * (PRIVATE) Browser detection
 */
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
// inspect target ( Firebug stylee)
var inspectTarget; 
var inspectStyle;
var inspectClick;

/* 
 * (PRIVATE) js debug console
 *
 * (c) gosha bine, 2007 | www.tagarga.com/blok 
 */

var Dbgc = {
  promptTitle:  " command> ",
  readable:     false,
	css:          'width:100%;height:250px;font-size:11px;background:black;color:#8cc;',

	print: function(s, nodelay) {
    if( nodelay )
  		setTimeout('Dbgc._print("' + escape(s) + '")', 1);
    else Dbgc._print( escape(s) );
	},
	clear: function() {
		try { document.getElementById('_DEBUG_CONSOLE_').value = Dbgc.promptTitle; } catch(e) {}
	},
	trace: function(obj, depth, funcs) {
		Dbgc.print(Dbgc.inspect(obj, depth, funcs));
	},
  prompt: function( msg ){
	  var console  = document.getElementById('_DEBUG_CONSOLE_');
    if( console ){
      if( console.value.length ){
        if( msg && msg.length != undefined && msg.length )
          console.value += msg + "\n" + Dbgc.promptTitle;
        else
          console.value += Dbgc.promptTitle;
      }
      Dbgc._scrolldown();
    }
  },
	inspect: function(obj, depth, funcs) {
		depth = parseInt(depth);
		return Dbgc._inspect(obj, '  ', isNaN(depth) ? 1 : depth, funcs);
	},
	close: function() {
		try {
			var o = document.getElementById('_DEBUG_CONSOLE_');
      o.style.display = "none";
		} catch(e) {}
	},
	stack: function() {

		function funcname(fp){
			var p = /function\s+(\w+)/.exec(fp + '');
			return p ? p[1] : 'anonymous';
		}

		var f = Dbgc.stack.caller, n = 0, buf = '';
		while(f) {
			var args = f.arguments, a = [];
			for(var i = 0; i < args.length; i++)
				a[a.length] = args[i] + '';
			buf += (++n) + ': ' + funcname(f) + '(' + a.join(', ') + ')\n';
			f = (f.caller == f) ? null : f.caller;
		}
		Dbgc._print(buf);
	},
	time: function(func, count) {
		count = Math.abs(parseInt(count) || 1);
		var time = new Date();
		while(count--) func();
		return new Date() - time;
	},

  _cli: function( lines ){
    lines = String(lines);
    if( lines.charAt( lines.length-1 ) != "\n" )
      return;
    lines = lines.split( "\n");
    lines.pop(); // remove latest break
    line  = lines[ lines.length-1 ];
    if( line ){
      myprompt= String(Dbgc.promptTitle).length;
      cmd   = line.slice( myprompt  );
      Dbgc.prompt( cli_run(cmd) );
    }
  },

  _scrolldown: function(){
    o = document.getElementById("_DEBUG_CONSOLE_");
    if( o.scrollTop != undefined )  {
      o.scrollTop = o.scrollHeight;
      s = o.scrollTop;
      o.focus();
      o.scrollTop = s;
    }
  },

	_inspect: function(obj, tab, depth, funcs) {
		var type = typeof(obj), klass = null,  value = (obj + ''), m, v, c = [];
		var minDepth = -10;

		if(obj === null) return 'null';
		if(type == 'undefined') return 'undefined';

		if(m = value.match(/\[object\W+(\w+)/))
			klass = m[1];
		else if(obj.constructor && (m = (obj.constructor + '').match(/function\s+(\w+)/)))
			klass = m[1];

		if(type == 'function' && value.match(/\[native.*?\]\s*\}/))
			value = '[native]';
		if(type == 'string' || type == 'function') {
      if( !Dbgc.readable )
        value = value.replace(/\s/g, function($0) { return {'\n':'\\n','\r':'\\r','\t':'\\t',' ':'\xBA'}[$0] })
      else
        value = value;
		}
		if(type == 'object') {
			if(depth < minDepth) {
				value = '?? RECURSION...';
			} else if(depth != 0) {
				var v;
				for(var p in obj) {
					try { v = Dbgc._inspect(obj[p], tab + '  ', depth - 1); }
					catch(e) { v = '?? ' + (e.message || e.description); }
					if(funcs && v.match(/^function\b/i))
						c.push(tab + p + ': ' + v);
					if(!funcs && !v.match(/^function\b/i))
						c.push(tab + p + ': ' + v);
				}
        if( obj.style != undefined && depth == 0){
          for( var p in obj.style ){
            if( is_ie )
              value = obj.currentStyle[ p ];
            else{
              cs    = document.defaultView.getComputedStyle(obj,null);
              value = cs.getPropertyValue( p );
            }
            if( value )
              c.push(tab +"style."+p+": "+  value );
          }
        }
			}
		}
		if(klass && type.toLowerCase() != klass.toLowerCase())
			type += ' ' + klass;
		if(typeof obj.length != 'undefined')
			type += '(' + obj.length + ')';
		c.unshift(type + '="' + value + '"');
		return c.join('\n');
	},
	_print: function(s) {
		s = unescape(s);
		if(document && document.getElementById && (document.getElementById('_DEBUG_CONSOLE_') || Dbgc._open()) ) {
			var o = document.getElementById('_DEBUG_CONSOLE_');
      o.onkeyup = function(){ Dbgc._cli( o.value ); }
			o.value += " " + s +'\n';
		}// else alert(s);
    Dbgc._scrolldown();
	},
	_open: function() {
		try {
			var div = document.createElement('DIV');
      div.id = "_DEBUG_CONTAINER_";
			div.innerHTML = "<textarea id='_DEBUG_CONSOLE_' wrap='off' style='" + Dbgc.css + "' onkeyup='Dbgc._cli( this.value )'></textarea>";
			if(window.attachEvent) {
				div.style.position = 'absolute';
				div.style.top = Dbgc._ietop();
			} else {
				div.style.position = 'fixed';
				div.style.top = 0;
			}
      div.style.width = "100%";
      div.style.height = "250px";
			div.style.right = 0;
			document.body.appendChild(div);
      document.body.style.padding = "250px 0px 0px 0px";
      document.body.style.backgroundPosition = "0px 250px";
			return true;
		} catch(e) { return false; }
	},
	_ietop: function() {
		return parseInt(document.documentElement.scrollTop || document.body.scrollTop) + 'px';
	}
}

/*
 * (PRIVATE) Basic Command line interface parser (CLI)
 */

function arguments_to_array( args )
{
  var arr = new Array();
  for (var i=0; i<args.length; ++i) {
    arr[i] = args[i];
  }
  return arr;
}

function cli_remove_blank_words( words )
{
  // Remove leading and trailing blank words.
  for( i = 0; i < words.length; i++ ){
    while (words[i].length > 0 && words[i].charAt( 0 ) == ' ') 
      words[i] = words[i].slice( 1 );
    while (words[i].length>0 && words[i].charAt( words.length-1 )==" ") 
      words[i] = words[i].slice( 0, words.length-1 );
  }
  return words;
}

function cli_run( cmd )
{
  cmd = String(cmd); // IE6
  // (DEVELOPER ONLY) take the risk to crash javascript :)
  if( cmd.match("\\(") && cmd.match("\\)") || cmd.match("=") ){ 
    eval(cmd);
    return;
  }
  words = cmd.split( /\s+/ );
  words = cli_remove_blank_words( words );

  var last_cmd_word = null;

  for (var i=0; i<words.length; ++i) {
    var fun_name = words.slice( 0, i+1 ).join( "_" );
    if (window[fun_name] == undefined) {
      break;
    } else {
      last_cmd_word = i;
    }
  }
  // check for DOM id's
  if( document.getElementById( words[0] ) != undefined ){
    depth = ( words.length > 1 ) ? words[1] : 1;
    func  = ( words.length > 2 ) ? words[2] : 0;
    return Dbgc.trace( document.getElementById( words[0] ), depth, func );
  }

  // error check
  if( words[0].match( "ls" ) || words[0].match( "dir" ) )
    return " hehe :] type 'help'";
  if (last_cmd_word===null || words.length==0)
    return " No such command \""+words[0]+"\"";

  // call func
  var fun_name = words.slice( 0, last_cmd_word+1 ).join( "_" );
  var fun = window[fun_name];
  var args = words.slice( last_cmd_word+1 );
  fun.apply( this, args );
}

/*
 * inspect DOM elements like firefox
 */
function mouseOver(e){
  // IE is retarded and doesn't pass the event object 
  if (e == null) e      = window.event; 
  if (inspectTarget != null){
    inspectTarget.style.border = inspectTarget.style;
    inspectTarget.onclick      = inspectTarget.click;
  }
  // IE uses srcElement, others use inspectTarget.
  inspectTarget                 = e.target != null ? e.target : e.srcElement;
  if( inspectTarget != null && inspectTarget.id != '_DEBUG_CONSOLE_' ){
    inspectTarget.style.border  = inspectTarget.style.border;
    inspectTarget.click         = inspectTarget.onclick;
    inspectTarget.href          = inspectTarget.href;
    inspectTarget.onclick       = function(){}
    inspectTarget.style.border  = "1px dotted black";
  }
}

function mouseDown(e){
  if( inspectTarget != null && inspectTarget.id != '_DEBUG_CONSOLE_' ){
    say( "INSPECT:  "+inspectTarget.tagName );
    __say( inspectTarget.style, 2 );
    Dbgc.prompt();
  }
}

/* 
 * PUBLIC FUNCTIONS
 */

// simple output without element/func inspection
function say( msg ) {
	Dbgc.print( msg );
  Dbgc.prompt();
}

// auto element/func inspection
function _say() {
	var buf = [];
	for(var i = 0; i < arguments.length; i++)
		buf.push(Dbgc.inspect(arguments[i]));
	Dbgc.print( buf.join('\n'));
}

// use this to break out of recursive mayhem
function __say(obj, depth, funcs) {  
	Dbgc.trace(obj, depth, funcs);
}

/*
 * CLI function bindings
 */

function help(){
  var o = document.getElementById('_DEBUG_CONSOLE_');
  o.value += " +----------------------------------------------------------------------------------+\n";
  o.value += " | help                                   - this help screen                        |\n";
  o.value += " | close                                  - closes this window                      |\n";
  o.value += " | inspect                                - inspect elements with mouseclick        |\n";
  o.value += " | clear                                  - clears all text in this window          |\n";
  o.value += " | time <funcname>                        - measure execution time                  |\n";
  o.value += " | stack                                  - view stack                              |\n";
  o.value += " | readable                               - interpret specialchars (linebreaks etc) |\n";
  o.value += " | resize <height>                        - resize console to <height> pixels       |\n";
  o.value += " | trace <varname> [recdepth] [showfuncs] - run recursive trace on JS var           |\n";
  o.value += " | <DOM id> [recdepth] [showfuncs]        - automatically runs trace on DOM element |\n";
  o.value += " | javascript expr                        - will be evalled (dangerous) :]          |\n";
  o.value += " |                                                                                  |\n";
  o.value += " | to call these commands inside JS code : just add ( )                             |\n";
  o.value += " +----------------------------------------------------------------------------------+\n";
}

function inspect()  { document.onmouseover = mouseOver; document.onmousedown = mouseDown; }
function close()    { Dbgc.close() }
function clear()    { Dbgc.clear();}
function stack()    { Dbgc.stack();}
function readable() { Dbgc.readable = !Dbgc.readble;}
function trace(obj, depth, func) { Dbgc.trace( window[obj] ? window[obj] :eval(obj), depth, func ); 
}
function time(f){ 
  var o = document.getElementById('_DEBUG_CONSOLE_');
  o.value += ( window[f] ) ? " "+Dbgc.time( window[f] ) + " ms\n" : " no function found by name '"+f+"'\n";
}
function resize(height){
  if( height ){
    document.body.style.padding = height+"px 0px 0px 0px";
    document.getElementById("_DEBUG_CONTAINER_").style.height = height;
    document.getElementById("_DEBUG_CONSOLE_").style.height = height;
  }
}
