<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_string extends rex_structure_field
{
    /**
     * @var string
     */
    protected $string;

    /**
     * @param string $string
     *
     * @return $this
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        $string = $this->string;

        if (rex_i18n::hasMsg($string)) {
            $string = rex_i18n::msg($string);
        } elseif (rex_i18n::hasMsg('header_'.$string)) {
            $string = rex_i18n::msg('header_'.$string);
        }

        $field_params = [
            'label' => $string,
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getFragment($field_params);
    }
}
