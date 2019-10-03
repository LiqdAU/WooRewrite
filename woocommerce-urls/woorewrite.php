<?php

class WooRewrite {

    public function __construct() {
        // Add options page to WooCommerce
        add_action( 'admin_menu', array($this, 'add_options_page'), 9999 );
        // Hook rewrites
        add_action('init', array($this, 'add_rewrites'), 99999);

        // Update category links
        add_filter('term_link', array($this, 'filter_permalink'), 10, 3);
        // Update product links
        add_filter('post_type_link', array($this, 'filter_product_permalink'), 10, 2);
    }


    /* Hierarchical URL Layout:
     *      /endpoint/                               =>  Shop Page (WooRewrite Settings)
     *      /endpoint/category-name/                 =>  Category
     *      /endpoint/category-name/product-name/    =>  Product
    */
    public function add_rewrites() {
        // Rewrite /endpoint/ to Shop page (Overrides WooCommerce shop endpoint)
        add_rewrite_rule($this->get_endpoint(true) . '?$', 'index.php?page_id=' . $this->get_shop_id(), 'top');

        // Rewrite /endpoint/category-name/ to Category Archive page
        add_rewrite_rule($this->get_endpoint(true) . '([^/]+)/?$', 'index.php?product_cat=$matches[1]', 'top');

        // Rewrite /endpoint/category-name/product-name/ to Single Product page
        add_rewrite_rule($this->get_endpoint(true) . '([^/]+)/([^/]+)/?$', 'index.php?product=$matches[2]', 'top');
    }

    public function get_endpoint($remove_leading = false) {
        $_rewrite = get_option('woorewrite_endpoint');
        $_rewrite = is_string($_rewrite) ? self::slashit($_rewrite) : '/shop/';
        if ($remove_leading) {
            $_rewrite = substr($_rewrite, 1);
        }
        return $_rewrite;
    }

    public function get_shop_id() {
        $_shoppage = get_option('woorewrite_shop_page');
        return is_numeric($_shoppage) ? intval($_shoppage) : false;
    }


    public function filter_product_permalink($url, $post) {
        if ($post->post_type === 'product') {
            $term = array_pop(get_the_terms($post->ID, 'product_cat'));
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


    // Options Page
    public function handle_admin_page() {
        if (isset($_GET['endpoint'])) {
            $this->update_options();
            flush_rewrite_rules();
        } else {
            require(__DIR__ . '/inc/admin.php');
        }
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
        }

        header('Location: admin.php?page=woorewrite');
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