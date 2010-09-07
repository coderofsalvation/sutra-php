{config_load file="/mod/filemanager/cfg/language_nl.yaml"}

{* lets check for interesting $_POST or $_GET vars *}
{call mod="filemanager" function="processActions"}

{if isset($_GET.succes_add)   ||
    isset($_GET.succes_delete)   }     {uinotify type="succes"  content="OK" duration=1000}  {/if}
{if isset($_GET.error_delete)    }     {uinotify type="error"   content=`#error_delete#`}    {/if}
{if isset($_GET.error_add)       }     {uinotify type="error"   content=`#error_add#`}       {/if}
{if isset($_GET.error_noroot)    }     {uinotify type="error"   content=`#error_noroot#`}    {/if}

{uicontainer    type="normal" description=`#module_description#` hidesubmit=true title=`#module_title#` }
  {widget mod="filemanager" name="tree"}
  <br>
  {uicomponent type="file" method="POST" action="filemanager/backend?event=SUTRA_MOD_FILEMANAGER_SAVE" hide_table=true iframeId="response"}
    <iframe id="response" name="response" marginheight="0" marginheight="0"></iframe>
    {widget mod="filemanager" name="treePath" id="path" class="cmsunit07"}
  {/uicomponent}
{/uicontainer}


{literal}
<style type="text/css">
  div#popupContent form input#file,
  div#popupContent form select#path  { width:111px; float:left }
  div#popupContent form input#upload { margin-left:138px; }
  iframe#response                    { float:left; width:0px; height:0px; padding-top:2px }
</style>
{/literal}
