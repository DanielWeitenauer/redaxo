<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority_ext extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        if (!$article instanceof rex_article) {
            return '';
        }

        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $button_params = [
            'label' => htmlspecialchars($sql->getValue('priority')),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                    'rex-structure-prio-ext'
                ],
                'title' => rex_i18n::msg('header_priority'),
            ]
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($article_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'form_article_priority' => $article_id,
            ]);
            $button_params['url'] = $context->getUrl($url_params, false);
        }

        return $this->getButtonFragment($button_params);
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

        // Display form only if requested
        if (rex_request('form_article_priority', 'int', -1) != $article_id) {
            return '';
        }

        $button_params = [
            'label' => rex_i18n::msg('article_save'),
            'attributes' => [
                'class' => [
                    'btn-send',
                ],
                'type' => 'submit',
                'name' => 'artedit_function',
                'title' => rex_i18n::msg('article_save'),
            ],
        ];
        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['attributes']['accesskey'] = $access_keys['save'];
            $button_params['attributes']['title'] .= ' ['.$access_keys['save'].']';
        }
        $fragment_button = new rex_fragment([
            'buttons' => [
                'button' => $button_params,
            ],
        ]);

        $url_params = array_merge($this->getVar('url_params'), [
            'category_id' => $category_id,
            'article_id' => $article_id,
        ]);

        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-edit-'.$article_id,
            'modal_url' => $context->getUrl($url_params),
            'modal_title' => rex_i18n::msg('article_edit'),
            'modal_body' => '
                <input type="hidden" name="rex-api-call" value="article_edit" />
                <input type="hidden" name="article-name" value="'.htmlspecialchars($article->getName()).'" />
                <input type="hidden" name="template_id" value="'.htmlspecialchars($article->getValue('template_id')).'" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_id').'</dt>
                            <dd>'.$article_id.'</dd>
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
