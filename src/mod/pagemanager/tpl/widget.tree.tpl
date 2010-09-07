{*
 *  this calls the tree lib ( which implements this smarty tag )
 *  
 *   - $data is assigned by the widget handler
 *   - if ajaxTarget is defined, then clicks will be rerouted to that particular divname 
 *}
<script type="text/javascript">
  {literal}
  window.clickPage  = function( url )             { if( confirm( "{/literal}{#reload_page_sure#}{literal}" ) ) ajax.doRequest( url + "?snippet=site", false, "GET", "site" ); admin.closePopup(); }
  window.deletePage = function( id  )             { if( confirm( "{/literal}{#delete_page_sure#}{literal}" ) ) ajax.doRequest( 'pagemanager/backend?action=delete&id=' + encodeURI( id ), false, "GET", 'popupContent' ); }
  window.sortPage   = function( direction, id )   { ajax.doRequest( 'pagemanager/backend?action='+direction+'&id='+encodeURI( id ), false, "GET", 'popupContent' ); }
  {/literal}
</script>
{tree data=$data ajaxTarget="popupContent" onClick="clickPage" onDelete="deletePage" onSort="sortPage" parentClick=$pagemanager.cfg.system.parents_clickable }

