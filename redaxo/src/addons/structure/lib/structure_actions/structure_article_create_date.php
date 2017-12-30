<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_create_date extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $date = $article->getValue('createdate');

        $button_params = [
            'label' => rex_formatter::strftime($date, 'datetime'),
            'attributes' => [
                'data-title' => rex_i18n::msg('header_date'),
            ],
        ];

        return $this->getButtonFragment($button_params);
    }
}
