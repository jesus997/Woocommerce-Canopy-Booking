<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.1
 */
defined( 'ABSPATH' ) || exit;
global $product;

$attribute_keys = array_keys( $attributes );

$schedule = value("schedule", false, $product->get_id());

$blocked_days = value("blocked_days", [], "wcb-options");
$dates_blocked = value("dates_blocked", [], "wcb-options");

$sbd = value("blocked_days", [], $product->get_id());
$sdb = value("dates_blocked", [], $product->get_id());

$blocked_days = array_merge($blocked_days, $sbd);
$dates_blocked = array_merge($dates_blocked, $sdb);

$blocked_days = array_unique($blocked_days);

$enable_booking_data = value("enable_booking_data", false, $product->get_id());

$transportation = [];

if($schedule && is_array($schedule)) {
    foreach($schedule as $time) {
        $stops = [];
        foreach($time["stops"] as $stop) {
            $stops[ get_the_title( $stop["stop"] ) ] = $stop["schedule"];
        }
        $transportation[$time['schedule']] = $stops;
    }
}

$trans_i18n = [
    "no_schedules" => __("Sorry, there are no transportation schedules for the specified activity schedule.", "wcb"),

];

$ddate = request("_tour_date", "");

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ); // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
	<?php if ($enable_booking_data) : ?>
		<table class="canopytour-inputs" cellspacing="0">
            <tbody>
			<input type="hidden" name="_early_discount" data-attribute_name="attribute_early_discount" value="0" onfocus="blur();" style="display:none;" />
			<input type="hidden" name="_bnf_discount" data-attribute_name="attribute_bnf_discount" value="0" onfocus="blur();" style="display:none;" />
            <input type="hidden" name="_blf_discount" data-attribute_name="attribute_blf_discount" value="0" onfocus="blur();" style="display:none;" />
                <tr>
					<td class="label">
                        <label for="_tour_date"><?= __("Select a date", "wcb") ?></label>
                    </td>
					<td class="value">
                        <input type="text" name="_tour_date" id="_tour_date" data-attribute_name="attribute_tour_date" placeholder="dd/mm/yyyy" required value="<?= $ddate ?>" onfocus="blur();" autocomplete="off" style="margin-bottom: 0;" />
                    </td>
                </tr> <?php
                if($schedule) { ?>
                    <tr>
                        <td class="label">
                            <label for="_tour_schedule"><?= __("Select a schedule", "wcb") ?></label>
                        </td>
                        <td class="value">
                            <select name="_tour_schedule" id="_tour_schedule" data-attribute_name="attribute_tour_schedule" required>
                                <option value="" disabled><?= __( 'Select a value', 'wcb'); ?></option> <?php
                                foreach($schedule as $time) { ?>
                                    <option value="<?= trim($time['schedule']) ?>"><?= $time['schedule'] ?></option> <?php
                                } ?>
                            </select>
                        </td>
                    </tr> <?php
                }
                if(count($transportation) > 0) {
					$fs = $schedule[0]['schedule']; ?>
                    <tr>
                        <td class="label">
                            <label for="_need_transportation"><?= __("Pick-up place", "wcb") ?></label>
                        </td>
                        <td class="value">
                            <select name="_need_transportation" id="_need_transportation" data-attribute_name="attribute_need_transportation" required>
                                <option value="No"><?= __( 'No', 'wcb'); ?></option><?php
                                foreach ($transportation[$fs] as $transpo => $schedule) { ?>
                                    <option value="<?= trim($transpo) ?>"><?= $transpo ?></option> <?php
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr data-show-if="_need_transportation" data-is="No">
                        <td class="label">
                            <label for="_transportation_schedules"><?= __("Pick-up schedule", "wcb") ?></label>
                        </td>
                        <td class="value">
                            <select name="_transportation_schedules" id="_transportation_schedules" data-attribute_name="attribute_transportation_schedules" required>
                                <option value="-1" disabled><?= __( 'Select a value', 'wcb'); ?></option><?php
                                if(is_array($transportation[$fs]) && !empty($transportation[$fs])) {
                                    foreach($transportation[$fs] as $transpo => $schedule) { ?>
                                        <option value="<?= trim($schedule) ?>"><?= $schedule ?></option> <?php
                                    }
                                } ?>
                            </select>
                            <p class="_transportation_schedules_message"></p>
                        </td>
                    </tr> <?php
                } ?>
            </tbody>
        </table>
	<?php endif; ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
						<td class="value">
							<?php
								wc_dropdown_variation_attribute_options( array(
									'options'   => $options,
									'attribute' => $attribute_name,
									'product'   => $product,
								) );
								//echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );
				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );
				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>
<?php if ($enable_booking_data) : ?>
<script>
	var TRANSJSON = <?= json_encode($transportation) ?>;
	var TRANSI18N = <?= json_encode($trans_i18n) ?>;
	(function($) {
		function data_show(t) {
			var selector = t.data("show-if"),
				to =  t.data("is");
			if($("#"+selector).val() === to) {
				t.hide();
			} else {
				t.show();
			}
		}
		function pickup_logic() {
			var data = TRANSJSON,
				schd = $("#_tour_schedule").val(),
				plce = $("#_need_transportation").val(),
				trgt = $("#_transportation_schedules"),
				sems = $("._transportation_schedules_message");
			if(schd !== "" && plce !== "No") {
				data = $.map(data[schd], function(g, i) {
					if(i === plce) {
						return g;
					}
				});
				if(data.length > 0) {
					sems.hide();
					trgt.show();
					trgt.find("option:not([value=\"-1\"])").remove();
					var html = "";
					$.each(data, function(i, g) {
						html += "<option value=\"" + g + "\">" + g + "</option>";
					});
					$(html).appendTo(trgt);
				} else {
					trgt.val("").hide();
					sems.text(TRANSI18N.no_schedules).show();
				}
			}
		}
		$(document).ready(function() {
			pickup_logic();
			$("#_need_transportation, #_tour_schedule").on("change", function(){
				pickup_logic();
			});
			$("[data-show-if]").each(function() {
				var t = $(this),
				selector = t.data("show-if");
				data_show(t);
				$("#"+selector).change(function() {
					data_show(t);
				});
			});
			var dp = $(".canopytour-inputs").find("#_tour_date"),
			blocked_days = [<?= implode(",", $blocked_days) ?>],
			dates_blocked = ["<?= implode("\",\"", simplificyDates($dates_blocked)) ?>"],
			opt = {
				language: '<?= substr(get_locale(), 0, 2) ?>',
				minDate: new Date(),
				autoClose: true,
				onRenderCell: function (date, cellType) {
					if (cellType == 'day') {
						var day = date.getDay(),
							mth = date.getMonth() + 1,
							dat = date.getDate() + "/" + (mth < 10 ? "0" : "") + mth + "/" + date.getFullYear(),
							isDisabled = (blocked_days.indexOf(day) != -1 || dates_blocked.indexOf(dat) != -1);
						return {
							disabled: isDisabled
						}
					}
				}
			};
			dp.datepicker(opt);
		});
	})(jQuery);
</script>
<?php endif; ?>
<?php
do_action( 'woocommerce_after_add_to_cart_form' );