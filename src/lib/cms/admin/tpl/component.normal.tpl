  <table class="{if $small}cmsunit13{else}cmsunit17{/if} {if $admin_filter}hide{/if}" {if $admin_filter}name="{$admin_filter}"{/if}>
    <tr>
      <td class="cmsunit09">
        {if $admin_help}<a href="#" title="{$admin_help}"><img src="/lib/cms/admin/tpl/gfx/icon.help.gif" name="help" class="hide"></a>{/if}
        <label for="{$admin_label_id}">{$admin_label}</label>
      </td>
      <td>
        {$component_content}
      </td>
    </tr>
  </table>
