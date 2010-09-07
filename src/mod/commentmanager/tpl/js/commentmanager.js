var cache      = Array();

/* makeEditable - converts the element to an input and saves value when 'onchange' event is fired
 *
 */
function makeEditable( id, comment_id ){
  if( !$sutra(id) || $sutra("input_"+id) )
    return;
  var value      = trim( $sutra(id).innerHTML );
  var input      = null;
  if( value.length < 20 ){
    input      = document.createElement( 'input' );
    input.type     = "text";
    input.value    = value
  }else{
    input           = document.createElement( 'textarea' );
    input.innerHTML = value
  }
  cache['id']            = value;
  input.style.width      = "100%";
  input.style.height     = "21px";
  input.style.fontFamily = $sutra(id).style.fontFamily;
  input.style.border     = "none";
  input.id               = "input_"+id;
  input.onchange         = function(){
    var value = trim( $sutra("input_"+id).value );
    if( $sutra("input_"+id).type != undefined && 
        $sutra("input_"+id).type == "text"    && 
        value.length > 15 &&
        value != cache[ id ] )
      $sutra(id).innerHTML = value.substr(0,15)+"..";
    else $sutra(id).innerHTML = trim( value );
    window.ajax.doRequest('/commentmanager/detail',"id="+comment_id+"&event=SAVE_COMMENT_VAR&"+id+"="+value,'POST');
  };
  if( $sutra(id).href != undefined )
    $sutra(id).onclick = function(){ return false; };
  $sutra(id).innerHTML = "";
  $sutra(id).appendChild( input );
  input.focus();
}

function makeEditableAll( elements, comment_id ){
  for( i in elements )
    makeEditable( i, comment_id );
} 

function trim(str, chars) {
  return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
  chars = chars || "\\s";
  return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
  chars = chars || "\\s";
  return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
