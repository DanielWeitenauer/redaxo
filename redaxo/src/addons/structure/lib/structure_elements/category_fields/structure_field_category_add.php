<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_add extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $category_permission = $this->getDataProvider()->getCategoryPermission();
        $function = $this->getDataProvider()->getFunction();

        if (!$category_permission) {
            return '';
        }

        $context = $this->getDataProvider()->getContext();
        $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
            'function' => 'add_cat',
        ]);

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => rex_i18n::msg('add_category'),
            'icon' => 'rex-icon rex-icon-add-category',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('add_category'),
            ],
        ];

        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $field_params['attributes']['accesskey'] = $access_keys['add'];
            $field_params['attributes']['title'] .= ' ['.$access_keys['add'].']';
        }

        $return = $this->getFragment($field_params);
        if ($function == 'add_cat') {
            $return .= $this->getForm();
        }

        return $return;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        $category_id = $this->getDataProvider()->getCategoryId();
        $clang = $this->getDataProvider()->getClangId();
        $pager = $this->getDataProvider()->getCategoryPager();

        $data_colspan = 5; // Only for BC reasons

        // EXTENSION POINT
        $meta_buttons = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_BUTTONS', '', [
            'id' => $category_id,
            'clang' => $clang,
        ]));

        // EXTENSION POINT
        $cat_form_add = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_ADD', '', [
            'id' => $category_id,
            'clang' => $clang,
            'data_colspan' => ($data_colspan + 1),
        ]));

        // Save button
        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('add_category'),
                'attributes' => [
                    'class' => [
                        'btn btn-save',
                    ],
                    'type' => 'submit',
                    'name' => 'category-add-button',
                    'title' => rex_i18n::msg('add_category'),
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
            'modal_id' => 'category-add-'.$category_id,
            'modal_title_id' => 'category-edit-title-'.$category_id,
            'modal_title' => rex_i18n::msg('header_category'),
            'modal_body' => '
                '.rex_api_category_add::getHiddenFields().'
                <input type="hidden" name="parent-category-id" value="'.$category_id.'" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt><label for="category-name">'.rex_i18n::msg('header_category').'</label></dt>
                            <dd><input id="category-name" class="form-control rex-js-autofocus" type="text" name="category-name" autofocus /></dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="category-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input id="category-position" class="form-control" type="text" name="category-position" value="'.($pager->getRowCount() + 1).'" /></dd>
                        </dl>
                        '.$meta_buttons.'
                        '.$cat_form_add.'
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/modal.php');
    }
}
