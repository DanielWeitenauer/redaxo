<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_id extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        return $this->edit_id;
    }
}
