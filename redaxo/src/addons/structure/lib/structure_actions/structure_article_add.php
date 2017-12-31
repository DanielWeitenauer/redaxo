<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_add extends rex_structure_action_field
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

        $url_params = array_merge($this->getVar('url_params'), [
            'form_article_add' => $category_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('article_add'),
            'icon' => 'rex-icon rex-icon-add-article',
            'url' => $this->getVar('context')->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn'.(!$this->getVar('hide_border') ? ' btn-default' : ''),
                ],
                'title' => rex_i18n::msg('article_add'),
            ],
        ];

        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $button_params['attributes']['accesskey'] = $access_keys['add_2'];
            $button_params['attributes']['title'] .= ' ['.$access_keys['add_2'].']';
        }

        $return = $this->getButtonFragment($button_params);

        // Show form if necessary
        if (rex_request('form_article_add', 'int', -1) == $category_id) {
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
        $category_id = $this->getVar('edit_id');

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }

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
        $fragment_button = new rex_fragment([
            'buttons' => $button_params
        ]);

        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-add-'.$category_id,
            'modal_url' => $this->getVar('context')->getUrl(),
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
                            <dd><input id="article-position" class="form-control" type="text" name="article-position" value="'.($this->getPagerRows() + 1).'" /></dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/structure_action_modal.php');
    }
}
