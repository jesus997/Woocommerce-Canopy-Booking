<?php
$text_align = is_rtl() ? 'right' : 'left';

if(!function_exists("get_value_count_by_key")) {
    function get_value_count_by_key($key, $default=false, $metadata) {
        foreach($metadata as $metadata_key => $metadata_value) {
            if($metadata_value->__get("key") === $key) {
                return $metadata_value->__get("value");
            }
        }
        return $default;
    }
}

if(!function_exists("get_gifts_loaded")) {
    function get_gifts_loaded($gifts) {
        $gifts_loaded = [];
        foreach ($gifts as $key => $gift) {
            $gift_key = md5($gift['name']);
            $children = 0;
            if(!$gift["adults_only"]) {
                $children = $gift["children"];
            }
            $people = $gift["adults"] + $children;
            if(!array_key_exists($gift_key, $gifts_loaded)) {
                $gifts_loaded[$gift_key] = [
                    "amount" => $gift["amount"],
                    "name" => $gift["name"],
                    "people" => $people
                ];
            } else {
                $gifts_loaded[$gift_key]["people"] += $people;
            }
        }
        return $gifts_loaded;
    }
}

if(!function_exists("get_all_gifts_loaded")) {
    function get_all_gifts_loaded($products) {
        $all_gifts = [];
        foreach ($products as $key => $product) {
            $product_id = $product["product_id"];
            $metadata = $product["metadata"];
            $adults = get_value_count_by_key("_tour_adults", 1, $metadata);
            $children = get_value_count_by_key("_tour_children", 0, $metadata);
            $gifts = value("souvenir_gifts", [], $product_id);
            foreach ($gifts as $key => $gift) {
                $gift["adults"] = $adults;
                $gift["children"] = $children;
                $all_gifts[] = $gift;
            }
        }
        return get_gifts_loaded($all_gifts);
    }
}

if($is_email) { ?>
    <table id="souvenirs-container" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
        <tr>
            <td style="text-align:<?php echo esc_attr($text_align); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
                <h3><?php esc_html_e( 'Souvenirs', 'wcb' ); ?></h3>

                <table class="td souvenirs-container-table" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; padding:0;" border="1">
                    <tr>
                        <td class="td">
                            <p><?= __("Your purchase includes", "wcb") ?>:</p>
                            <ul class="souvenirs-list"> <?php
                                $gifts = get_all_gifts_loaded($products);
                                foreach ($gifts as $key => $gift) {
                                    $amount = $gift['amount'] * $gift['people']; ?>
                                    <li><?= $amount." ".$gift['name'] ?></li> <?php
                                } ?>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> <?php
} else { ?>
    <section class="woocommerce-order-souvenirs-list">
        <h3 class="woocommerce-order-souvenirs-list__title"><?php _e( 'Souvenirs', 'wcb' ); ?></h3>
        <p><?= __("Your purchase includes", "wcb") ?>:</p>
        <ul class="woocommerce-order-souvenirs-list__list"> <?php
            $gifts = get_all_gifts_loaded($products);
            foreach ($gifts as $key => $gift) {
                $amount = $gift['amount'] * $gift['people']; ?>
                <li><?= $amount." ".$gift['name'] ?></li> <?php
            } ?>
        </ul>
    </section> <?php
}