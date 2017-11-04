<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_add extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '';
        }

        // Return button
        $url_params = array_merge($this->url_params, [
            'form_article_add' => $this->edit_id,
        ]);

        $button_params = [
            'button' => [
                'hidden_label' => rex_i18n::msg('article_add'),
                'icon' => 'add-article',
                'url' => $this->context->getUrl($url_params, false),
                'attributes' => [
                    'title' => rex_i18n::msg('article_add'),
                ],
            ],
        ];
        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['button']['attributes']['accesskey'] = $access_keys['add_2'];
            $button_params['button']['attributes']['title'] .= ' ['.$access_keys['add_2'].']';
        }

        $this->setVar('buttons', $button_params);

        $return = $this->parse('core/buttons/button.php');

        // Show form if necessary
        if (rex_request('form_article_add', 'int', -1) == $this->edit_id) {
            $return .= $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getModal()
    {
        $template_select = '';
        if (rex_addon::get('structure')->getPlugin('content')->isAvailable()) {
            $select = new rex_template_select();
            $select->setName('template_id');
            $select->setSize(1);
            $select->setStyle('class="form-control"');
            $select->setSelected();

            $template_select = '
                <dl class="rex-form-group form-group">
                    <dt><label for="article-name">'.rex_i18n::msg('header_template').'</label></dt>
                    <dd>'.$select->get().'</dd>
                </dl>
            ';
        }

        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('article_add'),
                'attributes' => [
                    'class' => [
                        'btn-save',
                    ],
                    'type' => 'submit',
                    'name' => 'submit',
                    'title' => rex_i18n::msg('article_add'),
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
            'modal_id' => 'article-add-'.$this->edit_id,
            'modal_url' => $this->context->getUrl($this->url_params),
            'modal_title' => rex_i18n::msg('article_add'),
            'modal_body' => '
                <input type="hidden" name="rex-api-call" value="article_add" />
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-name">'.rex_i18n::msg('header_article_name').'</label></dt>
                            <dd><input class="form-control" type="text" name="article-name" autofocus /></dd>
                        </dl>
                        '.$template_select.'
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_date').'</dt>
                            <dd>'.rex_formatter::strftime(time(), 'date').'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input id="article-position" class="form-control" type="text" name="article-position" value="'.($this->pager->getRowCount() + 1).'" /></dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/modal.php');
    }
}
