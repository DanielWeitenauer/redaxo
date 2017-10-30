<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_status extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);
        $user = rex::getUser();

        $status_index = $article->getValue('status');
        $states = rex_article_service::statusTypes();
        $status = $states[$status_index][0];
        $status_class = $states[$status_index][1];
        $status_icon = $states[$status_index][2];

        if ($article->isStartArticle() || !$user->hasPerm('publishArticle[]') || !$user->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '<span class="btn '.$status_class.'" title="'.$status.'"><i class="rex-icon '.$status_icon.'"></i></a>';
        }

        $url_params = array_merge($this->url_params, [
            'rex-api-call' => 'article_status',
            'article_id' => $this->edit_id,
        ]);

        return '<a class="btn btn-default '.$status_class.'" href="'.$this->context->getUrl($url_params).'" title="'.$status.'"><i class="rex-icon '.$status_icon.'"></i></a>';
    }
}
