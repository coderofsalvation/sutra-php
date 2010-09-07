{config_load file="/mod/pagemanager/cfg/language_nl.yaml"}
{config_load file="/mod/pagemanager/cfg/language_nl.yaml"}

{* lets check for interesting $_POST or $_GET vars *}
{call mod="pagemanager" function="processActions"}

{if isset($_GET.succes_sort)    ||
    isset($_GET.succes_add )    ||
    isset($_GET.succes_delete)      }     {uinotify type="succes"  content="OK" duration=1000} {/if}
{if isset($_GET.error_sort)         }     {uinotify type="error"   content=`#error_sort#`}     {/if}
{if isset($_GET.error_delete)       }     {uinotify type="error"   content=`#error_delete#`}   {/if}
{if isset($_GET.error_locked)       }     {uinotify type="error"   content=`#error_locked#`}   {/if}

{uicontainer    type="normal" description=`#module_description#` hidesubmit=true title=`#module_title#` }
  {widget mod="pagemanager" name="tree"}
  <br>
  {widget mod="webpage" name="treePath" id="path" class="cmsunit08"}

  <input type="text" id="title" name="title" class="cmsunit08"/>
  <div class="clear"></div>
  <button class="action" onclick="var title = $sutra('title').value; if( title.length ) window.ajax.doRequest( '/pagemanager/backend?action=add&parent_id=' + $sutra('path').options[ $sutra('path').selectedIndex ].value + '&title=' + title, false, 'GET', 'popupContent' );" style="margin-right:13px; width:149px">{#add#}</button>
{/uicontainer}
