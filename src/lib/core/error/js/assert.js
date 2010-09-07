/** 
 * assert - basic assertion 
 *
 * @param string $var description 
 * @return mixed The new value 
 *
 *  <example>
 *    assert( is.Object( myObject ), "myObject must be of type object!" );
 *    assert( is.String( myString ), "myString must be of type object!" );
 *    assert( is.Array( myArray ),   "myArray must be of type object!" );
 *  </example> 
 *
 */ 
function assert( expr, description ){
  var popup             = false;
  var firebug_assert    = true;
  var email             = true;
  if( !expr ){
    var err       = "ASSERTION FAIL: "+description;
    if( popup )
      alert( err );
    if( firebug_assert )
      console.assert( expr, description );
    if( email && window.error != undefined )
      window.error.handleError( err, "", "", true );
  }
}

var is={
  Null:function(a){
    return a===null;
  },
  Undefined:function(a){
    return a===undefined;
  },
  nt:function(a){
    return(a===null||a===undefined);
  },
  Function:function(a){
    return(typeof(a)==='function')?a.constructor.toString().match(/Function/)!==null:false;
  },
  String:function(a){
    return(typeof(a)==='string')?true:(typeof(a)==='object')?a.constructor.toString().match(/string/i)!==null:false;
  },
  Array:function(a){
    return(typeof(a)==='object')?a.constructor.toString().match(/array/i)!==null||a.length!==undefined:false;
  },
  Boolean:function(a){
    return(typeof(a)==='boolean')?true:(typeof(a)==='object')?a.constructor.toString().match(/boolean/i)!==null:false;
  },
  Date:function(a){
    return(typeof(a)==='date')?true:(typeof(a)==='object')?a.constructor.toString().match(/date/i)!==null:false;
  },
  HTML:function(a){
    return(typeof(a)==='object')?a.constructor.toString().match(/html/i)!==null:false;
  },
  Number:function(a){
    return(typeof(a)==='number')?true:(typeof(a)==='object')?a.constructor.toString().match(/Number/)!==null:false;
  },
  Object:function(a){
    if( is.Null(a) || is.Undefined(a) ) return false;
    return(typeof(a)==='object') ? a.constructor.toString().match(/object/i) !==null : false;
  },
  RegExp:function(a){
    return(typeof(a)==='function')?a.constructor.toString().match(/regexp/i) !==null : false;
  }
};

var type={
  of:function(a){
    for(var i in is){
      if(is[i](a)){
        return i.toLowerCase();
      }
    }
  }
};
