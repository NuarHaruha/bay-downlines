<?php
function get_downline_by_id($id)
{   global $wpdb;

    $table          = $wpdb->usermeta;
    $meta_value     = get_metadata('user', $id, 'account_id', true); 
    $meta_key       = 'id_penaja';
    
    $sql = "SELECT `user_id` AS id FROM $table WHERE `meta_key`=%s AND `meta_value`=%s";
    
    $results = $wpdb->get_results($wpdb->prepare($sql,$meta_key, $meta_value));    
       
    return ($results) ? $results : 0;
}

	
function findall_usertype($type='user_type_option_ahli'){
    global $wpdb;
    
    $table      = $wpdb->usermeta;    
    $meta_key   = $type;
    $meta_value = 'on';
    
        switch ($type){
            case 'stockist':
            case 'stokis':
                $meta_key = 'user_type_option_stokis';
            break;
            case 'staff':
                $meta_key = 'user_type_option_staff';
            break;
            case 'ahli':
            case 'members':
            default:
                $meta_key = 'user_type_option_ahli';
            break;
        }
    
    $sql = "SELECT `user_id` as id FROM $table WHERE `meta_key`=%s AND `meta_value`=%s ORDER BY `user_id` ASC";
    
    return $wpdb->get_results($wpdb->prepare($sql, $meta_key, $meta_value));
}

function get_total_downline($id)
{   global $wpdb;

    $table          = $wpdb->usermeta;
    $meta_value     = get_metadata('user', $id, 'account_id', true); 
    $meta_key       = 'id_penaja';
    
    $sql = "SELECT COUNT(`user_id`) AS total FROM $table WHERE `meta_key`=%s AND `meta_value`=%s";
    
    $results = $wpdb->get_results($wpdb->prepare($sql,$meta_key, $meta_value));    
       
    return ($results) ? $results[0]->total : 0;
}


function get_total_spend($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    
    $sql    = "SELECT SUM(`total_amount`) AS total FROM $table WHERE `ordered_by`=%d";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id));
    
    return ((empty($result)) ? 0 : $result);    
}

function get_total_pending($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    $status = 'pending';
    
    $sql    = "SELECT SUM(`total_amount`) AS total FROM $table WHERE `ordered_by`=%d AND `order_status`=%s";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id,$status));
    
    return ((empty($result)) ? 0 : $result);    
}

function get_total_approved($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    $status = 'approved';
    
    $sql    = "SELECT SUM(`total_amount`) AS total FROM $table WHERE `ordered_by`=%d AND `order_status`=%s";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id,$status));
    
    return ((empty($result)) ? 0 : $result);    
}

function get_last_purchased_date($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    
    $sql    = "SELECT `created_date` AS date FROM $table WHERE `ordered_by`=%d ORDER BY `created_date` DESC LIMIT 1";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id));
    
    return ((empty($result)) ? 0 : $result);    
}

function get_last_purchased_status($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    
    $sql    = "SELECT `order_status` AS status FROM $table WHERE `ordered_by`=%d ORDER BY `created_date` DESC LIMIT 1";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id));
    
    return ((empty($result)) ? 0 : $result);    
}

function get_last_purchased_amount($id)
{   global $wpdb;

    $id     = (int) $id;
    $table  = $wpdb->prefix.mc_products_sales::DB_TABLE_INVOICE;
    
    $sql    = "SELECT `total_amount` AS amount FROM $table WHERE `ordered_by`=%d ORDER BY `created_date` DESC LIMIT 1";
    
    $result = $wpdb->get_var($wpdb->prepare($sql,$id));
    
    return ((empty($result)) ? 0 : $result);    
}