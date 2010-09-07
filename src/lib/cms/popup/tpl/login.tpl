<img src="/lib/cms/popup/tpl/gfx/sutralogo.gif" style="margin-bottom:5px"/>
{*{if isset($_GET.error_delete)       }     {uinotify type="error"   content=`#error_delete#`}   {/if}*}

<form action="admin" method="POST" enctype="multipart/form-data" id="webpage" onsubmit="return false;">
<div name="page" class="cmsunit13 block border curved fill padding margin-bottom">
    {uicomponent  type="normal" label=`#username#` label_id="username" advanced=false}       
      <input type="text" id="username" name="username" class="required cmsunit06">
    {/uicomponent}
    {uicomponent  type="normal" label=`#password#` label_id="password" advanced=false}       
      <input type="password" id="password" name="password" class="required cmsunit06">
    {/uicomponent}
    {uicomponent type="submit" formId="webpage" refresh_page=true refresh_page_id="title_url" } {#login#} {/uicomponent}
</div>
</form>

<div class="clear"></div>

