<?php
  //Properties admin page
  define('_MI_XSTREAMER_MODULE_NAME','xStreamer');
  define('_MI_XSTREAMER_MODULE_NAME_DESCRIPTION','FLV Video Streaming & Showcase Module');
  define('_MI_XSTREAMER_BAND_THROTTLE','Enable Bandwidth Throttle');
  define('_MI_XSTREAMER_BAND_THROTTLED','Limit to the bandwidth dedicated per user. 128KB is a good limit for smooth video playback.');
  define('_MI_XSTREAMER_BAND_THROTTLE_V','Bandwidth Throttling Rate (EXPERIMENTAL)'); 
  define('_MI_XSTREAMER_BAND_THROTTLE_VD','Set the Badnwidth at which FLV Video will be streamed. Value is kilobytes per second per client.');
  define('_MI_XSTREAMER_PLAYER_HEIGHT','Default xStreamer Height');
  define('_MI_XSTREAMER_PLAYER_HEIGHTD','The default height of the xStreamer Video Player'); 
  define('_MI_XSTREAMER_PLAYER_WIDTH','Default xStreamer Width');
  define('_MI_XSTREAMER_PLAYER_WIDTHD','The default width of the xStreamer Video Player'); 
  define('_MI_XSTREAMER_CAT_SUBMENU','Category SubMenus');
  define('_MI_XSTREAMER_CAT_SUBMENUD','Show Categories as Submenu?');
  define('_MI_XSTREAMER_ENABLE_RATING','Enable Video Rating');
  define('_MI_XSTREAMER_ENABLE_RATINGD','Allows visitor to vote on videos');
  define('_MI_XSTREAMER_ENABLE_COMMENT','Enable Video Comments');
  define('_MI_XSTREAMER_ENABLE_COMMENTD','Allows visitors to leave comments on videos');
  //admin menus
  define('_MI_XSTREAMER_ADMIN_TITLE','%s Administrator Menu');
  define('_MI_XSTREAMER_MENU_PREFERENCES','Module Preferences');
  define('_MI_XSTREAMER_UPDATE_MODULE','Update Module');
  
  define('_MI_XSTREAMER_MENU_MANAGE_CATEGORY','Manage Categories');
  define('_MI_XSTREAMER_MENU_MANAGE_VIDEOS','Manage Videos');
  //template names
  define('_MI_XSTREAMER_ADMIN_VIDEO_ADD','Admin - Block - Add Video');
  define('_MI_XSTREAMER_ADMIN_CATEGORY_ADD','Admin - Block - Add Category');
  define('_MI_XSTREAMER_ADMIN_VIDEO','Admin - Video Index');
  define('_MI_XSTREAMER_CATEGORY_VIDEO','Admin - Category Index');
  
  define('_MI_XSTREAMER_VIDEO_INDEX','Video Index Page');
  define('_MI_XSTREAMER_VIDEO_PLAYER','Video Player');
  define('_MI_XSTREAMER_VOTING_BAR','Video Ajax Rating Bar');
  //blocks
  define('_MI_XSTREAMER_B_CATS','Video Categories');
  define('_MI_XSTREAMER_B_CATSD','Shows Video Categories');
  define('_MI_XSTREAMER_B_MOST_VIEWED','Most Viewed Videos');
  define('_MI_XSTREAMER_B_MOST_VIEWEDD','Shows Most Viewed Videos');
  define('_MI_XSTREAMER_B_HIGHEST_RATED','Highest Rated Videos');
  define('_MI_XSTREAMER_B_HIGHEST_RATEDD','Shows Highest Rated Videos');
  define('_MI_XSTREAMER_B_MOST_COMMENT','Most Commented Videos');
  define('_MI_XSTREAMER_B_MOST_COMMENTD','Shows Most Commented Videos');
  define('_MI_XSTREAMER_B_MOST_RATED','Most Rated Videos');
  define('_MI_XSTREAMER_B_MOST_RATEDD','Show Videos with most Rating Votes');
  define('_MI_XSTREAMER_B_BEST_VIDEOS','Best Videos Videos');
  define('_MI_XSTREAMER_B_BEST_VIDEOSD','Show Videos with most votes and highest ratings');
  //controller text
  define('_MI_XSTREAMER_SAVE_VIDEO_SUCCESS','Video Saved');
  define('_MI_XSTREAMER_SAVE_VIDEO_FAIL','Could not save Video');
  define('_MI_XSTREAMER_DELETE_VIDEO_CONFIRM','Are you sure you want to delete this Video?');
  define('_MI_XSTREAMER_SAVE_CAT_SUCCESS','Category Saved');
  define('_MI_XSTREAMER_SAVE_CAT_FAIL','Could not save Category');
  define('_MI_XSTREAMER_DELETE_CAT_CONFIRM','Are you sure you want to delete this Category?<br>Warning:Videos under this category will no longer display. Please ensure there are no videos under this category.');
?>
