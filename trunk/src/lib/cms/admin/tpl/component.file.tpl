  <form target='{$admin_iframeId}' action="{$admin_action}" method="{$admin_method}" enctype='multipart/form-data' onsubmit='return false;'>
  {if !$admin_hide_table}
  <table class="cmsunit17">
    <tr>
      <td class="cmsunit09">&nbsp;</td>
      <td>
  {/if}
        {$component_content}
        <input type="file" id="file" name="file" onchange="$sutra('fileLoading').style.display = 'block';submit()" />
        <img src="/lib/cms/admin/tpl/gfx/loading.gif" id="fileLoading" style="display:none"/>
  {if !$admin_hide_table}
      </td>
    </tr>
  </table>
  {/if}
  </form>
