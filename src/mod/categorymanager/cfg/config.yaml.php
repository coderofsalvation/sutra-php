# <?die()?>
#####################################################################################################################
# YAML configuration file
#
# NOTE #1: vars starting with '_' are only visible for developers and are removed in PHP
general:

system:
  name: Categorie manager module
  info: Hier kunt u categorieen toevoegen, verwijderen en sorteren
  shortname: categorymanager
  weight:10
  icon: tpl/gfx/icon.gif
  icon_hide:true
  default_parent_id: 1
  autocreate: true
  path_max_depth: 1

  #####################################################################################################################
  # here are the pages listed which are basically template files which use widgets 
  # NOTE: for easily creating pages which are NOT content managable ( like admin pages )
  #       urls are in format : http://yoursite.com/categorymanager/yourpagename
  #       for front-end pages, please create a page in the admin content editor (better SEO).
  pages:
    - name: backend
      file: tpl/backend.tpl
    - name: admin
      file: tpl/backend.tpl

  #####################################################################################################################
  # here are the widgets listed, which can be used from within any template          
  # /mod/webpage/class.widget.php will contain the implementation of the functions  
  # NOTE #1: the 'permissions' field is a commaseparated list of ACL permissions ( see /data/cfg.yaml.php )      
  # NOTE #2: if the 'permissions' field is absent, anonymous people can retrieve widgets by url requests!
  widgets:
    - name: tree
      function: tree
      permissions: SUTRA_VIEW_WIDGET
  
  #####################################################################################################################
  # here are the events defined, to which this module will listen.
  # /mod/webpage/class.events.php will handle the events at the given function               
  listenEvents:
    - event: SUTRA_PAGE_GET
      function: getPage
