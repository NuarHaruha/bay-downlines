<?php

/**
 * @package helper
 */
/*
Plugin Name: MDAG Downlines
Plugin URI: http://mdag.my
Description: Helper filter functions, for downlines 
Version: 1.0.0
Author: Nuar, MDAG Consultancy
Author URI: http://mdag.my
License: MIT License
License URI: http://mdag.mit-license.org/
*/

function mdag_get_penaja_id($user_id){
    return get_user_meta($user_id, 'id_penaja', true);   
}


function mdag_find_downline($level, $user_id){
    
    $ret = false;
    $code = mdag_get_id_ahli($user_id);
    
    switch ($level){
        case 1:
            $ret = mdag_downline_get_level_1($code);
        break;
        case 2:
            $ret = mdag_downline_get_level_2($code);
        break;
    }
    
    return $ret;
}

function mdag_downline_get_level_1($code_ahli){
   global $wpdb;
    
    $table      = $wpdb->prefix.'usermeta';
    
    $query      = "SELECT user_id as downline_id FROM $table WHERE `meta_value`=%s AND `meta_key`=\"id_penaja\"";
    
    $sql        = $wpdb->prepare($query,$code_ahli);
    
    return $wpdb->get_results($sql,ARRAY_A);    
}

function has_downline($code_ahli){
    return ((count(mdag_downline_get_level_1($code_ahli)) >= 0) ? true : false);
}


/**
 * mdag_filter_downlines_content()
 * 
 * @param mixed $content
 * @return
 */
function mdag_filter_downlines_content($content){
    global $current_user, $wp_roles;
    
    get_currentuserinfo();      
    
    $cid = $current_user->id;
    
    $not_available = '<tr><td colspan="2">-- not available --</td></tr>';
    
    // preparpe list for level 1
    $htm = '';
    
    $downlines = mdag_find_downline(1,$current_user->id);
    $cnt = count($downlines);
    
    if ($cnt >= 0){
        for ($i=0;$i<$cnt; $i++){
            $did = $downlines[$i]['downline_id'];
            
            $acc_id = mdag_get_id_ahli($did);
            $acc_name = mdag_get_full_name($did);
            
            $htm .= '<tr><td><small class="label label-info">'.$acc_id. '</small></td><td>' . $acc_name . '</td></tr>'."\n\r";
        }
    }
    
    $not_available = '<tr><td colspan="2">-- not available --</td></tr>';
                
    $metadata = array(
        '%downline_level_1%' => (empty($htm) ? $not_available : $htm ),
        '%downline_level_2%' => $not_available,
        '%downline_level_3%' => $not_available,
        '%downline_level_4%' => $not_available,
        '%downline_level_5%' => $not_available,
        '%downline_level_6%' => $not_available,
        '%downline_level_7%' => $not_available,
        '%downline_level_8%' => $not_available,
    );
            
    return strtr($content, $metadata);
}


//add_filter('the_content','mdag_filter_downline_info');
 
function mdag_filter_downline_info($content){
    
    if (is_page_template('page-downlines.php')){
        
        if (is_page(mdagEnum::SLUG_DOWNLINES)){
            $content = mdag_filter_downlines_content($content);
        }
    }
    
    return $content;
}

require dirname(__FILE__).'/mc_downlines.php';