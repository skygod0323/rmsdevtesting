$(document).ready(function() {
    $(".js-range-slider").ionRangeSlider({
        // min: 0,
        // max: 10,
        // from: 0,
        // step: 1,
        // grid: true,
        // grid_snap: true,
        type: "single",
        skin: "big",
        min: 0,
        max: 10,
        from: 0,
        step: 1,
        grid: true,
        grid_snap: true,
        onStart: function(data) {
            $('.range-wrapper .btn__like').attr('data-vote', data.from)
        },
        onChange: function(data) {
            $('.range-wrapper .btn__like').attr('data-vote', data.from)
        },
        onFinish: function(data) {
            $('.range-wrapper .btn__like').attr('data-vote', data.from)
        },
        onUpdate: function(data) {
            $('.range-wrapper .btn__like').attr('data-vote', data.from)

        }

    });

    $('.range-wrapper .rating-item').click((e) => {
        let _this = $(e.currentTarget);
        _this.parent().find('.active').removeClass('active');
        _this.addClass('active');
        let vote = _this.data('vote');
        $('.range-wrapper .btn__like').attr('data-vote', vote);
    })

    $(".js-guest").on("click", function() {
        var date = new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000);
        document.cookie =
            "kt_rt_first=hidden; path=/; expires=" + date.toUTCString();
        $(".first-visit").addClass("hidden");
    });

    $("body").on("click", ".js-close", function() {
        $(".fancybox-close").click();
    });

    $("[data-select]").change(function() {
        var selected = $(this).attr("data-select");
        $(".category_ids").html("");
        $("[data-select~=" + selected + "]").each(function() {
            if ($(this).find("option:selected") !== "") {
                $(this)
                    .val()
                    .forEach(function(item) {
                        var html =
                            '<input type="hidden" name="category_ids[]" value="' +
                            item +
                            '">';
                        $(".category_ids").append(html);
                    });
            }
        });
    });

    $(document).on("click", ".js-filters", function(e) {
        e.preventDefault();

        var newSearch = {};
        var href = e.target.href.split("?");

        if (href[1]) {
            newSearch = searchToObj(href[1]);
        }

        var currentSearch = searchToObj(location.search.substring(1));

        newSearch = {
            ...currentSearch,
            ...newSearch,
        };

        location.href = href[0] + "?" + $.param(newSearch);

        return false;
    });

    function searchToObj(search) {
        if (search == "") return {};

        return JSON.parse(
            '{"' +
            decodeURI(search)
            .replace(/"/g, '\\"')
            .replace(/&/g, '","')
            .replace(/=/g, '":"') +
            '"}'
        );
    }
});