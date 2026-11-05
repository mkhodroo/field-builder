<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class SelectMultipleField extends AbstractField
{
    public function render(): string
{
    $s = '<div class="form-group">';
    $s .= '<label>';
    $s .= trans('fields.' . $this->name);

    if (
        ($this->attributes['required'] ?? '') == 'on' &&
        ($this->attributes['readonly'] ?? '') != 'on'
    ) {
        $s .= ' <span class="text-danger">*</span>';
    }

    $s .= '</label>';

    // اضافه شدن [] به نام فیلد برای دریافت آرایه در PHP
    $fieldName = htmlspecialchars($this->name . '[]', ENT_QUOTES, 'UTF-8');

    $s .= '<select name="' . $fieldName . '" class="form-control select2" multiple="multiple" ';

    foreach ($this->attributes as $key => $value) {

        if ($key == 'required') {
            if ($value == 'on') {
                $s .= 'required ';
            }
        } elseif ($key == 'readonly') {
            if ($value == 'on') {
                $s .= 'disabled ';
            }
        } elseif ($key == 'value' || $key == 'query' || $key == 'options') {
            continue;
        } else {
            $s .= htmlspecialchars($key, ENT_QUOTES, 'UTF-8')
                . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '" ';
        }
    }

    $s .= '>';

    // مقادیر انتخاب‌شده
    $values = $this->attributes['value'] ?? [];

    if (!is_array($values)) {
        $values = [$values];
    }

    // گزینه‌ها از طریق Query
    if (!empty($this->attributes['query'])) {

        $sqlOptions = DB::select($this->attributes['query']);

        foreach ($sqlOptions as $option) {

            $value = (string)$option->value;
            $label = (string)$option->label;

            $selected = in_array($value, $values) ? 'selected' : '';

            $s .= '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" '
                . $selected . '>'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
    }

    // گزینه‌های دستی
    if (!empty($this->attributes['options']) && is_string($this->attributes['options'])) {

        $options = explode("\r\n", $this->attributes['options']);

        foreach ($options as $option) {

            $option = trim($option);

            $selected = in_array($option, $values) ? 'selected' : '';

            $s .= '<option value="' . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . '" '
                . $selected . '>'
                . htmlspecialchars($option, ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
    }

    $s .= '</select>';
    $s .= '</div>';

    return $s;
}
}
