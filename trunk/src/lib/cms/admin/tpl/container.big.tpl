{imagefont tag="h1" text=$admin_title font="Arista.ttf" fontsize="24" bgcolor1="FFFFFF" fgcolor1="555555" reflection=true fade_start=20 fade_end=0 fade_height=30}
{$uinotify}
<div class="clear"></div>

{*<div name="page" class="cmsunit23 last padding margin-top margin-bottom">*}
<div>
  <div align="right" id="filters">
    {if !$admin_hideadvanced}
    {#advanced#}&nbsp;&nbsp;<input type="checkbox" onclick="admin.toggleNames( 'advanced', this.checked )"><br>
    {/if}
    {if !$admin_hidedescription}
    {#explanation#}&nbsp;&nbsp;<input type="checkbox"  onclick="admin.toggleNames( 'helpInfo', this.checked, true ); admin.toggleNames( 'help', this.checked )" ><br>
    {/if}
    {foreach from=$filter item="f"}
      {$f|ucfirst}&nbsp;&nbsp;<input type="checkbox" onclick="admin.toggleNames( '{$f}', this.checked );" ><br>
    {/foreach}
    {if is_array($link)}
      <div align="right" class="padding-left" id="navigation">
        {foreach from=$link item="l"}
        <a href="{$l.href}" class="ajax" rel="popupContent">{$l.label|ucfirst}&nbsp;&laquo;</a><br>
        {/foreach}
      </div>
    {/if}
    {if !$admin_hidedescription}
    <div id="helpInfo" name="helpInfo" class="opacity-off">
      <i>{$admin_description}</i>
    </div>
    {/if}
  </div>
  {$container_content}
</div>
{*</div>*}
<div class="cmsunit01"></div> <!-- *FIXFME* why does this prevent overflow? -->
<div class="clear"></div>
