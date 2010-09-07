<h1>{$admin_title}</h1>{$uinotify}
<div class="clear"></div>
<div id="helpInfo" name="helpInfo" class="opacity-off">
  <i>{$admin_description}</i>
</div>

<div name="page" class="cmsunit17 block border curved fill padding margin-top margin-bottom">
  {$container_content}
</div>
<div class="cmsunit09 margin-right margin-top margin-bottom padding last" id="right">
  <div align="right" id="filters">
    {#advanced#}<input type="checkbox"  onclick="admin.toggleNames( 'advanced', this.checked )"><br>
    {#help#}<input type="checkbox"  onclick="admin.toggleNames( 'helpInfo', this.checked, true ); admin.toggleNames( 'help', this.checked )" ><br>
    {foreach from=$filter item="f"}
      {$f|ucfirst}<input type="checkbox"  onclick="admin.toggleNames( '{$f}', this.checked );" ><br>
    {/foreach}
  </div>
  <div align="right" class="margin-top padding-left" id="navigation">
    {*<a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>*}
    {*<a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>*}
    {*<a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>*}
  </div>
</div>
<div class="clear"></div>
