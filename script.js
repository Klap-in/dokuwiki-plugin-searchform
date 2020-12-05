jQuery(function () {
    jQuery('.searchform__qsearch_in')
        .each(function (i, input) {
            var $input = jQuery(input);
            var $form = $input.parent().parent();
            var $output = $form.find('.searchform__qsearch_out');
            var $ns = $form.find('[name="ns"]');

            $input.dw_qsearch({

                output: $output,

                getSearchterm: function () {
                    let query = $input.val(),
                        reg = new RegExp("(?:^| )(?:@|ns:)[\\w:]+");
                    if (reg.test(query)) {
                        return query;
                    }
                    let namespace = $ns.val();
                    return query + (namespace ? ' @' + namespace : '');
                }
            });

        });
});
