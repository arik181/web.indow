$.fn.strip_ids = function () {
    this.removeAttr('id');
    this.find('*').removeAttr('id');
    return this;
};

function create_svgs() {
    $('.cut_image_cont').each(function () {
        var contents;
        var title = 'Item ' + $(this).data('id');
        var sheet_width = $(this).data('sheet-width');
        var sheet_height = $(this).data('sheet-height');
        var sheetdim = [sheet_width, sheet_height];
        var cuts = $(this).data('cuts');
        if (cuts || sheet_width || sheet_height) {
            draw_lines(sheetdim, cuts, null);
            $('#title').html(title);
            contents = $('#svg1').clone().strip_ids();
            contents.find('tspan').each(function () {
                if ($(this).css('display') === 'none') {
                    $(this).remove();
                }
            });
            var serializer = new XMLSerializer();
            var str = serializer.serializeToString(contents[0]);
            $(this).html(str);
           // console.log(str);
        }
    });
}

function get_svgs() {
    var items = {};
    $('.cut_image_cont').each(function () {
        var serializer = new XMLSerializer();
        var id = $(this).data('id');
        var svg = $(this).children()[0];
        console.log(svg);
        var str = serializer.serializeToString(svg);
        items[id] = str;
    });
    return items;
}

$(function () {
    create_svgs();
    var items = get_svgs();
    $('#data').val(JSON.stringify(items));
    $('#postform').submit();
});