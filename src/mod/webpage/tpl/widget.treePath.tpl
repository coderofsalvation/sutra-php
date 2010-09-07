<select name="{$id}" id="{$id}" class="{$class}">
  {foreach from=$treePath item="node"}
    <option {if $node.id == $selected_id}SELECTED="SELECTED"{/if} value="{$node.id}">{$node.title_menu_indent}</option>
  {/foreach}
</select>
