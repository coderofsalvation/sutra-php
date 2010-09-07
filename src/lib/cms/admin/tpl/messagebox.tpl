{if strlen( html_entity_decode( $admin_content ) ) > 45 }<br><br>{/if}
<div class="message" id="adminMessage">
  <img class="messageIcon" src="lib/cms/admin/tpl/gfx/message.{$admin_type}.gif">
  <div class="messageContent">{$admin_content}</div>
</div>
{if isset( $admin_duration ) }
<script type="text/javascript">
  setTimeout( "opacity( 'adminMessage', 100, 0, 1500 )", {$admin_duration} );
</script>
{/if}

