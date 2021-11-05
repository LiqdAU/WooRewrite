# WooRewrite
 
Plugin to rewrite WooCommerce URLs to use a hierarchical structure

1. Install
2. Navigate to WooCommerce → WooRewrite
3. Set endpoint to desired shop endpoint
4. Click Save

You can optionally select a page to use as the shop page, and if you would like you can select a page template to use instead of the default WooCommerce archive page.

---

The plugin will also look for the following templates before loading the WooCommerce default template, so ensure these templates do not exist in your theme if you experience issues with the display of the store, unless of course you want to use these templates to modify the archive template.
- woorewrite-shop.php
- page-shop.php
- page-{ID}.php *(With ID being the ID of the selected page in the plugin settings)*

--- 

### What this plugin does

This plugin is mainly to rewrite the URLs into a more hierarchical structure.

For example, if you set the endpoint to "shop" (default), the following rewrites will be applied:

###### Shop Page
`/shop/` → `/shop/`

###### Categories
`/product-category/{category-name}/` → `/shop/{category-name}/`

###### Pages
`/product-category/{category-name}/page/{#}/` → `/shop/{category-name}/page/{#}/`

###### Products
`/product/{product-slug}/` → `/shop/category-name/{product-slug}/`


###### Note
*{ } defines dynamic portion of the url*
