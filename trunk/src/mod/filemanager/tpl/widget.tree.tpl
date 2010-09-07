{*
 *  this calls the tree lib ( which implements this smarty tag )
 *  
 *   - $data is assigned by the widget handler
 *   - if ajaxTarget is defined, then clicks will be rerouted to that particular divname 
 *}
<script type="text/javascript">
  {literal}
  window.hideTooltip  = function( id  )             { tooltip.toolTip(); }
  window.showTooltip  = function( id  )             { if( $checkExtension( id, ['jpg','gif','png'] ) ) tooltip.toolTip( "<img src="+id+" width='270'/>" ); }
  window.deleteFile   = function( id  )             { if( confirm( "{/literal}{#delete_page_sure#}{literal}" ) ) ajax.doRequest( 'filemanager/backend?action=delete&id=' + encodeURI( id ), false, "GET", 'popupContent' ); }
  {/literal}
</script>
{if isset( $_GET.setOnClick )}{assign var="onClick" value=$_GET.setOnClick}{/if}
{tree data=$data ajaxTarget="popupContent" onDelete="deleteFile" onMouseOver="showTooltip" onMouseOut="hideTooltip" onClick=$onClick rootUrl=$rootUrl target="_blank"}
