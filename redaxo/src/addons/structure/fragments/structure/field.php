<?php
/**
 * Creates a structure field
 */

$field_params = $this->field_params;

if (!isset($field_params['attributes'])) {
    $field_params['attributes'] = [];
}
if (!isset($field_params['label'])) {
    $field_params['label'] = '';
}
if (isset($field_params['hidden_label']) && $field_params['hidden_label']) {
    $field_params['label'] = '<span class="sr-only">'.$field_params['label'].'</span>';
}

$icon = isset($field_params['icon']) ? '<i class="'.$field_params['icon'].'"></i>' : '';

$tag = 'span';
$href = '';
if (isset($field_params['url'])) {
    $tag = 'a';
    $href = ' href="'.$field_params['url'].'"';
}
if ($icon && $field_params['label']) {
    $icon = $icon.' ';
}

echo '<'.$tag.$href.rex_string::buildAttributes($field_params['attributes']).'>'.$icon.$field_params['label'].'</'.$tag.'>';
