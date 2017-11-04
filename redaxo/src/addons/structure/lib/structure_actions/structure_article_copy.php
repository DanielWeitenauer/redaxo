<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_copy extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!rex::getUser()->hasPerm('copyArticle[]')) {
            return '';
        }

        // Return button
        $url_params = array_merge($this->url_params, [
            'form_article_copy' => $this->edit_id,
            'article_id' => $this->edit_id,
        ]);

        $button_params = [
            'button' => [
                'label' => '<i class="rex-icon fa-copy"></i><span class="sr-only">'.rex_i18n::msg('copy_article').'</span>',
                'url' => $this->context->getUrl($url_params, false),
                'attributes' => [
                    'class' => [
                        'btn-default',
                    ],
                    'title' => rex_i18n::msg('copy_article'),
                ],
            ],
        ];
        $this->setVar('buttons', $button_params, false);

        $return = $this->parse('core/buttons/button.php');

        // Show form if necessary
        if (rex_request('form_article_copy', 'int', -1) == $this->edit_id) {
            $return .= $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getModal()
    {
        $article = rex_article::get($this->edit_id);
        $category_id = $article->getCategoryId();

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
        $fragment_button = new self(['buttons' => $button_params]);

        $fragment_modal = new self([
            'modal_id' => 'article-copy-'.$this->edit_id,
            'modal_url' => $this->context->getUrl($this->url_params),
            'modal_title' => rex_i18n::msg('content_submitcopyarticle'),
            'modal_body' => '
                <input type="hidden" name="rex-api-call" value="article_copy" />
                <input type="hidden" name="article_id" value="'.$this->edit_id.'" />
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

        return $fragment_modal->parse('structure/modal.php');
    }
}
