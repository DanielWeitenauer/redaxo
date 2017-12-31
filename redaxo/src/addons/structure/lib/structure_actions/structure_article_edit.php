<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_edit extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('edit'),
            'icon' => 'rex-icon rex-icon-edit',
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($article_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'form_article_edit' => $article_id,
            ]);
            $button_params['url'] = $context->getUrl($url_params, false);
            $button_params['attributes']['class'][] = 'btn-default';
        }

        $return = $this->getButtonFragment($button_params);

        return $return;
    }

    /**
     * @return string
     * @throws rex_exception
     */
    public function getModal()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }
        // Display form only if reuested
        if (rex_request('form_article_edit', 'int', -1) != $article_id) {
            return '';
        }

        $template_select = '';
        if (rex_addon::get('structure')->getPlugin('content')->isAvailable()) {
            $select = new rex_template_select();
            $select->setName('template_id');
            $select->setSize(1);
            $select->setStyle('class="form-control"');
            $select->setSelected($article->getValue('template_id'));

            $template_select = '
                <dl class="rex-form-group form-group">
                    <dt><label for="article-name">'.rex_i18n::msg('header_template').'</label></dt>
                    <dd>'.$select->get().'</dd>
                </dl>
            ';
        }

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

        $url_params = array_merge($this->getVar('url_params'), [
            'category_id' => $category_id,
            'article_id' => $article_id,
        ]);

        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-edit-'.$article_id,
            'modal_url' => $context->getUrl($url_params, false),
            'modal_title' => rex_i18n::msg('article_edit'),
            'modal_body' => '
                <input type="hidden" name="rex-api-call" value="article_edit" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_id').'</dt>
                            <dd>'.$article_id.'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-name">'.rex_i18n::msg('header_article_name').'</label></dt>
                            <dd><input class="form-control" type="text" name="article-name" value="'.htmlspecialchars($article->getName()).'" autofocus /></dd>
                        </dl>
                        '.$template_select.'
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_date').'</dt>
                            <dd>'.rex_formatter::strftime($article->getCreateDate(), 'date').'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input class="form-control" type="text" name="article-position" value="'.htmlspecialchars($article->getPriority()).'" /></dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/structure_action_modal.php');

    }
}
