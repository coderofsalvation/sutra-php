  <link rel="stylesheet" href='/lib/core/datagrid/tpl/css/style.css' type="text/css" media="screen, projection">
	{if $pagination}
		<div class="paginationTitle curved">Pages :</div>
    {if $paginationCurrent != 1}
    <a href="{$paginationUrl}?page=1{if isset($search)}&search={$search}{/if}" class="ajax" rel="popupContent"><div class="pagination curved {if $index	== $paginationCurrent}current{/if}">&lt;</div></a>
    {/if}
		{foreach from=$pagination item="index"}
      <a href="{$paginationUrl}?page={$index}{if isset($search)}&search={$search}{/if}" class="ajax" rel="popupContent"><div class="pagination curved {if $index	== $paginationCurrent}current{/if}">{$index}</div></a>
		{/foreach}
    {if $paginationCurrent < $paginationCount}
    <a href="{$paginationUrl}?page={$paginationCount}{if isset($search)}&search={$search}{/if}" class="ajax" rel="popupContent"><div class="pagination curved {if $index	== $paginationCurrent}current{/if}">&gt;</div></a>
    {/if}
	{/if}
  {if $searchUrl}
  <div class="span-04 right last" style="display:none;" name="zoeken">
    <input type="submit" value="&gt;" class="right span-01 last" onclick="window.ajax.doRequest( baseurl+String('{$searchUrl}').substr(1),'search='+$sutra('search').value,'POST','popupContent')"/>
    <input type="text" name="search" class="right span-03 last" id="search" style="display:inline" value="Search here.." onclick="if( this.value == 'Search here..') this.value = '';"/>
  </div>
  {/if}
  {if $pagination || $searchUrl}
		<div class="clear"></div>
		<br>
  {/if}
  <div class="datagrid margin-bottom">
		<div class="header">
			{setmarkers var="columns" value=$columns}
			{foreach from=$columns item="column" name="columns"}
				<div name="id" class="cmsunit{$column.width} th divider{if isset($column.first)}-first{elseif isset($column.last)}-last{/if}" style="{if isset($column.align)}text-align: {$column.align}{/if}">{$column.name}</div>
			{/foreach}
		</div>
    <div class="clear"></div>
    <div class="body">
		{foreach from=$data item="row"}
			<div class="tr {cycle values="odd,even"} ">
				{foreach from=$columns item="column" name="columns"}
          {if $column.autoclick}<a name="id" href="{$clickUrl}?id={$row|getvar:"id"}" class="ajax" rel="popupContent">{/if}
            <div class="cmsunit{$column.width} td divider{if isset($column.first)}-first{elseif isset($column.last)}-last{/if}" style="{if isset($column.align)}text-align: {$column.align}{/if}">{columnvalue dataProvider=$row column=$column}</div>
          {if $column.autoclick}</a>{/if}
				{/foreach}
			</div>
			<div class="clear"></div>
		{/foreach}
    {if count($data) == 0 }<br>{#noresult#}<br>{/if}
    <div class="clear"></div>
  </div> 
