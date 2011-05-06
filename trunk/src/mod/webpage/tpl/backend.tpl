{config_load file="/mod/webpage/cfg/language_nl.yaml"}
{assign_page} 

{if isset( $save_succes ) } {uinotify type="succes" content="OK" duration=1000} {/if}

<form action="/webpage/backend?event=SUTRA_ADMIN_SAVE_CONTENT" method="POST" enctype="multipart/form-data" id="webpage" onsubmit="return false;">
  <input type="hidden" name="use_session" value="true"/>
  {uicontainer    type="normal" title=`#module_title#` description=`#module_description#`}
    {if !$page.locked}
    {uicomponent  type="normal" help=`#help_path#` label=`#path#` label_id="parent_id" }
      {widget mod="webpage" name="treePath" id="parent_id" selected_id=$page.parent_id class="cmsunit08"}                         
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_type#` label=`#type#` label_id="type" filter="advanced" }      
      <select name="type" id="type" class="cmsunit05">
        {foreach from=$webpage.cfg.system.page_types item="type"}
          <option {if $type == $page.type}selected="selected"{/if} value="{$type}">{$type}</option>
        {/foreach}
      </select>
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_tpl#` label="Template" label_id="tpl" filter="advanced" }      
      <select name="tpl_master" id="tpl_master" class="cmsunit05">
        {foreach from=$webpage.cfg.system.page_tpl key="tpl" item="tplname"}
          <option {if $tplname == $page.tpl_master}selected="selected"{/if} value="{$tplname}">{$tpl}</option>
        {/foreach}
      </select>
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_date#` label=`#date#` label_id="date" filter="advanced"}       
      {html_select_date time=$page.date field_order="DMY" field_array="date" prefix="" month_extra="style='width:60px'" start_year="-2" end_year="+1"}
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_visible#` label=`#visible#` label_id="visible" filter="advanced"}       
      <input type="checkbox" id="visible" name="visible" {if $page.visible || $page.visible == NULL}checked="checked"{/if}/>
    {/uicomponent}
    {/if}
    {uicomponent  type="normal" help=`#help_title#` label=`#title#` label_id="title" }       
      <input type="text" id="title" name="title" class="required" value="{$page.title}" onkeyup="if( !$sutra('title_url').disabled ) $sutra('title_url').value = $hyphenate(this.value); $sutra('title_menu').value = this.value"/>         
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_title_menu#` label=`#title_menu#` label_id="title" }       
      <input type="text" id="title_menu" name="title_menu" class="required" value="{$page.title_menu}"/>
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_title_url#` label=`#title_url#` label_id="title_url" }       
      <input type="text" id="title_url" name="title_url"   value="{$page.title_url}" onkeyup="this.value = $hyphenate( this.value );" {if $webpage.cfg.general.default_url == $page.title_url}disabled="disabled"{/if}/>     
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_meta_keywords#` label=`#meta_keywords#` label_id="meta_keywords"}
      <input type="text" id="meta_keywords" name="meta_keywords"       value="{$page.meta_keywords}"/>
    {/uicomponent}
    {uicomponent  type="normal" help=`#help_meta_description#` label=`#meta_description#` label_id="meta_description"}       
      <input type="text" id="meta_description" name="meta_description"       value="{$page.meta_description}"/>         
    {/uicomponent}

    {* HERE THE CUSTOM HTML WILL BE INSERTED (see SUTRA_TPL_FETCH event & /custom directory) *}
    {foreach from=$custom item="tpl"}
      {include file=$tpl}
    {/foreach}

    {uicomponent type="submit" formId="webpage" refresh_page=true refresh_page_id="title_url" } {#save#} {/uicomponent}
  {/uicontainer}

</form>
