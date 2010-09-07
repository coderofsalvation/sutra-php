{config_load file="/mod/commentmanager/cfg/language_nl.yaml"}
{if isset( $delete_succes ) && $delete_succes }  {uinotify type="succes" content="OK" duration=1000} {/if}
{if isset( $delete_succes ) && !$delete_succes}  {uinotify type="error"  content=`#error_delete#` duration=1000} {/if}

{uicontainer type="big" description=`#module_description#` hidesubmit=true title=`#module_title#` }

	{column name="Author"   width=4 truncate="10" var="author"}
  {column name="Email"    width=4 truncate="10" var="email"}
  {column name="Website"  width=6 truncate="17" var="website"}
  {column name="Date"     width=4 truncate="10" var="date"}
  {column name="Page"     width=4 truncate="10" var="@page.title_menu"}
  {column name="Options"  width=4 tpl="/mod/commentmanager/tpl/datagrid.options.tpl" vars="id|email" align="center"}
	{pagination itemsPerPage=10 currentPage=$_GET.page url="/commentmanager/backend"}
  {datagrid mod="commentmanager" function="loadComments" clickUrl="/commentmanager/detail"}

{/uicontainer}
