<?php
$product_id = $product->get_id();
$sactive = value("souvenir_active", false, $product_id);
$name = value("souvenir_name", $product_id);
$vwod = value("souvenir_valid_with_other_discounts", true, $product_id);
$increase_price = value("souvenir_increase_price", false, $product_id);
$new_price = value("souvenir_price", 0, $product_id);
$doatats = value("days_of_anticipation_to_apply_this_souvenir", 0, $product_id);
$gifts = value("souvenir_gifts", [], $product_id);
$souvenir_price_text = sprintf(__("completely <strong>free</strong>", "wcb"), wc_price($new_price));

if($increase_price && $new_price > 0) {
    $souvenir_price_text = sprintf(__("for only %s per adult", "wcb"), wc_price($new_price));
}

if($sactive) { ?>
<input type="hidden" id="souvenir_hidden_input" name="__is_plus" value="0">
<div class="souvenirs-container"<?= $doatats > 0 ? " style=\"display:none;\"" : "" ?>>
    <div class="souvenirs-wrapper">
        <div class="souvenir-call-to-action">
            <p><?= sprintf(__("Turn your adventure into <strong>%s</strong> %s and receive upon your arrival", "wcb"),
                    $name,
                    $souvenir_price_text
                )
            ?>:</p>
            <ul><?php
                foreach ($gifts as $key => $gift) { ?>
                    <li><?= $gift['amount'] ?> <?= $gift['name'] ?></li> <?php
                }?>
            </ul>

            <div class="souvenir-buttons">
                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button souvenir_add_to_cart_button button alt"><?php echo esc_html( __("I want the PLUS experience", "wcb") ); ?></button>
                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="souvenir_add_to_cart_link"><?php echo esc_html( __("Maybe another day, I want the normal experience", "wcb") ); ?></button>
            </div>
        </div>
    </div>
</div>
<?php
}

if(!$sactive || $doatats > 0) { ?>
<div class="default_single_add_to_cart_button"> <?php
    if($sactive && $doatats > 0) { ?>
        <div class="souvenir-call-to-action-secondary">
            <p><?= sprintf(__("Book with %s days or less in advance and get %s.", "wcb"),
                $doatats,
                humanize_souvenirs_array($gifts)
            ) ?></p>
        </div> <?php
    } ?>
    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
</div><?php
}