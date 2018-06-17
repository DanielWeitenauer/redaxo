<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_edit extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $function = $this->getDataProvider()->getFunction();
        $article_id = $this->getDataProvider()->getArticleId();
        $user = rex::getUser();

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => rex_i18n::msg('change'),
            'icon' => 'rex-icon rex-icon-edit',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                #'data-toggle' => 'modal',
                #'data-target' => 'article-edit-'.$article_id,
            ],
        ];

        // Active state
        if ($user->getComplexPerm('structure')->hasCategoryPerm($edit_id)) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'article_id' => $edit_id,
                'function' => 'edit_art',
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
            $field_params['attributes']['class'][] = 'btn-default';
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        $return = $this->getFragment($field_params);
        // The form fields are injected after the field
        if ($function == 'edit_art' && $edit_id == $article_id && $user->getComplexPerm('structure')->hasCategoryPerm($edit_id)) {
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
        $sql = $this->getDataProvider()->getSql();

        // Save button
        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('article_save'),
                'attributes' => [
                    'class' => [
                        'btn-send',
                    ],
                    'type' => 'submit',
                    'name' => 'artedit_function',
                    'title' => rex_i18n::msg('article_save'),
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
            'modal_id' => 'article-edit-'.$edit_id,
            'modal_title_id' => 'article-edit-title-'.$edit_id,
            'modal_title' => rex_i18n::msg('article_edit'),
            'modal_body' => '
                '.rex_api_article_edit::getHiddenFields().'
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_id').'</dt>
                            <dd>'.$sql->getValue('id').'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-name">'.rex_i18n::msg('header_article_name').'</label></dt>
                            <dd><input class="form-control rex-js-autofocus" type="text" name="article-name" value="'.htmlspecialchars($sql->getValue('name')).'" autofocus /></dd>
                        </dl>
                        '.$this->getTemplateSelect($sql->getValue('template_id')).'
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_date').'</dt>
                            <dd>'.rex_formatter::strftime($sql->getDateTimeValue('createdate'), 'date').'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input class="form-control" type="text" name="article-position" value="'.htmlspecialchars($sql->getValue('priority')).'" /></dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/modal.php');
    }

    /**
     * @param int $selected_template_id
     *
     * @return string
     */
    protected function getTemplateSelect($selected_template_id)
    {
        if (!rex_addon::get('structure')->getPlugin('content')->isAvailable()) {
            return '';
        }

        $select = new rex_template_select();
        $select->setName('template_id');
        $select->setSize(1);
        $select->setStyle('class="form-control"');
        $select->setSelected($selected_template_id);

        return '
            <dl class="rex-form-group form-group">
                <dt><label for="article-name">'.rex_i18n::msg('header_template').'</label></dt>
                <dd>'.$select->get().'</dd>
            </dl>
        ';
    }
}
