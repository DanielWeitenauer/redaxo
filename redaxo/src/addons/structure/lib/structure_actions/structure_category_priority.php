<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_priority extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!$this->edit_id) {
            return '';
        }

        $category_priority = rex_category::get($this->edit_id)->getPriority();

        return '<span class="btn" data-title="'.rex_i18n::msg('header_priority').'">'.htmlspecialchars($category_priority).'</span>';
    }
}
