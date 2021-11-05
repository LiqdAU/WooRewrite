# WooRewrite
 
WordPress Plugin to rewrite WooCommerce URLs to use a hierarchical structure.

Some of the functionality in this plugin can be achieved by altering the WP permalink settings, however this plugin simplifies the process and adds some additional little tweaks for maximum compatibility.

---

#### Installation
1. Install via .zip file
2. Navigate to WooCommerce → WooRewrite
3. Set endpoint to desired shop endpoint
4. Click Save

You can optionally select a page to use as the shop page, and if you would like you can select a page template to use instead of the default WooCommerce archive page.

--- 

#### What this plugin does

This plugin is mainly to rewrite the URLs into a more hierarchical structure.

For example, if you set the endpoint to "shop" *(default)*, the following rewrites will be applied:

###### Shop Page
`/shop/` → `/shop/`

###### Categories
`/product-category/{category-name}/` → `/shop/{category-name}/`

###### Pages
`/product-category/{category-name}/page/{#}/` → `/shop/{category-name}/page/{#}/`

###### Products
`/product/{product-slug}/` → `/shop/category-name/{product-slug}/`


###### Note
*{ } denotes dynamic portion of the url*

---

#### Custom Page Templates
It can also be used to load a custom page template for your shop page, to allow for completely custom Shop pages without modifying WooCommerce templates.
This can be done by selecting a Shop Page & Page Template in the plugin settings.

The plugin will also look for the following templates *(in this order)* before loading the WooCommerce default template, so ensure these templates do not exist in your theme if you experience issues with the display of the store, unless of course you want to use these templates to modify the archive template.
- woorewrite-shop.php
- page-shop.php
- page-{ID}.php *(With ID being the ID of the selected page in the plugin settings)*

--- 

#### Breadcrumbs
WooRewrite will also add the Shop page to the WooCommerce breadcrumbs output.

For example `Home → Shop → Category → Product`
instead of `Home → Category → Product`
