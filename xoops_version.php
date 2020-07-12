<?php
require_once(XOOPS_ROOT_PATH.'/modules/xstreamer/include/consts.php');

$modversion['name'] = _MI_XSTREAMER_MODULE_NAME;
$modversion['version'] = "0.95";
$modversion['description'] = _MI_XSTREAMER_MODULE_NAME_DESCRIPTION;
$modversion['credits'] = "Panther Software Publishing Limited";
$modversion['author'] = "Nazar Aziz - nazar@panthersoftware.com";
$modversion['help'] = "";
$modversion['license'] = "Copyright Panther Software Publishing Limited 2001-2006.";
$modversion['official'] = 0;
$modversion['image'] = "images/xstreamer_slogo.png";
$modversion['dirname'] = XSTREAMER_MODULE_DIR;

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

//tables
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][] = 'xstreamer_category';
$modversion['tables'][] = 'xstreamer_video';
$modversion['tables'][] = 'xstreamer_vote';

// Menu
$modversion['hasMain'] = 1;
//

// Smarty
$modversion['use_smarty'] = 1;

// Templates
//add blocks go first
$modversion['templates'][1]['file'] = 'xstreamer_admin_video_add.html';
$modversion['templates'][1]['description'] = _MI_XSTREAMER_ADMIN_VIDEO_ADD;
$modversion['templates'][2]['file'] = 'xstreamer_admin_category_add.html';
$modversion['templates'][2]['description'] = _MI_XSTREAMER_ADMIN_CATEGORY_ADD;

//admin templates
$modversion['templates'][30]['file'] = 'xstreamer_admin_video_index.html';
$modversion['templates'][30]['description'] = _MI_XSTREAMER_ADMIN_VIDEO;
$modversion['templates'][31]['file'] = 'xstreamer_admin_category_index.html';
$modversion['templates'][31]['description'] = _MI_XSTREAMER_CATEGORY_VIDEO;
//front end 
$modversion['templates'][60]['file'] = 'xstreamer_player_code.html';
$modversion['templates'][60]['description'] = _MI_XSTREAMER_VIDEO_PLAYER;
$modversion['templates'][61]['file'] = 'xstreamer_voting_bar.html';
$modversion['templates'][61]['description'] = _MI_XSTREAMER_VOTING_BAR;
$modversion['templates'][62]['file'] = 'xstreamer_video_index.html';
$modversion['templates'][62]['description'] = _MI_XSTREAMER_VIDEO_INDEX;

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['pageName'] = 'index.php';
$modversion['comments']['itemName'] = 'id';
// Comment callback functions
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'news_com_approve';
$modversion['comments']['callback']['update'] = 'news_com_update';

// Blocks
$i=1;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_CATS;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_CATSD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_categories";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_categories.html';
$i++;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_MOST_VIEWED;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_MOST_VIEWEDD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_most_viewed";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_most_viewed.html';
$i++;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_HIGHEST_RATED;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_HIGHEST_RATEDD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_highest_rated";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_highest_rated.html';
$i++;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_MOST_COMMENT;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_MOST_COMMENTD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_most_commented";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_most_commented.html';
$i++;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_MOST_RATED;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_MOST_RATEDD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_most_rated";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_most_rated.html';
$i++;
$modversion['blocks'][$i]['file'] = "block_functions.php";
$modversion['blocks'][$i]['name'] = _MI_XSTREAMER_B_BEST_VIDEOS;
$modversion['blocks'][$i]['description'] = _MI_XSTREAMER_B_BEST_VIDEOSD;
$modversion['blocks'][$i]['show_func'] = "b_xstreamer_best_videos";
$modversion['blocks'][$i]['template'] = 'xstreamer_block_best_videos.html';

// config
$i=1;
$modversion['config'][$i]['name'] = 'bandwidthThrottle';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_BAND_THROTTLE';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_BAND_THROTTLED';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'bandwidthThrottleValue';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_BAND_THROTTLE_V';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_BAND_THROTTLE_VD';
$modversion['config'][$i]['formtype'] = 'string';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 128;
$i++;
$modversion['config'][$i]['name'] = 'defPlayerWidth';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_PLAYER_HEIGHT';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_PLAYER_HEIGHTD';
$modversion['config'][$i]['formtype'] = 'string';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 450;
$i++;
$modversion['config'][$i]['name'] = 'defPlayerHeight';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_PLAYER_WIDTH';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_PLAYER_WIDTHD';
$modversion['config'][$i]['formtype'] = 'string';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 320;
$i++;
$modversion['config'][$i]['name'] = 'defEnableRating';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_ENABLE_RATING';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_ENABLE_RATINGD';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$i++;
$modversion['config'][$i]['name'] = 'defEnableComment';
$modversion['config'][$i]['title'] = '_MI_XSTREAMER_ENABLE_COMMENT';
$modversion['config'][$i]['description'] = '_MI_XSTREAMER_ENABLE_COMMENTD';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
//$i++;
//$modversion['config'][$i]['name'] = 'catAsSubMenu';
//$modversion['config'][$i]['title'] = '_MI_XSTREAMER_CAT_SUBMENU';
//$modversion['config'][$i]['description'] = '_MI_XSTREAMER_CAT_SUBMENUD';
//$modversion['config'][$i]['formtype'] = 'yesno';
//$modversion['config'][$i]['valuetype'] = 'int';
//$modversion['config'][$i]['default'] = 1;

//notification
$modversion['hasNotification'] = 0;

?>
