/*
	Feel free to use your custom icons for the tree. Make sure they are all of the same size.
	User icons collections are welcome, we'll publish them giving all regards.
*/
if( !baseurl )
  baseurl = "";
var TREE_TPL = {
	'target'  : '_blank',	// name of the frame links will be opened in
							// other possible values are: _blank, _parent, _search, _self and _top

	'icon_e'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/empty.gif', // empty image
	'icon_l'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/empty.gif',  // vertical line
	
  'icon_32' : baseurl+'/lib/core/tree/tpl/doxygen/icons/base.gif',   // root leaf icon normal
  'icon_36' : baseurl+'/lib/core/tree/tpl/doxygen/icons/base.gif',   // root leaf icon selected
	
	'icon_48' : baseurl+'/lib/core/tree/tpl/doxygen/icons/folder.gif',   // root icon normal
	'icon_52' : baseurl+'/lib/core/tree/tpl/doxygen/icons/foldersel.gif',   // root icon selected
	'icon_56' : baseurl+'/lib/core/tree/tpl/doxygen/icons/folder.gif',   // root icon opened
	'icon_60' : baseurl+'/lib/core/tree/tpl/doxygen/icons/foldersel.gif',   // root icon selected
	
	'icon_16' : baseurl+'/lib/core/tree/tpl/doxygen/icons/folder.gif', // node icon normal
	'icon_20' : baseurl+'/lib/core/tree/tpl/doxygen/icons/foldersel.gif', // node icon selected
	'icon_24' : baseurl+'/lib/core/tree/tpl/doxygen/icons/folderopen.gif', // node icon opened
	'icon_28' : baseurl+'/lib/core/tree/tpl/doxygen/icons/foldersel.gif', // node icon selected opened

	'icon_0'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/page.gif', // leaf icon normal
	'icon_4'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/pagesel.gif', // leaf icon selected
	
	'icon_2'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/empty.gif', // junction for leaf
	'icon_3'  : baseurl+'/lib/core/tree/tpl/doxygen/icons/empty.gif',       // junction for last leaf
	'icon_18' : baseurl+'/lib/core/tree/tpl/doxygen/icons/plus.gif', // junction for closed node
	'icon_19' : baseurl+'/lib/core/tree/tpl/doxygen/icons/plus.gif',       // junctioin for last closed node
	'icon_26' : baseurl+'/lib/core/tree/tpl/doxygen/icons/minus.gif',// junction for opened node
	'icon_27' : baseurl+'/lib/core/tree/tpl/doxygen/icons/minus.gif'       // junctioin for last opended node
};

