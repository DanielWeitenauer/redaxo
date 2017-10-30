<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_create_date extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);
        $create_date = $article->getValue('createdate');

        return '<span data-title="'.rex_i18n::msg('header_date').'">'.rex_formatter::strftime($create_date, 'datetime').'</span>';
    }
}
