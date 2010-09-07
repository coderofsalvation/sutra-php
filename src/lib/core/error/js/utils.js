// *FIX* prevent console.log from crashing javascript if firebug is not available
if( typeof window.console == 'undefined'){ window.console = { log:function(){} }; }

/** 
 * print_r - php style var dumping in html or firebug
 * 
 * @param mixed input
 * @return string
 */ 
function print_r(theObj, outputEl ){
  var html;
  if( is.Array( theObj ) || is.Object( theObj ) ){
    html += "<ul style='margin-left:50px'>";
    for(var p in theObj){
      if( is.Array( theObj[p] ) || is.Object( theObj[p] ) ){
        html += "<li>["+p+"] => "+typeof(theObj)+"</li>";
        html += "<ul>";
        print_r(theObj[p]);
        html += "</ul>";
      } else {
        html += "<li>["+p+"] => "+theObj[p]+"</li>";
      }
    }
    html += "</ul>";
  }
  var output = document.getElementById( outputEl );
  if( output )
    output.innerHTML += html;
  return html;
}

/** 
 * hyphenate - replaces weird chars by a simple '-' (for conversion to css classnames,urls etc)
 * 
 * @param string str
 * @return string
 */ 
function hyphenate( str )
{
  if ( !str ) return false;
  str = String(str);
  str = str.toLowerCase();
  return str.replace(/[^a-z0-9]/gi, "-");
}

