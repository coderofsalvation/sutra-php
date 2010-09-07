{inc file="/lib/cms/admin/tpl/css/cms.css"}
{inc file="/lib/cms/admin/tpl/css/grid.css"}
{inc file="/lib/cms/admin/tpl/js/panel.js"}
{inc file="/lib/cms/admin/tpl/js/tween.js"}
{inc file="/lib/cms/admin/tpl/js/nicEdit.sutraImage.js"}
{inc file="/lib/cms/admin/tpl/js/nicEdit.config.js"}
{config_load file="lib/cms/admin/tpl/language/nl.yaml"}

<div id="panelDiv">
  {snippet name="panel"}
    {isAllowed permission="SUTRA_VIEW_PANEL"}
      <script type="text/javascript">
        // lets let the admin javascript class use some localized strings
        admin.strings[ 'STATUS_WIDGET_IS_LOADING' ] = "{#STATUS_WIDGET_IS_LOADING#}";
        admin.strings[ 'ALERT_DEV' ]                = "{#LABEL_ICON_DEV#}{#ALERT_DEV#}";
      </script>

      <div id="toolTip" class="toolTip curved"></div>
      <div id="panelContainer" class="-opacity-off">
        <center> <!-- center tag for IE 5.5 & 6.0/Mobile compatibility -->
          <table id="panel">
            <tr>
              <td height="87">
                <div id="panelLeft">
                  <img id="ajax" src="/lib/cms/admin/tpl/gfx/loading-off.gif"/>
                </div>
                <div id="panelMiddle">
                  {foreach from=$panelIcons item="panelIcon"}
                    <a href="{$panelIcon.link}" class="ajax left" rel="popupContent" onclick="admin.clickModule('{$panelIcon.info} ({#STATUS_IS_LOADING#})'); return false"><img src="{$panelIcon.icon}" alt="{$panelIcon.name}" title="{$panelIcon.info}" onmouseover="admin.setStatus('{$panelIcon.info}')" border="0"/></a>
                  {/foreach}
                  <a href="#" onclick="admin.close()" class="right"><img id="iconLogout" src="/lib/cms/admin/tpl/gfx/icon.logout.gif" alt="{#LABEL_ICON_LOGOUT#}" title="{#LABEL_ICON_LOGOUT#}" onmouseover="admin.setStatus('{#STATUS_LOGOUT#}')" border="0"/></a>
                  {isAllowed permission="SUTRA_DEVELOPER"}
                    <a href="javascript:admin.toggleDev()" class="right"><img id="iconDev" src="/lib/cms/admin/tpl/gfx/icon.dev-{if $cache}off{else}on{/if}.gif" alt="{#LABEL_ICON_DEV#}" title="{#LABEL_ICON_DEV#}" onmouseover="admin.setStatus('{#STATUS_DEV#}')" border="0"/></a>
                  {/isAllowed}
                  {isAllowed permission="SUTRA_MOD_PAGE_EDIT"}
                  <a href="javascript:admin.toggleEdit()" class="right"><img id="iconEdit" src="/lib/cms/admin/tpl/gfx/icon.edit-off.gif" alt="{#LABEL_ICON_EDIT#}" title="{#LABEL_ICON_EDIT#}" onmouseover="admin.setStatus('{#STATUS_EDIT#}')" border="0"/></a>
                  {/isAllowed}
                </div>
                <div id="panelRight"></div>
                <div class="clear"></div>
                <div id="panelStatus"></div>
                <div id="nicedit"></div>
              </td>
            </tr>
            <tr>
              <td>
                <img class="shadow" src="/lib/cms/admin/tpl/gfx/dropshadowbar.png"/>
              </td>
            </tr>
          </table>
        </center>
        <div class="clear"></div>
      </div>

      {*
       *<script type="text/javascript">
       *  // fadein panel
       *  {literal}
       *  Event.domReady.add( function(){
       *    shiftOpacity( 'panelContainer', 500 );
       *  });
       *  {/literal}
       *</script>
       *}
    {/isAllowed}
  {/snippet}
</div>
