# <?die()?>
######################################################################################################################
# YAML configuration file
#
# NOTE #1: vars starting with '_' are only visible for developers and are removed in PHP
general:

system:
  name: Commentmanager 
  info: Commentmanager module
  shortname: commentmanager
  weight:99
  icon: tpl/gfx/icon.gif
  icon_hide:false
  autocreate: false

  #####################################################################################################################
  # here are the pages listed which are basically template files which use widgets 
  # NOTE: for easily creating pages which are NOT content managable ( like admin pages )
  #       urls are in format : http://yoursite.com/filemanager/yourpagename
  #       for front-end pages, please create a page in the admin content editor (better SEO).
  pages:
    - name: backend
      file: tpl/backend.tpl
    - name: detail
      file: tpl/detail.tpl

  #####################################################################################################################
  # here are the widgets listed, which can be used from within any template          
  # /mod/webpage/class.widget.php will contain the implementation of the functions  
  # NOTE #1: the 'permissions' field is a commaseparated list of ACL permissions ( see /data/cfg.yaml.php )      
  # NOTE #2: if the 'permissions' field is absent, anonymous people can retrieve widgets by url requests!
  widgets:
    - name: testwidget 
      function: testWidget
      permissions: SUTRA_VIEW_WIDGET
  
  #####################################################################################################################
  # here are the events defined, to which this module will listen.
  # /mod/filemanager/class.events.php will handle the events at the given function               
  listenEvents:
    - event: SUTRA_PAGE_SAVE
      function: savePage
    - event: SUTRA_PAGE_GET
      function: getPage
    - event: DELETE_COMMENT_ITEM
      function: deleteComment
    - event: UPDATE_COMMENT_VAR
      function: updateCommentVar
