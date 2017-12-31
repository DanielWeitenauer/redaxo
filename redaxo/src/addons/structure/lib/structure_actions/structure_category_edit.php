<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_edit extends rex_structure_action_field
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

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('change'),
            'icon' => 'rex-icon rex-icon-edit',
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'form_category_edit' => $category_id,
            ]);
            $button_params['url'] = $this->getVar('context')->getUrl($url_params, false);
            $button_params['attributes']['class'][] = 'btn-default';
        }

        $return = $this->getButtonFragment($button_params);

        // Display form if necessary
        if (rex_request('form_category_edit', 'int', -1) == $category_id) {
            $return .= $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getModal()
    {
        $category_id = $this->getVar('edit_id');
        $sql = $this->getVar('sql');

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }

        $clang = rex_request('clang', 'int');
        $clang = rex_clang::exists($clang) ? $clang : rex_clang::getStartId();
        $data_colspan = 5; // Only for BC reasons

        // Extension point
        $cat_form_buttons = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_BUTTONS', '', [
           'id' => $category_id,
           'clang' => $clang,
        ]));

        // Extension point
        $cat_form_edit = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_EDIT', '', [
            'id' => $category_id,
            'clang' => $clang,
            'category' => $sql,
            'catname' => $sql->getValue('catname'),
            'catpriority' => $sql->getValue('catpriority'),
            'data_colspan' => $data_colspan + 1,
        ]));

        return '  
            <div class="modal fade" id="category-edit-'.$category_id.'">
                <div class="modal-dialog">
                    <form id="rex-form-category-move-'.$category_id.'" class="modal-content form-vertical" action="'.$this->getUrl().'" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title">'.rex_i18n::msg('header_category').'</h3>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="rex-api-call" value="category_edit" />
                            <input type="hidden" name="category-id" value="'.$category_id.'" />
                            <div class="row">
                                <div class="col-xs-12">
                                    <dl class="rex-form-group form-group">
                                        <dt>'.rex_i18n::msg('header_id').'</dt>
                                        <dd>'.$category_id.'</dd>
                                    </dl>
                                    <dl class="rex-form-group form-group">
                                        <dt><label for="category-name">'.rex_i18n::msg('header_category').'</label></dt>
                                        <dd><input class="form-control rex-js-autofocus" type="text" name="category-name" value="'.htmlspecialchars($this->getSqlValue('catname')).'" autofocus /></dd>
                                    </dl>
                                    <dl class="rex-form-group form-group">
                                        <dt><label for="category-position">'.rex_i18n::msg('header_priority').'</label></dt>
                                        <dd><input class="form-control" type="text" name="category-position" value="'.htmlspecialchars($this->getSqlValue('catpriority')).'" /></dd>
                                    </dl>
                                    '.$cat_form_buttons.'
                                    '.$cat_form_edit.'
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-send" type="submit" name="category-edit-button" '.rex::getAccesskey(rex_i18n::msg('save_category'), 'save').'>'.rex_i18n::msg('save_category').'</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.rex_i18n::msg('form_abort').'</button>
                        </div>
                    </form>
                </div>
            </div> 
            <script>
                $(document).ready(function() {
                    $("#category-edit-'.$category_id.'").modal();
                });
            </script>
        ';
   }
}
