{config_load file="/mod/categorymanager/cfg/language_nl.yaml"}

{* lets check for interesting $_POST or $_GET vars *}
{call mod="categorymanager" function="processActions"}

{if isset($_GET.succes_sort)    ||
    isset($_GET.succes_add )    ||
    isset($_GET.succes_delete)      }     {uinotify type="succes"  content="OK" duration=1000} {/if}
{if isset($_GET.error_sort)         }     {uinotify type="error"   content=`#error_sort#`}     {/if}
{if isset($_GET.error_delete)       }     {uinotify type="error"   content=`#error_delete#`}   {/if}

{uicontainer    type="normal" description=`#module_description#` hidesubmit=true title=`#module_title#` }
  {widget mod="categorymanager" name="tree"}
  <br>
  {if $categorymanager.cfg.system.path_max_depth > 1 }
    {widget mod="categorymanager" name="treePath" id="path" class="cmsunit08"}
  {else}
    <div class="cmsunit08 left" style="height:10px"></div>
  {/if}
  <input type="text" id="title" name="title" class="cmsunit08"/>
  <div class="clear"></div>
  {if $categorymanager.cfg.system.path_max_depth > 1 }
    <button class="action" onclick="var title = $sutra('title').value; if( title.length ) window.ajax.doRequest( '/categorymanager/backend?action=add&parent_id=' + $sutra('path').options[ $sutra('path').selectedIndex ].value + '&title=' + title, false, 'GET', 'popupContent' );" style="margin-right:13px; width:149px">{#add#}</button>
  {else}
    <button class="action" onclick="var title = $sutra('title').value; if( title.length ) window.ajax.doRequest( '/categorymanager/backend?action=add&parent_id={$categorymanager.cfg.system.default_parent_id}&title=' + title, false, 'GET', 'popupContent' );" style="float:right; margin-right:13px; width:163px">{#add#}</button>
  {/if}
{/uicontainer}
