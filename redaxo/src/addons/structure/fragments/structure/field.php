<?php
/**
 * Creates a structure field
 */

if (!isset($this->field['attributes'])) {
    $this->field['attributes'] = [];
}
if (!isset($this->field['label'])) {
    $this->field['label'] = '';
}
if (isset($this->field['hidden_label'])) {
    $this->field['label'] = '<span class="sr-only">'.$this->field['hidden_label'].'</span>';
}

$icon = isset($this->field['icon']) ? '<i class="'.$this->field['icon'].'"></i>' : '';

$tag = 'span';
$href = '';
if (isset($this->field['url'])) {
    $tag = 'a';
    $href = ' href="'.$this->field['url'].'"';
}
if ($icon && $this->field['label']) {
    $icon = $icon.' ';
}

echo '<'.$tag.$href.rex_string::buildAttributes($this->field['attributes']).'>'.$icon.$this->field['label'].'</'.$tag.'>';
