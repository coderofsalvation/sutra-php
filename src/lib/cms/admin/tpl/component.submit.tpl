  <table class="{if $small}cmsunit13{else}cmsunit17{/if}">
    <tr>
      <td class="cmsunit09">&nbsp;</td>
      <td>
        <button class="action curved" onclick="if( window.$validateForm( '{$admin_formId}' ) ) window.ajax.doRequest( $sutra( '{$admin_formId}' ).action.replace('http://'+document.location.hostname,''), $formToString( '{$admin_formId}' ), $sutra( '{$admin_formId}' ).method, 'popupContent' );"/>
          {$component_content}
        </button>
      </td>
    </tr>
  </table>
