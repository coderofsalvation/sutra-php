<h1 class="containerTitle">{$admin_title}</h1>{$uinotify}
<div class="clear"></div>
<div id="helpInfo" name="helpInfo" class="opacity-off">
  <i>{$admin_description}</i>
</div>

{*<div name="page" class="cmsunit23 last padding margin-top margin-bottom">*}
<div>
  <div align="right" id="filters">
    {#advanced#}<input type="checkbox"  onclick="admin.toggleNames( 'advanced', this.checked )"><br>
    {#help#}<input type="checkbox"  onclick="admin.toggleNames( 'helpInfo', this.checked, true ); admin.toggleNames( 'help', this.checked )" ><br>
    {foreach from=$filter item="f"}
      {$f|ucfirst}<input type="checkbox"  onclick="admin.toggleNames( '{$f}', this.checked );" ><br>
    {/foreach}
  </div>
  <div align="right" class="margin-top padding-left" id="navigation">
<!--    <a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>--> 
<!--    <a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>-->
<!--    <a href="" class="ajax" rel="popupContent">Some nav&nbsp;&laquo;</a><br>-->
  </div>
  {$container_content}
</div>
{*</div>*}
<div class="cmsunit01"></div> <!-- *FIXFME* why does this prevent overflow? -->
<div class="clear"></div>
