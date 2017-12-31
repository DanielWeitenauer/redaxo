<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_add extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $category_id = $this->getVar('edit_id');

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'form_category_add' => $category_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('add_category'),
            'icon' => 'rex-icon rex-icon-add-category',
            'url' => $this->getVar('context')->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn'.(!$this->getVar('hide_border') ? ' btn-default' : ''),
                ],
                'title' => rex_i18n::msg('add_category'),
            ],
        ];

        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['attributes']['accesskey'] = $access_keys['add'];
            $button_params['attributes']['title'] .= ' ['.$access_keys['add'].']';
        }

        return $this->getButtonFragment($button_params);
    }

    /**
     * @return string
     */
    public function getModal()
    {
        $category_id = $this->getVar('edit_id');
        $clang = $this->getVar('clang');
        $data_colspan = 5; // Only for bc reasons

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }
        // Show form if only if requested
        if (rex_request('form_article_add', 'int', -1) != $category_id) {
            return '';
        }

        // EXTENSION POINT
        $cat_form_buttons = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_BUTTONS', '', [
            'id' => $category_id,
            'clang' => $clang,
        ]));

        // EXTENSION POINT
        $cat_form_add = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_ADD', '', [
            'id' => $category_id,
            'clang' => $clang,
            'data_colspan' => ($data_colspan + 1),
        ]));

        return '  
            <div class="modal fade" id="category-add-'.$category_id.'" style="text-align:left;">
                <div class="modal-dialog">
                    <form id="rex-form-category-add-'.$category_id.'" class="modal-content form-vertical" action="'.$this->context->getUrl($this->url_params).'" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title">'.rex_i18n::msg('header_category').'</h3>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="rex-api-call" value="category_add" />
                            <input type="hidden" name="parent-category-id" value="'.$category_id.'" />
                            <div class="row">
                                <div class="col-xs-12">
                                    <dl class="rex-form-group form-group">
                                        <dt><label for="category-name">'.rex_i18n::msg('header_category').'</label></dt>
                                        <dd><input id="category-name" class="form-control rex-js-autofocus" type="text" name="category-name" autofocus /></dd>
                                    </dl>
                                    <dl class="rex-form-group form-group">
                                        <dt><label for="category-position">'.rex_i18n::msg('header_priority').'</label></dt>
                                        <dd><input id="category-position" class="form-control" type="text" name="category-position" value="'.($this->pager->getRowCount() + 1).'" /></dd>
                                    </dl>
                                    '.$cat_form_buttons.'
                                    '.$cat_form_add.'
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-save" type="submit" name="category-add-button" '.rex::getAccesskey(rex_i18n::msg('add_category'), 'save').'>'.rex_i18n::msg('add_category').'</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.rex_i18n::msg('form_abort').'</button>
                        </div>
                    </form>
                </div>
            </div> 
            <script>
                $(document).ready(function() {
                    $("#category-add-'.$category_id.'").modal();
                });
            </script>
        ';
    }
}
