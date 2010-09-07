/* here you can disable/enable certain (custom) icons */
nicConfig.buttonList            = ['save','bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','indent','outdent','sutraImage','link','unlink','forecolor','bgcolor'];
nicConfig.iconList              = {"xhtml":1,"bgcolor":2,"forecolor":3,"bold":4,"center":5,"hr":6,"indent":7,"italic":8,"justify":9,"left":10,"ol":11,"outdent":12,"removeformat":13,"right":14,"save":25,"strikethrough":16,"subscript":17,"superscript":18,"ul":19,"underline":20,"image":21,"sutraImage":21,"link":22,"unlink":23,"close":24,"arrow":26,"upload":27};
nicConfig.colorList             = [ "#4eb048", "#898b8c", "#000000" ];
nicConfig.formatList            = { 'h2' : 'Heading&nbsp;2', 'h3' : 'Heading&nbsp;3' };

nicEditors.registerPlugin(nicButtonTips);
nicEditors.registerPlugin(nicPlugin,nicSelectOptions);
nicEditors.registerPlugin(nicPlugin,nicLinkOptions);
nicEditors.registerPlugin(nicPlugin,nicColorOptions);
//nicEditors.registerPlugin(nicPlugin,nicImageOptions);
nicEditors.registerPlugin(nicPlugin,nicSaveOptions);
//nicEditors.registerPlugin(nicPlugin,nicUploadOptions);
nicEditors.registerPlugin(nicXHTML);
//nicEditors.registerPlugin(nicBBCode);
nicEditors.registerPlugin(nicPlugin,nicCodeOptions);
nicEditors.registerPlugin(nicPlugin,nicSutraImageOptions);
