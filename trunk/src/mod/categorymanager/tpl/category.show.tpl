{config_load file="/mod/categorymanager/cfg/language_nl.yaml"}
{if count($category.pages) == 0}
  {#category_no_content#}
{else}
  {foreach from=$category.pages item="page"}
    {if $page.visible}
    <div class="bloglatest">
      <a href="{$page.title_url_path}">
        <b class="green">{$page.title}</b>&nbsp;
        {if isset($page.yaml.content_1)}
          {$page.yaml.content_1|strip_tags|truncate:300}
        {else}
          {#article_no_content#}
        {/if}
        <br>
      </a>
    </div>
    <div align="right" class="grey bloginfo">
      {if $page.category_obj.title}
        {#posted_in#} {#category#} {$page.category_obj.title}<br>
      {else}
        {#posted_in_deleted_category#}
      {/if}
      {$date|ucfirst}
      <br><BR>
    </div>
    {/if}
  {/foreach}
{/if}
