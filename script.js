jQuery(function () {
    jQuery('.searchform__qsearch_in')
        .each(function (i, input) {
            var $input = jQuery(input);
            var $form = $input.parent().parent();
            var $output = $form.find('.searchform__qsearch_out');
            var $ns = $form.find('[name="ns"]');
            var $notns = $form.find('[name="-ns"]');

            $input.dw_qsearch({

                output: $output,

                getSearchterm: function () {
                    let query = $input.val(),
                        reg = new RegExp("(?:^| )(?:\\^|@|-ns:|ns:)[\\w:]+");
                    if (reg.test(query)) {
                        return query;
                    }
                    let prefix = ' @';
                    let namespace = $ns.val();
                    let excludednamespace = $notns.val();

                    if(excludednamespace) {
                        namespace = excludednamespace;
                        prefix = ' ^';
                    }

                    return query + (namespace ? prefix + namespace : '');
                }
            });

        });
});
