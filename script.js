(function(window, $, undefined) {

    var surcusPTag = {

        parseTags: function(tagsList){
            var tags = tagsList.replace(/X\s*/g, ',').replace(/X+/g, ',').replace(/[X\s]+$/, '').replace(/^[,\s]+/, '');
            return tagBox.clean(tags).split(',');
        },

        buildOptions: function(tagsList){

            var tags = surcusPTag.parseTags(tagsList);
            var selectElement = $('#surcus_ptag_selector');

            selectElement.empty();
            selectElement.append($('<option />').text('None'));


            $.each(tags, function(key, val){

                if ( val.trim().length ) {

                    var newOption = $('<option />').text(val).val(val);

                    if (val == $('#selected_surcus_ptag').val()) {
                        newOption.prop('selected', true);
                    }
                    selectElement.append(newOption);
                }
            });
        }
    };

    $(document).on('click', '.tagadd, .ntdelbutton', function(){
        surcusPTag.buildOptions($('.tagchecklist').find('span').text());
    });

    $(document).ready(function(){
        surcusPTag.buildOptions($('.the-tags').text());

        //Added to handle the use case of page-refresh and presented tag list differing
        //from what is stored in the db
        setTimeout(function(){
            surcusPTag.buildOptions($('.tagchecklist').find('span').text());
        }, 1000);
    });

})(window, jQuery);