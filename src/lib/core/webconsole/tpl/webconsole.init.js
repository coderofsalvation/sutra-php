var e = document.createElement('div');
e.style.position = 'absolute';
e.id             = 'termDiv';
e.className      = 'opacity-half curved';
document.body.appendChild(e);
var p = document.createElement('img');
p.src            = baseurl + "lib/core/webconsole/termlib/tests/bbs/bbs.preloader.gif"
p.id             = 'termLoader';
document.body.appendChild(e);
document.body.appendChild(p);
{foreach from=$cmds item="cmd"}
navigation[ "{$cmd.cmd}" ] = {literal}{{/literal} clearscreen:false, url:"lib/core/webconsole/cfg/commands/{$cmd.file}" {literal}}{/literal}
{/foreach}
openTerminal('[{$prompt}] $ ', 'termLoader');
