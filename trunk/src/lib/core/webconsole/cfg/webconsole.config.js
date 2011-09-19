var navigation = {
  "M"              : {clearscreen: true,  url: "lib/core/webconsole/cfg/webconsole.menu.txt"},
  "S"              : {clearscreen: true,  url: "lib/core/webconsole/cfg/commands/info.php?argv[0]=info&argv[1]=server" },
  "E"              : {clearscreen: true,  url: "lib/core/webconsole/cfg/commands/info.php?argv[0]=info&argv[1]=session"},
  "U"              : {clearscreen: true,  url: "lib/core/webconsole/cfg/commands/info.php?argv[0]=info&argv[1]=sutra"  },
  "L"              : {clearscreen: true,  url: "lib/core/webconsole/cfg/commands/info.php?argv[0]=info&argv[1]=log"    },
  // html option is buggy *FIXME* need rendering bugfix
  //"U"            : {clearscreen: false, url: "tests/bbs/bbs.upload.php", html:true },
  "J"              : {clearscreen: true,  function: jstest},
};

var helpPage = [
   "%+|<img src='lib/core/webconsole/tpl/help.gif' style='position:absolute;left:25px;margin-top:10px'/>%-|",
   "                     *** Available commands ***",
   " ",
   "                     %c(cyan)info %c(default)<%c(white)server|session|sutra|log%c(default)>",
   " ",
   "                     type 'exit' to quit.",
   " ",
   " ",
   " ",
];

function jstest(){
  alert("this is a alert");
  myterm.prompt();
}
