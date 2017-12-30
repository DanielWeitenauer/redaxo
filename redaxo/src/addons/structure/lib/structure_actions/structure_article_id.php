<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_id extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');

        $button_params = [
            'label' => htmlspecialchars($sql->getValue('id')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getButtonFragment($button_params);
    }
}
