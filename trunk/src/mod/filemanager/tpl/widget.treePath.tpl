<select name="{$id}" id="{$id}" class="{$class}">
  {foreach from=$treePath item="node"}
    <option value="{$node.path}">{$node.name_short_indent}</option>
  {/foreach}
</select>
