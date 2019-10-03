<div class="woorewrite">
    <h3>WooRewrite Settings</h3>
    <form action="">
        <input type="hidden" name="page" value="woorewrite" />
        <label for="shop-endpoint">
            <span>Shop Endpoint</span>
            <input id="shop-endpoint" type="text" name="endpoint" placeholder="/shop/" value="<?php echo WooRewrite::get()->get_endpoint(); ?>">
        </label>
        <label for="shop-page">
            <span>Shop Page</span>
            <select id="shop-page" name="shoppage">
                <?php
                foreach (get_pages() as $page) {
                    $_current = WooRewrite::get()->get_shop_id() === $page->ID;
                    echo '<option value="' . $page->ID . ($_current ? '" selected="selected' : '') . '">' . $page->post_title . '</option>';
                }
                ?>
            </select>
        </label>
        <button type="submit" class="button button-primary button-large">Update</button>
    </form>
</div>
<style type="text/css">
    .woorewrite {
        margin: 50px 0;
        background: #fff;
        max-width: 400px;
        padding: 20px;
    }

    .woorewrite h3 {
        text-align: center;
        padding-bottom: 20px;
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

    .woorewrite form::after,
    .woorewrite label::after {
        clear: both;
        content: '';
        display: block;
    }

    .woorewrite select,
    .woorewrite input {
        float: right;
        width: 150px;
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
</style>
