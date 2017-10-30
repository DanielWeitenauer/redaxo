<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_id extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        return $this->sql->getValue('id');
    }
}
