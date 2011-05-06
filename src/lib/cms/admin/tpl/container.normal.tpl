{imagefont tag="h1" text=$admin_title font="Arista.ttf" fontsize="24" bgcolor1="FFFFFF" fgcolor1="555555" reflection=true fade_start=20 fade_end=0 fade_height=30}
{$uinotify}

<div class="clear"></div>

<div name="page" class="cmsunit17 block border curved fill padding margin-top margin-bottom">
  {$container_content}
</div>
<div class="cmsunit08 margin-right margin-top margin-bottom padding last" id="right">
  <div align="right" id="filters">
    {if !$admin_hideadvanced}
    {#advanced#}&nbsp;&nbsp;<input type="checkbox"  onclick="admin.toggleNames( 'advanced', this.checked )"><br>
    {/if}
    {#explanation#}&nbsp;&nbsp;<input type="checkbox"  onclick="admin.toggleNames( 'helpInfo', this.checked, true ); admin.toggleNames( 'help', this.checked )" ><br>
    {foreach from=$filter item="f"}
      {$f|ucfirst}&nbsp;&nbsp;<input type="checkbox"  onclick="admin.toggleNames( '{$f}', this.checked );" ><br>
    {/foreach}
  </div>
  {if is_array($link)}
  <div align="right" class="padding-left" id="navigation">
    {foreach from=$link item="l"}
    <a href="{$l.href}" class="ajax" rel="popupContent">{$l.label|ucfirst}&nbsp;&laquo;</a><br>
    {/foreach}
  </div>
  {/if}
  <div id="helpInfo" name="helpInfo" class="opacity-off">
    <i>{$admin_description}</i>
  </div>
  <div class="clear">
</div>
<div class="clear"></div>
