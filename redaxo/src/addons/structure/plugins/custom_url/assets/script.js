<script type="text/javascript">
    jQuery(document).ready(function($) {
        urlTypes = new Array();
        urlType = <?php echo $urlType; ?>;
        cloneChecked = <?php if ($cloneChecked) { echo 'true'; } else { echo 'false;'; } ?>;

        urlTypes[<?php echo SEO42_URL_TYPE_DEFAULT; ?>] = '#urltype_default';
        urlTypes[<?php echo SEO42_URL_TYPE_USERDEF_INTERN; ?>] = '#urltype_userdef_intern';
        urlTypes[<?php echo SEO42_URL_TYPE_USERDEF_EXTERN; ?>] = '#urltype_userdef_extern';
        urlTypes[<?php echo SEO42_URL_TYPE_MEDIAPOOL; ?>] = '#urltype_mediapool';
        urlTypes[<?php echo SEO42_URL_TYPE_INTERN_REPLACE; ?>] = '#urltype_intern_replace';
        urlTypes[<?php echo SEO42_URL_TYPE_INTERN_REPLACE_CLANG; ?>] = '#urltype_intern_replace_clang';
        urlTypes[<?php echo SEO42_URL_TYPE_REMOVE_ROOT_CAT; ?>] = '#urltype_remove_root_cat';
        urlTypes[<?php echo SEO42_URL_TYPE_CALL_FUNC; ?>] = '#urltype_call_func';
        urlTypes[<?php echo SEO42_URL_TYPE_LANGSWITCH; ?>] = '#urltype_langswitch';
        urlTypes[<?php echo SEO42_URL_TYPE_NONE; ?>] = '#urltype_none';

        $('#url_type').change(function() {
            // first hide all sections
            $('.section').hide();

            // then make section for new selected url type visible
            $(urlTypes[$(this).val()]).show();

            // clone checkbox
            if ($(this).val() == urlType && cloneChecked) {
                // set correct checkbox state
                $('#url_clone').attr('checked', true);
            } else {
                // reset checkbox state
                $('#url_clone').attr('checked', false);
            }

            // hide row with clone checkbox for certain url types
            if ($(this).val() == <?php echo SEO42_URL_TYPE_USERDEF_INTERN; ?> || $(this).val() == <?php echo SEO42_URL_TYPE_LANGSWITCH; ?>) {
                $('#clone-row').hide();
            } else {
                $('#clone-row').show();
            }
        });

    <?php if ($dataUpdated) { ?>jQuery('.rex-navi-content li:last-child a').attr('href', '<?php echo seo42::getFullUrl(); ?>');<?php } ?>

    <?php if (!$doDataUpdate) { ?>$('#url_type').val(<?php echo rex_request('url_type', 'int'); ?>); $('#url_type').change();<?php } ?>
    });
</script>
