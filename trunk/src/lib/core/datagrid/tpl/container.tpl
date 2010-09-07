  <link rel="stylesheet" href='/lib/core/datagrid/tpl/css/style.css' type="text/css" media="screen, projection">
	{if $pagination}
		<div class="paginationTitle curved">Pages :</div>
		{foreach from=$pagination item="index"}
      <a href="{$paginationUrl}?page={$index}" class="ajax" rel="popupContent"><div class="pagination curved {if $index	== $paginationCurrent}current{/if}">{$index}</div></a>
		{/foreach}
		<div class="clear"></div>
		<br>
	{/if}
  <div class="datagrid curved margin-bottom">
		<div class="header">
			{setmarkers var="columns" value=$columns}
			{foreach from=$columns item="column" name="columns"}
				<div class="cmsunit{$column.width} th divider{if isset($column.first)}-first{elseif isset($column.last)}-last{/if}">{$column.name}</div>
			{/foreach}
		</div>
    <div class="clear"></div>
    <div class="body">
		{foreach from=$data item="row"}
			<a href="{$clickUrl}?id={$row|getvar:"id"}" class="ajax" rel="popupContent">
			<div class="tr {cycle values="odd,even"} ">
				{foreach from=$columns item="column" name="columns"}
					<div class="cmsunit{$column.width} td divider{if isset($column.first)}-first{elseif isset($column.last)}-last{/if}" style="{if isset($column.align)}text-align: {$column.align}{/if}">{columnvalue dataProvider=$row column=$column}</div>
				{/foreach}
			</div>
			</a>
			<div class="clear"></div>
		{/foreach}
    <div class="clear"></div>
  </div> 
