# <?die()?>
#######################################################################################################################
# YAML configuration file
#
# NOTE #1: vars starting with '_' are only visible for developers and are removed in PHP
general:
  default_url: /home
  default_meta_description: Enter default meta description here
  default_meta_keywords: Enter default meta keywords here

system:
  name: Pagina
  info: Pagina eigenschappen bewerken
  autocreate: false
  shortname: page
  icon: tpl/gfx/icon.gif
  icon_hide: false
  weight: 1
  page_types:
    - normal
    - url
    - iframe
    - block
  page_tpl_default: index.tpl
  page_tpl:
    standaard: index.tpl
    homepage: index.home.tpl
  #####################################################################################################################
  # here are the pages listed which are basically template files which use widgets 
  # NOTE: for easily creating pages which are NOT content managable ( like admin pages )
  #       urls are in format : http://yoursite.com/webpage/yourpagename
  #       for front-end pages, please create a page in the admin content editor (better SEO).
  pages:
    - name: backend
      file: tpl/backend.tpl
      permissions: SUTRA_VIEW_PANEL,SUTRA_MOD_WRITE,SUTRA_MOD_PAGE_EDIT

  #####################################################################################################################
  # here are the widgets listed, which can be used from within any template          
  # /mod/webpage/class.widget.php will contain the implementation of the functions  
  # NOTE #1: the 'permissions' field is a commaseparated list of ACL permissions ( see /data/cfg.yaml.php )      
  # NOTE #2: if the 'permissions' field is absent, anonymous people can retrieve widgets by url requests!
  widgets:
    - name: treePath
      function: treePath
      permissions: SUTRA_VIEW_WIDGET
  
  #####################################################################################################################
  # here are the events defined, to which this module will listen.
  # /mod/webpage/class.events.php will handle the events at the given function               
  listenEvents:
    - event: SUTRA_READY
      function: getPage
    - event: SUTRA_ADMIN_SAVE_CONTENT
      function: savePage
