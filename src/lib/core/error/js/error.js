/*****************************************************************************
 * Basic error handling class for javascript 1.1 and above
 *
 * 23-12-2009 [Coder of Salvation] start from scratch
 */


var error = {
  urlError:   "error",
  urlAssert:  "assert",
  ro:  (navigator.appName == 'Microsoft Internet Explorer') ? new ActiveXObject('Microsoft.XMLHTTP') : ro = new XMLHttpRequest(),

  init: function( url ){
    // assign own handler
    window.onerror = error.handleError; 
  },
  
  /*
   * advanced: a full error handler
   */
  handleError: function(err, file, line, isAssert) {
    var log = 'err='          +err+
              '&file='         +file+
              '&line='         +line+
              '&backtrace='    +encodeURI(error.getBacktrace())+
              '&host='         +document.location.host+
              '&href='         +document.location.href+
              '&pathname='     +document.location.pathname+
              '&appCodeName='  +navigator.appCodeName+
              '&appName='      +navigator.appName+
              '&appVersion='   +navigator.appVersion+
              '&cookieEnabled='+navigator.cookieEnabled+
              '&platform='     +navigator.platform+
              '&userAgent='    +navigator.userAgent;
    error.ro.open( 'post', isAssert ? error.urlAssert : error.urlError , true);
    error.ro.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    error.ro.send( log );
    return true; // error is handled
  },

  getBacktrace: function() {
    var callstack = [];
    var isCallstackPopulated = false;
    try {
      i.dont.exist+=0; //doesn't exist- that's the point
    } catch(e) {
      if( !callstack.length && e.stack != undefined )   callstack = e.stack;    // FIREFOX
      if( !callstack.length && e.message != undefined ) callstack = e.message;  // OPERA
    }
    if (!callstack.length) { //IE and Safari
      var currentFunction = arguments.callee.caller;
      while (currentFunction) {
        callstack += arguments.callee.toString() + " -> " + currentFunction.toString();
        currentFunction = currentFunction.caller;
      }
    }
    return callstack.replace( new RegExp( "@", "g" ),"\n@");
  }
}

error.init();
