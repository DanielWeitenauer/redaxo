<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_update_user extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);
        $user = $article->getValue('updateuser');

        return '<span>'.htmlspecialchars($user).'</span>';
    }
}
