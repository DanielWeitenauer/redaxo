<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_status extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $user = rex::getUser();

        if (!$this->edit_id || !$user->hasPerm('publishCategory[]') || !$user->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '';
        }

        $article = rex_article::get($this->edit_id);

        $status_index = (int) $article->isOnline();
        $states = rex_category_service::statusTypes();
        $status = $states[$status_index][0];
        $status_class = $states[$status_index][1];
        $status_icon = $states[$status_index][2];

        $url_params = array_merge($this->url_params, [
            'rex-api-call' => 'category_status',
            'category-id' => $this->edit_id,
        ]);

        return '<a class="btn btn-default '.$status_class.'" href="'.$this->context->getUrl($url_params).'" title="'.$status.'"><i class="rex-icon '.$status_icon.'"></i></a>';
    }
}
