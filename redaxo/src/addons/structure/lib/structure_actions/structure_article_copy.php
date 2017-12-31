<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_copy extends rex_structure_action_field
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

        if (!rex::getUser()->hasPerm('copyArticle[]')) {
            return '';
        }

        // Return button
        $url_params = array_merge($this->getVar('url_params'), [
            'form_article_copy' => $article_id,
            'article_id' => $article_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('copy_article'),
            'icon' => 'rex-icon fa-copy',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
            ],
        ];

        $return = $this->getButtonFragment($button_params);

        // Show form if necessary
        if (rex_request('form_article_copy', 'int', -1) == $article_id) {
            $return .= $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     * @throws rex_exception
     */
    protected function getModal()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $category_select = new rex_category_select(false, false, true, !rex::getUser()->getComplexPerm('structure')->hasMountPoints());
        $category_select->setName('category_copy_id_new');
        $category_select->setId('category_copy_id_new');
        $category_select->setSize('1');
        $category_select->setAttribute('class', 'form-control');
        $category_select->setSelected($category_id);

        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('content_submitcopyarticle'),
                'attributes' => [
                    'class' => [
                        'btn-send',
                    ],
                    'type' => 'submit',
                    'name' => 'submit',
                    'title' => rex_i18n::msg('content_submitcopyarticle'),
                    'data-confirm' => rex_i18n::msg('content_submitcopyarticle').'?',
                ],
            ],
        ];
        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['button']['attributes']['accesskey'] = $access_keys['save'];
            $button_params['button']['attributes']['title'] .= ' ['.$access_keys['save'].']';
        }
        $fragment_button = new rex_fragment(['buttons' => $button_params]);

        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-copy-'.$article_id,
            'modal_url' => $context->getUrl(),
            'modal_title' => rex_i18n::msg('content_submitcopyarticle'),
            'modal_body' => '
                <input type="hidden" name="rex-api-call" value="article_copy" />
                <input type="hidden" name="article_id" value="'.$article_id.'" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt><label for="category_copy_id_new">'.rex_i18n::msg('copy_article').'</label></dt>
                            <dd>'.$category_select->get().'</dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/structure_action_modal.php');
    }
}
