<?php
/**
 * @package redaxo\structure
 */
class rex_structure_service
{
    /**
     * @param string $string
     * @return string
     */
    public static function escape($string)
    {
        $return = explode(',', $string);
        // Remove empty
        array_filter($return, function($item) {
            return trim($item);
        });
        array_walk($return, function(&$item) {
            $item ='`'.trim($item).'`';
        });

        return implode(',',$return);
    }

    /**
     * @param array $array
     * @return array
     */
    public static function normalizeArray(array $array)
    {
        return array_map(function($item) {
            if (!is_array($item)) {
                $item = [$item]; // (array) would transform the object
            }
            return $item;
        }, $array);
    }
}
