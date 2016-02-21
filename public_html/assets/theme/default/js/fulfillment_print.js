$(function () {
    $.fn.strip_ids = function () {
        this.removeAttr('id');
        this.find('*').removeAttr('id');
        return this;
    };
	function round_add(val) {
		return Math.round((parseFloat(val) + .03) * 1000) / 1000;
	}
    $('.cut_image_cont').each(function () {
        var contents;
        var sheet_width = $(this).data('sheet-width');
        var sheet_height = $(this).data('sheet-height');
        var sheetdim = [sheet_width, sheet_height];
        var cuts = $(this).data('cuts');
        if (cuts || sheet_width || sheet_height) {
            var sizes = draw_lines(sheetdim, cuts, null);
			var $traveler = $(this).closest('.traveler_cont');
			$traveler.find('.trav_b_len').html(round_add(sizes.b));
			$traveler.find('.trav_t_len').html(round_add(sizes.t));
			$traveler.find('.trav_l_len').html(round_add(sizes.l));
			$traveler.find('.trav_r_len').html(round_add(sizes.r));
            contents = $('#svg1').clone().strip_ids();
            $(this).html(contents);
        }
        //window.print();
    });
});