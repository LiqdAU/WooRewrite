<?php

class WooRewrite {

    public $notices = array();

    public function __construct() {
        // Add options page to WooCommerce
        add_action( 'admin_menu', array($this, 'add_options_page'), 999999 );

        // Hook rewrites
        add_action( 'init', array($this, 'add_rewrites'), 999999 );

        // Update category links
        add_filter( 'term_link', array($this, 'filter_permalink'), 10, 3 );

        // Update product links
        add_filter( 'post_type_link', array($this, 'filter_product_permalink'), 10, 2 );

        // Add shop page to breadcrumbs
        add_filter( 'woocommerce_get_breadcrumb', array($this, 'filter_breadcrumbs') , 10, 1);

        // Rewrite WooCommmerce shop page filter
        add_filter( 'woocommerce_get_shop_page_id', array ( $this, 'force_page_id_filter' ), 9999);

        // Force the page template to be loaded - overwrites WooCommerce's template filter
        add_filter( 'template_include', array( $this, 'force_page_template_filter'), 9999 );

        // Remove WC filters if loading the shop page normally
        add_action('pre_get_posts', function($q) {
            global $woocommerce;
            if ($q->is_main_query() && absint( $q->get( 'page_id' )) === $this->get_shop_id()) {
                $woocommerce->query->remove_product_query();
                $woocommerce->query->remove_ordering_args();
            }
        }, 0);
    }


    /* Hierarchical URL Layout:
     *      /endpoint/                               =>  Shop Page (WooRewrite Settings)
     *      /endpoint/category-name/                 =>  Category
     *      /endpoint/category-name/page/2/          =>  Category (Set Page)
     *      /endpoint/category-name/product-name/    =>  Product
    */
    public function add_rewrites() {

        // Rewrite /endpoint/ to Shop page (Overrides WooCommerce shop endpoint) (Only if the shop page ID is set)
        is_numeric($this->get_shop_id()) && $this->get_shop_id() >= 0 ? add_rewrite_rule($this->get_endpoint(true) . '?$', 'index.php?page_id=' . $this->get_shop_id(), 'top') : false;

        // Rewrite /endpoint/category-name/ to Category Archive page
        add_rewrite_rule($this->get_endpoint(true) . '([^/]+)/?$', 'index.php?product_cat=$matches[1]', 'top');

        // Rewrite /endpoint/category-name/page/0/ to Archive Page Number
        add_rewrite_rule($this->get_endpoint(true) . '([^/]+)/page/([^/]+)?$', 'index.php?product_cat=$matches[1]&paged=$matches[2]', 'top');

        // Rewrite /endpoint/category-name/product-name/ to Single Product page
        add_rewrite_rule($this->get_endpoint(true) . '([^/]+)/([^/]+)/?$', 'index.php?product=$matches[2]', 'top');
    }

    public function force_page_id_filter( $id ) {
        $shopid = $this->get_shop_id();
        return is_numeric($shopid) && $shopid > 0 ? $shopid : $id;
    }

    public function force_page_template_filter( $template ) {
        if (!$this->is_shop_page()) {
            return $template;
        }
        $shop_template = $this->locate_shop_template();
        return empty($shop_template) ? $template : $shop_template;
	}

    public function get_endpoint($remove_leading = false) {
        $_rewrite = get_option('woorewrite_endpoint');
        $_rewrite = is_string($_rewrite) ? self::slashit($_rewrite) : '/shop/';
        if ($remove_leading) {
            $_rewrite = substr($_rewrite, 1);
        }
        return $_rewrite;
    }

    public function locate_shop_template() {
        $template = locate_template($this->get_shop_template());

        // If the above template was found - use this template
        if (file_exists($template)) { return $template; }

        // Defaults to the following template hierarchy if above template not found
		$possible_templates = array(
		    'woorewrite-shop',
		    'page-shop',
		    'page-' . $this->get_shop_id(),
			'page',
			'index',
		);

		foreach ( $possible_templates as $possible_template ) {
			$path = get_query_template( $possible_template );
			if ( $path ) {
			    return $path;
			    break;
			}
		}
		return '';
    }

