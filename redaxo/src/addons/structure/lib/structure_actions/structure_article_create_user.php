<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_create_user extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);
        $create_user = $article->getValue('createuser');

        return '<span data-title="'.rex_i18n::msg('header_user').'">'.htmlspecialchars($create_user).'</span>';
    }
}
