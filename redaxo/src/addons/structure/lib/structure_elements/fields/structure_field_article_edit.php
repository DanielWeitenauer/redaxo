<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_edit extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $edit_id = $this->getVar('edit_id');
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');
        $function = rex_request('function', 'string');
        $article_id = rex_request('article_id', 'int');
        $article_id = rex_article::get($article_id) ? $article_id : 0;

        $field_params = [
            $this->hasVar('hidden_label') && $this->getVar('hidden_label') ? 'hidden_label' : 'label' => rex_i18n::msg('change'),
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
            $url_params = array_merge($this->getVar('url_params'), [
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
     * @throws rex_exception
     */
    protected function getForm()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();
        $sql = $this->getSql();
        /** @var rex_context $context */
        $context = $this->getVar('context');
        $url_params = array_merge($this->getVar('url_params'), [
            'category_id' => $category_id,
            'article_id' => $article_id,
        ]);

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

        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-edit-'.$article_id,
            'modal_title_id' => 'article-edit-title'.$article_id,
            'modal_url' => $context->getUrl($url_params, false),
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
                            <dd><input class="form-control" type="text" name="article-name" value="'.htmlspecialchars($sql->getValue('name')).'" autofocus /></dd>
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

    /**
     * @return rex_sql
     * @throws rex_sql_exception
     */
    protected function getSql()
    {
        if ($this->hasVar('sql') instanceof rex_sql) {
            return $this->getVar('sql');
        }

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM '.rex::getTable('article').' WHERE id = ?', [
            $this->getVar('edit_id')
        ]);

        return $sql;
    }
}
