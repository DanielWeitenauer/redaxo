<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_add extends rex_structure_field
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
            'function' => 'add_art',
        ]);

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => rex_i18n::msg('article_add'),
            'icon' => 'rex-icon rex-icon-add-article',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('article_add'),
            ],
        ];

        if (rex::getProperty('use_accesskeys')) {
            $access_keys = (array) rex::getProperty('accesskeys', []);
            $field_params['attributes']['accesskey'] = $access_keys['add_2'];
            $field_params['attributes']['title'] .= ' ['.$access_keys['add_2'].']';
        }

        $return = $this->getFragment($field_params);
        if ($function == 'add_art') {
            $return .= $this->getForm();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getForm()
    {
        $category_id = $this->getDataProvider()->getCategoryId();
        $artpager = $this->getDataProvider()->getArtPager();

        // Button fragment
        $button_params = [
            'button' => [
                'label' => rex_i18n::msg('article_add'),
                'attributes' => [
                    'class' => [
                        'btn-save',
                    ],
                    'type' => 'submit',
                    'name' => 'artadd_function',
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

        // Modal fragment
        $fragment_modal = new rex_fragment([
            'modal_id' => 'article-add-'.$category_id,
            'modal_title_id' => 'article-add-title-'.$category_id,
            'modal_title' => rex_i18n::msg('article_add'),
            'modal_body' => '
                '.rex_api_article_add::getHiddenFields().'
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-name">'.rex_i18n::msg('header_article_name').'</label></dt>
                            <dd><input class="form-control" type="text" name="article-name" autofocus /></dd>
                        </dl>
                        '.$this->getTemplateSelect().'
                        <dl class="rex-form-group form-group">
                            <dt>'.rex_i18n::msg('header_date').'</dt>
                            <dd>'.rex_formatter::strftime(time(), 'date').'</dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label for="article-position">'.rex_i18n::msg('header_priority').'</label></dt>
                            <dd><input id="article-position" class="form-control" type="text" name="article-position" value="'.($artpager->getRowCount() + 1).'" /></dd>
                        </dl>
                    </div>
                </div>
            ',
            'modal_button' => $fragment_button->parse('core/buttons/button.php'),
        ]);

        return $fragment_modal->parse('structure/modal.php');
    }
}
