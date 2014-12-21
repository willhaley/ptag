(function (window, $, undefined) {
    'use strict';

    //Handles main ptag logic
    var surcusPTag = {

        //cleans tag list and returns array
        //Uses tagBox object from core.  This is mostly borrowed core code.
        parseTags: function (tagsList) {
            var tags = tagsList.replace(/X\s*/g, ',').replace(/X+/g, ',').replace(/[X\s]+$/, '').replace(/^[,\s]+/, '');
            return tagBox.clean(tags).split(',');
        },

        //Parses list and rebuilds drop down
        buildOptions: function (tagsList) {

            var tags = surcusPTag.parseTags(tagsList);
            var selectElement = $('#surcus_ptag_selector');

            selectElement.empty();
            selectElement.append($('<option />').text('Not Set'));

            $.each(tags, function (key, val) {

                if (val.trim().length) {

                    var newOption = $('<option />').text(val).val(val);

                    if (val === $('#selected_surcus_ptag').val()) {
                        newOption.prop('selected', true);
                    }
                    selectElement.append(newOption);
                }
            });
        }
    };


    //First pull in the list of tags from what is stored in the db
    //After the page has had a second to load, we need to do it again based on
    //what is in the displayed list b/c it may differ.
    $(document).ready(function () {
        surcusPTag.buildOptions($('.the-tags').text());

        //Added to handle the use case of page-refresh and presented tag list differing
        //from what is stored in the db
        setTimeout(function () {
            surcusPTag.buildOptions($('.tagchecklist').find('span').text());
        }, 1000);
    });

    //Listen for any action taken on the tag box and rebuild the dropdown
    $(document).on('click', '.tagadd, .ntdelbutton', function () {
        surcusPTag.buildOptions($('.tagchecklist').find('span').text());
        $('.surcus-ptag__tag').text($('#surcus_ptag_selector').val());

    });

    //Updated the displayed primary tag when drop down changed
    $(document).on('change', '#surcus_ptag_selector', function () {
        $('.surcus-ptag__tag').text($(this).val());
    });

    //On cancel request, reset the drop down and displayed value to
    //stored value to avoid confusion. Close Drop Down.
    $(document).on('click', '.surcus_ptag__cancel', function () {
        $('.surcus-ptag__tag').text($('#selected_surcus_ptag').val());
        $('.surcus-ptag__edit').slideToggle();
        surcusPTag.buildOptions($('.tagchecklist').find('span').text());
    });

    //Confirm change by just closing drop down.
    $(document).on('click', '.surcus_ptag__confirm', function () {
        $('.surcus-ptag__edit').slideToggle();
    });

})(window, jQuery);