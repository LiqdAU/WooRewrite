<div class="woorewrite-wrapper">
    <?php WooRewrite::get()->display_notices(); ?>
    <div class="woorewrite">
        <div class="page-title">
            <h3>WooRewrite</h3>
            <h4>Shop Page Settings</h4>
        </div>
        <form action="">
            <input type="hidden" name="page" value="woorewrite" />
            <label for="shop-endpoint" class="shop-endpoint">
                <span>Endpoint</span>
                <input id="shop-endpoint" type="text" name="endpoint" placeholder="/shop/" value="<?php echo WooRewrite::get()->get_endpoint(); ?>">
            </label>
            <label for="shop-page" class="shop-page">
                <span>Page</span>
                <select id="shop-page" name="shoppage">
                    <option value="">Use default</option>
                    <option disabled="disabled">--------------</option>
                    <?php
                    foreach (get_pages() as $page) {
                        $_current = WooRewrite::get()->get_shop_id() === $page->ID;
                        echo '<option value="' . $page->ID . ($_current ? '" selected="selected' : '') . '">' . (empty($page->post_title) ? '[ Untitled Page ]' : $page->post_title) . '</option>';
                    }
                    ?>
                </select>
            </label>
            <label for="shop-page-template" class="shop-page-template">
                <span>Template</span>
                <select id="shop-page-template" name="shoppage_template">
                    <option value="">Default Page Template</option>
                    <option disabled="disabled">--------------</option>
                    <?php
                    foreach (get_page_templates() as $template) {
                        $_current = WooRewrite::get()->get_shop_template() === $template;
                        echo '<option value="' . $template . ($_current ? '" selected="selected' : '') . '">' . $template . '</option>';
                    }
                    ?>
                </select>
            </label>
            <div class="button-wrapper">
                <button type="submit" class="button button-primary button-large">Update</button>
            </div>
            <div class="other-info">
                <p>If your endpoint is set to <b>/shop/</b> the default WooCommerce endpoint will be overwritten.</p>
            </div>
        </form>
    </div>
    <style type="text/css">
        .woorewrite-wrapper {
            margin-top: 50px;
        }

        .woorewrite-wrapper .notice {
            margin: 0;
            margin-bottom: 15px;
            max-width: 500px;
            box-sizing: border-box;
        }


        .woorewrite {
            background: #fff;
            width: 100%;
            max-width: 500px;
            padding: 20px;
            box-sizing: border-box;
        }

        .woorewrite h4 {
            margin: 0;
        }

        .woorewrite .page-title {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 40px;
            border-bottom: 1px solid #eee;
        }

        .woorewrite form {
            box-sizing: border-box;
            padding: 20px;
        }

        .woorewrite label  {
            display: block;
            margin-bottom: 20px;
        }

        .woorewrite .button-wrapper::after,
        .woorewrite label::after {
            clear: both;
            content: '';
            display: block;
        }

        .woorewrite select,
        .woorewrite input {
            float: right;
            width: 250px;
        }

        .woorewrite label span {
            display: inline-block;
            margin-right: 30px;
            font-weight: bold;
            padding: 4px;
        }

        .woorewrite button {
            float: right;
        }

        .woorewrite .other-info {
            opacity: 0.8;
            clear: both;
            margin-top: 10px;
        }

        .woorewrite .other-info p {
            margin: 0;
            margin-top: 20px;
        }

        .woorewrite label {
            overflow: hidden;
            transition: max-height 0.5s;
        }

        .woorewrite .shop-page-template {
            max-height: 30px;
        }

        .woorewrite[data-shoppage=""] .shop-page-template {
            max-height: 0;
        }
    </style>
    <script type="text/javascript">
        (function($) {
            var woorewrite = $('.woorewrite');

            $('.woorewrite select').change(function() {
                $(woorewrite).attr('data-' + $(this).attr('name'), $(this).val());
            }).each(function() {
                $(this).change();
            });

        })(jQuery);
    </script>
</div>
