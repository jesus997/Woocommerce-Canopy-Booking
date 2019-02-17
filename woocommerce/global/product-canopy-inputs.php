<?php
    $is_vehicle_tour = isset($is_vehicle_tour) ? $is_vehicle_tour : false;
    $max_adults = value("maximum_number_of_adults", 100, $product->get_id());
    $max_children = value("maximum_number_of_children", 100, $product->get_id());
    $childrens_field_caption = value("childrens_field_caption", "", $product->get_id());
    $second_price = value("second_price", false, $product->get_id());
?>
<input type="hidden" name="_early_discount" data-attribute_name="attribute_early_discount" value="0" onfocus="blur();" style="display:none;" />
<input type="hidden" name="_bnf_discount" data-attribute_name="attribute_bnf_discount" value="0" onfocus="blur();" style="display:none;" />
<input type="hidden" name="_blf_discount" data-attribute_name="attribute_blf_discount" value="0" onfocus="blur();" style="display:none;" />
<table class="canopytour-inputs" cellspacing="0">
    <tbody>
        <tr>
            <td class="label">
                <label for="_tour_date"><?= __("Select a date", "wcb") ?></label>
            </td>
            <td class="value">
                <input type="text" name="_tour_date" id="_tour_date" data-attribute_name="attribute_tour_date" placeholder="dd/mm/yyyy" required value="<?= $ddate ?>" autocomplete="off" onfocus="blur();" style="margin-bottom: 0;" />
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
                        $i = 0;
                        foreach($schedule as $time) {
                            $selected = $i === 0 ? ' selected="selected"' : '' ; ?>
                            <option value="<?= trim($time['schedule']) ?>"<?= $selected ?>><?= $time['schedule'] ?></option> <?php
                            $i++;
                        } ?>
                    </select>
                </td>
            </tr> <?php
        }
        if(!$is_vehicle_tour) { ?>
            <tr>
                <td class="label">
                    <label for="_tour_adults"><?= __("Adults", "wcb") ?> (<?= $product->get_price_html() ?>)</label>
                </td>
                <td class="value"> <?php
                    woocommerce_quantity_input( array(
                        'input_name'    => '_tour_adults',
                        'min_value'     => 1,
                        'max_value'     => $max_adults,
                        'input_value'   => $dadults, // WPCS: CSRF ok, input var ok.
                    ) ); ?>
                </td>
            </tr> <?php
            if(!empty($second_price)) { ?>
                <tr>
                    <td class="label">
                        <label for="_tour_children"><?= __("Children", "wcb") ?> (<?= wc_price($second_price) ?>)</label>
                    </td>
                    <td class="value children_quantity_input">
                        <div class="input"> <?php
                        woocommerce_quantity_input( array(
                            'input_name'    => '_tour_children',
                            'min_value'     => 0,
                            'max_value'     => $max_children,
                            'input_value'   => $dchildren, // WPCS: CSRF ok, input var ok.
                        ) ); ?>
                        </div>
                        <small><?= $childrens_field_caption ?></small>
                    </td>
                </tr> <?php
            }
        }
        if(count($transportation) > 0) {
            $fs = $schedule[0]['schedule']; ?>
            <tr>
                <td class="label">
                    <label for="_need_transportation"><?= __("Pick-up place", "wcb") ?></label>
                </td>
                <td class="value">
                    <select name="_need_transportation" id="_need_transportation" data-attribute_name="attribute_need_transportation" required>
                        <!--<option value="No"><?= __( 'No', 'wcb'); ?></option>--><?php
                        foreach ($transportation[$fs] as $transpo => $schedule) { ?>
                            <option value="<?= trim($transpo) ?>"><?= $transpo ?></option> <?php
                        } ?>
                    </select>
                </td>
            </tr>
            <tr> <!-- data-show-if="_need_transportation" data-is="No" -->
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