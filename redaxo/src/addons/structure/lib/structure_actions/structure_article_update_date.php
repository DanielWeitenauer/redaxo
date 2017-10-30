<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_update_date extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);
        $date = $article->getValue('updatedate');

        return '<span>'.rex_formatter::strftime($date, 'datetime').'</span>';
    }
}
