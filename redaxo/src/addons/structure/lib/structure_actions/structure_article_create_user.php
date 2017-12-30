<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_create_user extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);

        $button_params = [
            'label' => htmlspecialchars($article->getValue('createuser')),
            'attributes' => [
                'data-title' => rex_i18n::msg('header_user'),
            ],
        ];

        return $this->getButtonFragment($button_params);
    }
}
