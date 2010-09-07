{inc file="/lib/cms/admin/tpl/css/cms.css"}
{inc file="/lib/cms/admin/tpl/css/grid.css"}
{inc file="/lib/cms/admin/tpl/js/panel.js"}
{config_load file="lib/cms/admin/tpl/language/nl.yaml"}
<div>
  <center> <!-- center tag for IE 5.5 & 6.0/Mobile compatibility -->
    <table id="popup">
      {snippet name="popup"}
      <tr>
        <td>
          <div id="popupPage" class="curved {if !isset($csshide)}opacity-off{/if}" style="{if isset($csshide)}display:none{/if}{if isset($small)}margin-top:115px;margin-left:-166px;height:238px;width:342px{/if}" onmouseover="admin.show( 'popupClose', true, false )" onmouseout=" admin.show( 'popupClose', false, false )">
            <div id='popupClose' class="curved" onclick="admin.show( 'popupContent', true, true );admin.show( 'popupPage', false, false );" {if isset($small)}style="left:302px;"{/if}></div>
            <div id="popupContent" {if isset($small)}style="height:178px"{/if}>
              {$popupContent}
              <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <img class="shadow" src="/lib/cms/admin/tpl/gfx/dropshadowbar.png" {if isset($small)}style="width:325px;margin-left:0px"{/if}/>
          </div>
        </td>
      </tr>
      {/snippet}
    </table>
  </center>
</div>

<script type="text/javascript">
  // fadein panel
  {literal}
  Event.domReady.add( function(){
    shiftOpacity( 'popupPage', 500 );
  });
  {/literal}
</script>
