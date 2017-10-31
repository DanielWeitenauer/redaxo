<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority_inc extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!$this->edit_id || !rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '';
        }

        $old_priority = $this->sql->getValue('priority');
        $new_priority = $old_priority + 1;

        $url_params = array_merge($this->url_params, [
            'rex-api-call' => 'article_priority',
            'article_id' => $this->edit_id,
            'old_priority' => $old_priority,
            'new_priority' => $new_priority,
        ]);

        $return = '<a href="'.$this->context->getUrl($url_params).'" class="btn btn-default"><i class="fa fa-arrow-down"></i></a>';

        return $return;
    }
}
