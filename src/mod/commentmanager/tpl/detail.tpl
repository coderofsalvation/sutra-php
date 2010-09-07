{loadcomment}
{uicontainer    type="normal" title="View comment" description=`#detail_description#`}
<script type="text/javascript" src='/mod/commentmanager/tpl/js/commentmanager.js' ></script>
	<table>
		<tr>
			<td class="cmsunit05" align="right"><b>Gereageerd op pagina:</b></td>
			<td class="padding-left padding-right">
				<div class="curved border padding-left padding-right">
					<a href="{$comment|getvar:"page"|getvar:"title_url_path"}?snippet=site" class="ajax" rel="site">{$comment|getvar:"page"|getvar:"title"}</a>

				</div>
			</td>
		</tr>
		<tr>
			<td class="cmsunit05" align="right"><b>Author:</b></td>
			<td class="padding-left padding-right">
				<div id="author" class="curved border padding-left padding-right" onclick="makeEditable('author',{$comment|getvar:"id"})">
					{$comment|getvar:"author"}
				</div>
		  </td>
	  </tr>
		<tr>
			<td class="cmsunit05" align="right"><b>Date:</b></td>
			<td class="padding-left padding-right">
				<div id="date" class="curved border padding-left padding-right" onclick="makeEditable('date',{$comment|getvar:"id"});">
					{$comment|getvar:"date"|date:"d-m-Y"}
			  </div>
		  </td>
		</tr>
		<tr>
			<td class="cmsunit05" align="right"><b>Email:</b></td>
			<td class="padding-left padding-right" style="overflow:none">
				<div id="email" class="curved border padding-left padding-right" onclick="makeEditable('email',{$comment|getvar:"id"});">
				{$comment|getvar:"email"}
				</div>
		  </td>
		</tr>
		<tr>
			<td class="cmsunit05" align="right"><b>Website:</b></td>
			<td class="padding-left padding-right">
				<div class="curved border padding-left padding-right">
					<a id="website" href="{$comment|getvar:"website"}" target="_blank">{$comment|getvar:"website"}</a>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><b>Bericht:</b></td>
		</tr>
	</table>
	<div id="comment" class="curved border padding" onclick="makeEditable('website',{$comment|getvar:"id"});">
		{$comment|getvar:"html"}
	</div>
	<br>
	<button class="action" onclick="makeEditableAll( Array('date','email','website','comment','author'),{$comment|getvar:"id"});">{#edit#}</button>
  <a class="right" href="/commentmanager/backend?event=DELETE_COMMENT_ITEM&id={$comment|getvar:"id"}" class="ajax" rel="popupContent">
    {#delete#}&nbsp;<img src="/lib/core/datagrid/tpl/gfx/icons/delete.gif"/>
  </a>
  <div class="clear"></div>
{/uicontainer}

