{inc file="/tpl/front/css/reset.css"}
{inc file="/tpl/front/css/grid.css"}
{inc file="/tpl/front/css/style.css"}

{inc file="/tpl/front/js/typeface-0.14.js"}
{inc file="/tpl/front/js/typeface.myriad_web_bold.js"}
{inline language="js"}ajax.addCallback( _typeface_js.renderDocument );{/inline}

{$panel}
{$popup}

<div id="wallpaper">
  <div>
    <img id="wallpaper" src="/tpl/front/gfx/wallpaper.jpg">
  </div>
</div>
<!--[if lte IE 6]><style type="text/css">img#wallpaper { width:100%; height:100% }</style><![endif]-->

<center><!-- center IE 5.5 / Mobile compatibility -->
  <div id="site">
    <!-- you can request the snippet content by calling http://{$_SERVER.HTTP_HOST}/{#rootdir#}/?snippet=site -->
    <!-- very usable for AJAX -->
    {snippet name="site"}
    <div id="menu"></div> 
    <div id="header"  class="span-19 curved last"></div>  
    <div id="breadcrumb" class="span-19 last">
      <a href="/">Home</a>&nbsp;&gt;&nbsp;<a href="">yourpage</a>  
    </div>
    <div id="content" class="span-19 curved last">
      <h1 class="padding typeface-js">{$page.title|ucfirst}</h1>
      <div class="clear"></div>
      <div class="border span-10 margin-left left padding editable" id="content_1">{$page.yaml.content_1}</div>
      <div class="border span-06 margin-right left last padding editable" id="content_2">{$page.yaml.content_2}</div>
      <div class="clear"></div>
      <div class="border margin padding editable" id="content_3">{$page.yaml.content_3}</div>
      <div class="clear"></div>
      <div class="border span-06 margin-left left padding editable" id="widget_1"></div>
      <div class="border span-10 margin-right left last padding editable" id="content_4">{$page.yaml.content_4}</div>
      <div class="clear"></div>
      <div class="border margin padding" id="content_5"></div>
      <div class="margin">{widget file="custom/widget/exampleWidget.php"}</div>
    </div>
    <div class="editable padding margin-bottom" id="footer">{$page.footer} footercontent here (C) </div>
    {/snippet}
  </div>
</center>
