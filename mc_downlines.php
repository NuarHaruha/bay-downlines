<?php
/**
 * @author Nuarharuha 
 */	

class mc_downlines
{
    public $version = '1.0.0';    
    
    public $menu_page;
    
    public $primary_slug = 'mc_downlines';
    
    public $default_cap = 'manage_options';
    
    public $page;
    
    public $request_page;
    
    public $slug;
    
    public $plugin_uri;
    
    public $plugin_path;
    
    // currenct user_id
    public $current_id;
    
    
    public function __construct()
    {
        $this->_init();
    }
    
    private function _init()
    {
        $this->plugin_path = dirname(__FILE__).'/';
        $this->plugin_uri = WP_PLUGIN_URL.'/downlines/';
        
        require $this->plugin_path.'libs/mc_query.php';
        
        if (is_admin()) $this->_initAdmin();
    }
    
    private function _initAdmin()
    {
        $this->_load_primary_tablelist();
        add_action('admin_init', array(&$this, 'admin_stylsheets'));
        add_action('admin_menu', array(&$this,'register_admin_menu'));
        
        
    }
    
    private function _load_primary_tablelist()
    {
        require $this->plugin_path.'table_mc_downlines.php';
    }
    
    public function register_admin_menu()
    {
        $title  = 'Downlines';
        $icon   = 'downlines-16.png';
        $position = 60;
        
        $this->menu_page = add_menu_page($title, $title, $this->default_cap, $this->primary_slug, 
                                    array(&$this,'admin_panel'), $this->get_image($icon), $position);  
                                    
        add_action( 'admin_print_styles-' . $this->menu_page, array(&$this,'admin_styles') );                                    
    }    

    public function admin_stylsheets()
    {
        wp_register_style( 'mc_downlines_stylesheet', plugins_url('/libs/stylesheet.css', __FILE__) );
    }
    
    public function admin_styles() {
       wp_enqueue_style( 'mc_downlines_stylesheet' );
   }     
    
    public function admin_panel()
    {   
        $this->request_page = $_REQUEST['page'];
        
        // file exists is expensive;
        require $this->plugin_path.'panel_'.$this->request_page.'.php';
        //new dBug($this);
    }
    
    public function get_image($img)
    {
        return $this->plugin_uri.'/img/'.$img;
    }
    
    public function get_ahli()
    {
        $ahli = findall_usertype('ahli');
        
        $users = array();
        
        if (has_count($ahli)):            
            foreach($ahli as $index=>$user):
                
                $this->current_id = $user->id;
  
                                            
                $users[] = array('id'           => $this->current_id,
                                'code'          => $this->info('code'),
                                'name'          => $this->info('name'),
                                'stockist_id'   => $this->info('stockist_id'),
                                'parent_id'     => $this->info('parent_id'),
                                'parent_code'   => $this->info('parent_code'),
                                'created_date'  => $this->info('register_date'),
                                'downlines'     => get_total_downline($this->current_id),
                                'last_purchased_date' => get_last_purchased_date($this->current_id),
                                'total_spend'   => get_total_spend($this->current_id),
                                'total_pending' => get_total_pending($this->current_id),
                                'net_total'     => get_total_approved($this->current_id)
                    );
            endforeach; // ahli.id
        endif; // has_count
        
        return $users;
    }
    
    public function get_downline_by_id($id)
    {  
        $users = get_downline_by_id($id);
        
        return $users;
    }
    
    public function info($type){
        
        $result = mc_get_userinfo($this->current_id, $type);
            
            switch($type){
                case 'stockist_id':
                    $result = (empty($result)) ? 'N/A': $result;
                break;
            }
            
        
        return $result;
        
    }
}

$downlines = new mc_downlines();