    public function get_shop_template() {
        $_shoptemplate = get_option('woorewrite_shop_template');
        if (!empty($_shoptemplate)) {
            return $_shoptemplate;
        } else {
            return ''; // Default template
        }
    }

    public function get_shop_id() {
        $_shoppage = get_option('woorewrite_shop_page');
        return is_numeric($_shoppage) ? intval($_shoppage) : false;
    }

    public function is_shop_page() {
        return is_numeric($this->get_shop_id()) ? ($this->get_shop_id() === get_queried_object_id()) : is_shop();
    }

    public function filter_product_permalink($url, $post) {
        if ($post->post_type === 'product') {
            $terms = get_the_terms($post->ID, 'product_cat');
            $term = null;
            if (!$terms) {
                $term = get_term(intval(get_option('default_category')));
            } else {
                $term = array_pop($terms);
            }
            $url = trailingslashit(home_url($this->get_endpoint()) . $term->slug . '/' . $post->post_name);
        }
        return $url;
    }

    public function filter_permalink($url, $term, $taxonomy) {
        switch ($taxonomy) {
            case 'product_cat':
                $url = trailingslashit(home_url($this->get_endpoint()) . $term->slug);
                break;
        }
        return $url;
    }

    // Add shop page to WooCommerce breadcrumbs
    public function filter_breadcrumbs($crumbs) {
        if (!$this->is_shop_page() && is_woocommerce()) {
            $home = array_shift($crumbs);
            array_unshift($crumbs, $home, array(
                is_numeric($this->get_shop_id()) ? get_the_title($this->get_shop_id()) : 'Shop',
                home_url($this->get_endpoint())
            ));
        }
        return $crumbs;
    }

    // Options Page
    public function handle_admin_page() {
        if (isset($_GET['flush']) && $_GET['flush'] === 'true') {
            flush_rewrite_rules();
            header('Location: admin.php?page=woorewrite&success=true');
        }

        if (isset($_GET['success']) && $_GET['success'] === 'true') {
            $this->add_notice('updated', 'Settings updated successfully.');
        }

        if (isset($_GET['endpoint'])) {
            $this->update_options();
        } else {
            require(__DIR__ . '/inc/admin.php');
        }
    }

    public function add_notice($type, $notice) {
        $this->notices[] = array(
            'type' => $type,
            'notice' => $notice
        );
    }

    public function display_notices() {
        echo '<div class="notices">';
        foreach ($this->notices as $notice) {
            echo '<div class="' . $notice['type'] . ' notice"><p>' . __( $notice['notice'] , 'woorewrite' ) . '</p></div>';
        }
        echo '</div>';
    }

    public function add_options_page() {
        add_submenu_page(
            'woocommerce',
    		'WooRewrite Settings',
    		'WooRewrite',
    		'manage_options',
    		'woorewrite',
    		array($this, 'handle_admin_page')
    	);
    }

    public function update_options() {
        if (isset($_GET['endpoint'])) {
            update_option('woorewrite_endpoint', self::slashit($_GET['endpoint']));
        }

        if (isset($_GET['shoppage'])) {
            update_option('woorewrite_shop_page', $_GET['shoppage']);
            update_option('woocommerce_shop_page_id', $_GET['shoppage']);
        }

        if (isset($_GET['shoppage_template'])) {
            update_option('woorewrite_shop_template', $_GET['shoppage_template']);
        }

        header('Location: admin.php?page=woorewrite&flush=true');
    }

    // Static Instance
    public static $instance;
    public static function get() { return self::$instance; }
    public static function set($i) { self::$instance = $i; }

    public static function slashit($string) {
        $string = trailingslashit($string);
        $string = substr($string, 0, 1) === '/' ? $string : '/' . $string;
        return $string;
    }
}
