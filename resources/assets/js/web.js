window.feather = {
    replace: function () {
        //
    }
};

require("./../../template/js/app");

$("#ajax-modal").on("shown.bs.modal", function(e) {
    $(this).load($(e.relatedTarget).attr("href"));
});

new LazyLoad({
    elements_selector: ".lazy"
});