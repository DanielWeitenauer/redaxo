<?php
/**
 * Creates a structure field
 */

$field = $this->field;

if (!isset($field['attributes'])) {
    $field['attributes'] = [];
}
if (!isset($field['label'])) {
    $field['label'] = '';
}
if (isset($field['hidden_label'])) {
    $field['label'] = '<span class="sr-only">'.$field['hidden_label'].'</span>';
}

$icon = isset($field['icon']) ? '<i class="'.$field['icon'].'"></i>' : '';

$tag = 'span';
$href = '';
if (isset($field['url'])) {
    $tag = 'a';
    $href = ' href="'.$field['url'].'"';
}
if ($icon && $field['label']) {
    $icon = $icon.' ';
}

echo '<'.$tag.$href.rex_string::buildAttributes($field['attributes']).'>'.$icon.$field['label'].'</'.$tag.'>';
