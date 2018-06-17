<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_head extends rex_structure_field
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @param $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        $key = $this->key;

        if (rex_i18n::hasMsg($key)) {
            $key = rex_i18n::msg($key);
        } elseif (rex_i18n::hasMsg('header_'.$key)) {
            $key = rex_i18n::msg('header_'.$key);
        }

        $field_params = [
            'label' => htmlspecialchars($key),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getFragment($field_params);
    }
}
