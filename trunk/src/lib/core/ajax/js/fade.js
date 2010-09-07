function shiftOpacity(id, millisec) {
    //if an element is invisible, make it visible, else make it ivisible
    var el = document.getElementById(id);
    if( !el ) return;
    var op = getOpacity(id);
    if( op < 1)
      opacity(id, 0, 100, millisec);
    else
      opacity(id, 100, 0, millisec);
} 

function opacity(id, opacStart, opacEnd, millisec) {
    //speed for each frame
    var speed = Math.round(millisec / 100);
    var timer = 0;

    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("changeOpacity(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            setTimeout("changeOpacity(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
}

//change the opacity for different browsers
function changeOpacity(opacity, id) {
    var object = document.getElementById(id);
    if( !object ) return;
    object.style.opacity = (opacity / 100);
    object.style.MozOpacity = (opacity / 100);
    object.style.KhtmlOpacity = (opacity / 100);
    object.style.filter = "alpha(opacity=" + opacity + ")";
} 

function getOpacity( id) {
    var object = document.getElementById(id);
    if( !object ) return;
    if( object.style.opacity      != undefined ) return object.style.opacity      * 100;
    if( object.style.MozOpacity   != undefined ) return object.style.MozOpacity   * 100;
    if( object.style.KhtmlOpacity != undefined ) return object.style.KhtmlOpacity * 100;
    if( object.style.filter       != undefined ) return object.style.filter;
} 
