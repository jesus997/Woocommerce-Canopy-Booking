<?php
$blocked_days = getBlockedDays($product);
$dates_blocked = getDatesBlocked($product);
$trans_i18n = [
    "no_schedules" => __("Sorry, there are no transportation schedules for the specified activity schedule.", "wcb"),
];
$tour_date = isset($ddate) && !empty($ddate) ? $ddate : date("d/m/Y");
$tour_date = \DateTime::createFromFormat('d/m/Y', $tour_date);
$tour_date = $tour_date->format('Y/m/d');
?>
<script>
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
			var data = <?= json_encode($transportation) ?>,
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
						var selected = i == 0 ? ' selected="selected"' : '';
                        html += "<option value=\"" + g + "\"" + selected + ">" + g + "</option>";
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
			today = new Date(),
			tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 2),
			opt = {
				language: '<?= substr(get_locale(), 0, 2) ?>',
				minDate: tomorrow,
				autoClose: true,
				startDate: new Date("<?= $tour_date ?>"),
				onRenderCell: function (date, cellType) {
					if (cellType == 'day') {
						var day = date.getDay(),
							mth = date.getMonth() + 1,
							dte = date.getDate(),
							dat = (dte < 10 ? "0" : "") + dte + "/" + (mth < 10 ? "0" : "") + mth + "/" + date.getFullYear(),
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