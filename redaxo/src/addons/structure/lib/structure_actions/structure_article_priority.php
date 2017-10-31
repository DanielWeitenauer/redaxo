<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!$this->edit_id) {
            return '';
        }

        return '<span class="btn" data-title="'.rex_i18n::msg('header_priority').'">'.htmlspecialchars($this->sql->getValue('priority')).'</span>';
    }
}
