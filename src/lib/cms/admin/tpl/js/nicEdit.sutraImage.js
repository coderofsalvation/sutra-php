/* START CONFIG */
var nicSutraImageOptions = {
	buttons : {
		'sutraImage' : {name : 'Add Image', type : 'nicSutraImageButton', tags : ['IMG']}
	}
	
};
/* END CONFIG */

var nicSutraImageButton = nicEditorAdvancedButton.extend({	
	addPane : function() {
		this.im = this.ne.selectedInstance.selElm().parentTag('IMG');
		this.addForm({
			'' : {type : 'title', txt : 'Add/Edit Image'},
			'src' : {type : 'text', txt : 'URL', 'value' : 'http://', style : {width: '150px'}},
			'alt' : {type : 'text', txt : 'Alt Text', style : {width: '100px'}},
			'align' : {type : 'select', txt : 'Align', options : {'left' : 'Left', 'right' : 'Right'}}
		},this.im);
    var el                = document.createElement("br");
    el.style.clear        = "both";
    var tree              = document.createElement("div");
    tree.className        = "block border curved fill padding";
    tree.id               = "tree";
    this.form.appendChild( el );
    this.form.appendChild( tree );
    window.ajax.doRequest( '/widget/filemanager/tree?setOnClick=treeOnClick', '', 'GET', 'tree' );
	},
	
	submit : function(e) {
		var src = this.inputs['src'].value;
		if(src == "" || src == "http://") {
			alert("You must enter a valid Image URL to insert");
			return false;
		}
		this.removePane();
		if(!this.im) {
			var tmp = 'javascript:nicImTemp();';
			this.ne.nicCommand("insertImage",tmp);
			this.im = this.findElm('IMG','src',tmp);
		}
		if(this.im) {
			this.im.setAttributes({
				src : this.inputs['src'].value,
				alt : this.inputs['alt'].value,
				align : this.inputs['align'].value
			});
      this.im.className = "img-align-"+this.inputs['align'].value;
		}
	}
});

window.treeOnClick      = function( id ){ 
  var url = baseurl.substr( 0, baseurl.length - 1 );
  url    += "/data/upload";
  url    += id;
  $sutra("src").value = url;
  return false;
}

//nicEditors.registerPlugin(nicPlugin,nicSutraImageOptions);
