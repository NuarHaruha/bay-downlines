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

function get_children_count($id){
    return get_total_downline($id);
}

/**
 *  lv2
 */
function get_grand_children_count($uid){
    $gchild = get_grand_children($uid);

    $count = 0;

    if (!empty($gchild)){
        foreach($gchild as $id=> $users){

            if (is_array($users)){
                foreach($users as $i){
                    $count++;
                }
            }
        }
    }

    return $count;
}
function get_grand_children_approved_sales($uid){
    $gchild = get_grand_children($uid);

    $sales = 0;

    if (!empty($gchild)){
        foreach($gchild as $id=> $users){

            if (is_array($users)){
                foreach($users as $i){
                    $id = (int) $i;

                    $invoices = get_approved_invoice_id($id);

                    // check the invoice items
                    if ($invoices){
                        foreach($invoices as $invoice){
                            $sales++;
                        }
                    }
                }
            }
        }
    }

    return $sales;
}

function get_grand_children_sales($uid){
    $gchild = get_grand_children($uid);

    $sales = 0;

    if (!empty($gchild)){
        foreach($gchild as $id=> $users){

            if (is_array($users)){
                foreach($users as $i){
                    $id = (int) $i;

                    $invoices = get_all_invoice_id($id);

                    // check the invoice items
                    if ($invoices){
                        foreach($invoices as $invoice){
                            $sales++;
                        }
                    }
                }
            }
        }
    }

    return $sales;
}


function get_grand_children_pv($uid){
    $gchild = get_grand_children($uid);

    $pv = 0;

    if (!empty($gchild)){
        foreach($gchild as $id=> $users){

            if (is_array($users)){
                foreach($users as $i){
                    $id = (int) $i;

                    $invoices = get_approved_invoice_id($id);

                    // check the invoice items
                    if ($invoices){
                        foreach($invoices as $invoice){
                            $items = get_invoice_meta($invoice->invoice_id, 'orders', false);

                            // get items pv
                            if ($items){
                                foreach($items as $index => $item){

                                    foreach($item['product_id'] as $pid => $amount){

                                        $meta = get_post_meta( $pid );
                                        $points = (int) $meta['pv'][0];
                                        $unit   = (int) $item['quantity'][$pid];
                                        $points = $points * $unit;
                                        $pv += $points;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $pv;
}

function get_direct_children($uid){
    global $wpdb;

    $db         = $wpdb->usermeta;

    $code       = uinfo($uid,'code');

    $sql = "SELECT user_id id FROM $db WHERE meta_key='id_penaja' AND meta_value=%s";

    $query = $wpdb->prepare($sql,$code);

    return $wpdb->get_results($query);
}

function get_grand_children($uid){

    $children = get_direct_children($uid);

    $g_children = array();

       if ($children){
           foreach($children as $child){
               $e = get_direct_children($child->id);
               if (!empty($e)){
                   $g_children[$child->id] = $e;
               }
           }
       }
    return $g_children;
}

function get_children_pv($uid){
    global $wpdb;

    $db         = $wpdb->usermeta;

    $code       = uinfo($uid,'code');

    $sql = "SELECT user_id id FROM $db WHERE meta_key='id_penaja' AND meta_value=%s";

    $query = $wpdb->prepare($sql,$code);

    $children   = $wpdb->get_results($query);

    $pv = 0;

    if ($children){
        foreach($children as $child){
            $id = $child->id;
            $invoices = get_approved_invoice_id($id);

            // check the invoice items
            if ($invoices){
                foreach($invoices as $invoice){
                    $items = get_invoice_meta($invoice->invoice_id, 'orders', false);

                    // get items pv
                    if ($items){
                        foreach($items as $index => $item){

                            foreach($item['product_id'] as $pid => $amount){

                                $meta = get_post_meta( $pid );
                                $points = (int) $meta['pv'][0];
                                $unit   = (int) $item['quantity'][$pid];
                                $points = $points * $unit;
                                $pv += $points;
                            }
                        }
                    }
                }
            }
        }
    }

    return $pv;
}


function get_children_approved_sales_count($uid){
    global $wpdb;

    $db         = $wpdb->usermeta;

    $code       = uinfo($uid,'code');

    $sql = "SELECT user_id id FROM $db WHERE meta_key='id_penaja' AND meta_value=%s";

    $query = $wpdb->prepare($sql,$code);

    $children   = $wpdb->get_results($query);

    $sales = 0;

    if ($children){
        foreach($children as $child){
            $id = $child->id;
            $invoices = get_approved_invoice_id($id);

            // check the invoice items
            if ($invoices){
                foreach($invoices as $invoice){
                    $sales++;
                }
            }
        }
    }

    return $sales;
}


function get_children_sales_count($uid){
    global $wpdb;

    $db         = $wpdb->usermeta;

    $code       = uinfo($uid,'code');

    $sql = "SELECT user_id id FROM $db WHERE meta_key='id_penaja' AND meta_value=%s";

    $query = $wpdb->prepare($sql,$code);

    $children   = $wpdb->get_results($query);

    $sales = 0;

    if ($children){
        foreach($children as $child){
            $id = $child->id;
            $invoices = get_all_invoice_id($id);

            // check the invoice items
            if ($invoices){
                foreach($invoices as $invoice){
                    $sales++;
                }
            }
        }
    }

    return $sales;
}

function get_approved_invoice_id($uid){
    global $wpdb;

    $db     = $wpdb->base_prefix.mc_products_sales::DB_TABLE_INVOICE;

    $sql    = "SELECT invoice_id FROM $db WHERE ordered_by=%d AND order_status=%s AND created_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()";

    return $wpdb->get_results($wpdb->prepare($sql,$uid,'approved'));
}

function get_all_invoice_id($uid){
    global $wpdb;

    $db     = $wpdb->base_prefix.mc_products_sales::DB_TABLE_INVOICE;

    $sql    = "SELECT invoice_id FROM $db WHERE ordered_by=%d AND created_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()";

    return $wpdb->get_results($wpdb->prepare($sql,$uid));
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