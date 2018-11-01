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

if(empty($attributes)) {
	$attributes = [];
	foreach($product->get_attributes() as $attr_name => $attr) {
		if(!$attr->get_variation()) continue;
		$attributes[$attr->get_name()] = $attr->get_options();
	}
}

if(empty($available_variations)) {
	$available_variations = $product->get_available_variations();
}

$attribute_keys = array_keys( $attributes );

$schedule = value("schedule", false, $product->get_id());
$transportations = value("transportation_stops", [], $product->get_id());

$blocked_days = value("blocked_days", [], "wcb-options");
$dates_blocked = value("dates_blocked", [], "wcb-options");

$enable_booking_data = value("enable_booking_data", false, $product->get_id());

$transportation_json = [];

foreach($transportations as $stop) {
    $schedules = explode(",", $stop['schedule'] );
    $transportation_json[ get_the_title($stop['transportation']) ] = $schedules;
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
	<table class="canopytour-inputs" cellspacing="0">
		<tbody>
			<input type="hidden" name="_early_discount" data-attribute_name="attribute_early_discount" value="0" onfocus="blur();" style="display:none;" />
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
							$schedule = explode(",", $schedule);
							natsort($schedule);
							foreach($schedule as $time) { ?>
								<option value="<?= trim($time) ?>"><?= $time ?></option> <?php
							} ?>
						</select>
					</td>
				</tr> <?php
			}
			if(count($transportations) > 0) { ?>
				<tr>
					<td class="label">
						<label for="_need_transportation"><?= __("Pick-up place", "wcb") ?></label>
					</td>
					<td class="value">
						<select name="_need_transportation" id="_need_transportation" data-attribute_name="attribute_need_transportation" required>
							<option value="No"><?= __( 'No', 'wcb'); ?></option><?php
							foreach ($transportations as $transportation) {
								$stop = get_the_title($transportation['transportation']); ?>
								<option value="<?= trim($stop) ?>"><?= $stop ?></option> <?php
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
							$schedules = $transportation[0]['schedule'];
							$schedules = explode(",", $schedules);
							if(is_array($schedules) && !empty($schedules)) {
								foreach($schedules as $schedule) { ?>
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
	var TRANSJSON = <?= json_encode($transportation_json) ?>;
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
				data = $.map(data[plce], function(g, i) {
					var f = g.match(/\d/g),
						s = schd.match(/\d/g);
					f = f.join("");
					s = s.join("");
					if(parseInt(f) < parseInt(s)) {
						return g;
					}
				});
				if(data.length > 0) {
					sems.hide();
					trgt.show();
					trgt.find("option:not([value=\"\"])").remove();
					var html = "<option value=\"" + data[data.length - 1] + "\">" + data[data.length - 1] + "</option>";
					/*$.each(data, function(i, g) {
						html += "<option value=\"" + g + "\">" + g + "</option>";
					});*/
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