<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_edit extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $function = $this->getDataProvider()->getFunction();
        $category_id = $this->getDataProvider()->getCategoryId();

        $sql = $this->getDataProvider()->getSql();
        $category_active_id = $sql->getValue('id');

        $user = rex::getUser();
        $category_permission = $this->getDataProvider()->getCategoryPermission();

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => rex_i18n::msg('change'),
            'icon' => 'rex-icon rex-icon-edit',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('change'),
            ],
        ];

        if ($category_permission) {
            // Active state
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'edit_id' => $category_active_id,
                'function' => 'edit_cat',
                'catstart' => $this->getDataProvider()->getCatStart(),
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
            $field_params['attributes']['class'][] = 'btn-default';
        } elseif (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_active_id)) {
            // Inactive state
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        $return = $this->getFragment($field_params);
        // The form fields are injected after the field
        if ($category_permission && $function == 'edit_cat' && $edit_id == $category_active_id) {
            $return .= $this->getForm();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getForm()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $clang = $this->getDataProvider()->getClangId();

        $sql = $this->getDataProvider()->getSql();
        $category_active_id = $sql->getValue('id');
        $cat_name = $sql->getValue('catname');
        $cat_priority = $sql->getValue('catpriority');

        $data_colspan = 5; // Only for BC reasons

        // Extension point
        $meta_buttons = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_BUTTONS', '', [
           'id' => $edit_id,
           'clang' => $clang,
        ]));

        // Extension point
        $cat_form_edit = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_EDIT', '', [
            'id' => $edit_id,
            'clang' => $clang,
            'category' => $sql,
            'catname' => $cat_name,
            'catpriority' => $cat_priority,
            'data_colspan' => $data_colspan + 1,
        ]));

        // Save button
        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('save_category'),
                'attributes' => [
                    'class' => [
                        'btn-send',
                    ],
                    'type' => 'submit',
                    'name' => 'category-edit-button',
                    'title' => rex_i18n::msg('save_category'),
                ],
            ],
        ];

        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['button']['attributes']['accesskey'] = $access_keys['save'];
            $button_params['button']['attributes']['title'] .= ' ['.$access_keys['save'].']';
        }

        $fragment_button = new rex_fragment([
            'buttons' => $button_params,
        ]);

        // Modal
        $fragment_modal = new rex_fragment([
            'modal_id' => 'category-edit-'.$edit_id,
            'modal_title_id' => 'category-edit-title-'.$edit_id,
            'modal_title' => rex_i18n::msg('header_category'),
            'modal_body' => '
                '.rex_api_category_edit::getHiddenFields().'
                <input type="hidden" name="category-id" value="'.$edit_id.'" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_id').'</dt>
                            <dd>'.$category_active_id.'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="category-name">'.rex_i18n::msg('header_category').'</label></dt>
                            <dd><input class="form-control rex-js-autofocus" type="text" name="category-name" value="'.htmlspecialchars($cat_name).'" autofocus /></dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="category-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input class="form-control" type="text" name="category-position" value="'.htmlspecialchars($cat_priority).'" /></dd>
                        </dl>
                        '.$meta_buttons.'
                        '.$cat_form_edit.'
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/modal.php');
   }
}
