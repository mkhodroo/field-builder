<?php

namespace MyFormBuilder\Fields;

use Behin\SimpleWorkflow\Models\Core\ViewModel;

class ViewModelFieldV2 extends AbstractField
{
    public function render(): string
    {
        $id = $this->attributes['id'];
        $viewModelId = $this->attributes['view_model_id'];
        $style = $this->attributes['style'] ?? '';
        $searchEnabled = $this->attributes['search'] ?? true;
        $paginationEnabled = $this->attributes['pagination'] ?? true;
        $perPage = (int)($this->attributes['per_page'] ?? 10);
        $searchPlaceholder = $this->attributes['search_placeholder'] ?? 'جست‌وجوی رکوردها...';

        $s = '';

        $viewModel = ViewModel::find($viewModelId);

        if (!$viewModel) {
            return '';
        }

        $columns = explode(',', $viewModel->default_fields ?? '');

        $headerColor = $viewModel->getAttribute('header_color');
        if (empty($headerColor)) {
            $headerColor = '#f5f5f5';
        }

        $title = $viewModel->getAttribute('label');
        if (empty($title)) {
            $title = trans('fields.' . $viewModel->name);
        }

        $s .= "<div
            class='table-responsive card p-0'
            style='" . e($style) . "'
        >";

        $s .= "<div
            class='card-header d-flex flex-wrap align-items-center'
            style='
                background-color: " . e($headerColor) . ";
                min-height: 50px;
                border-bottom: 1px solid rgba(0,0,0,.1);
                padding: 0;
            '
        >";

        $s .= "<div
            class='d-flex align-items-center flex-grow-1'
            style='padding: 0 15px;'
        >";
        $s .= "<h5
            class='mb-0 font-weight-bold'
            style='color: #333;'
        >";
        $s .= e($title);
        $s .= "</h5>";
        $s .= "</div>";

        $s .= "<div
            class='d-flex align-items-stretch'
            style='
                align-self: stretch;
                direction: ltr;
            '
        >";

        if ($viewModel->allow_create_row) {
            $s .= "<div
                id='create-view-model-row-{$viewModel->id}'
                class='d-flex align-items-center me-2'
            ></div>";
        }

        $refreshTitle = trans('fields.Refresh');
        if ($refreshTitle === 'fields.Refresh') {
            $refreshTitle = 'بروزرسانی';
        }

        $s .= "<button
            type='button'
            class='btn btn-secondary'
            title='" . e($refreshTitle) . "'
            style='
                border-radius: 0;
                min-width: 50px;
                border-top: 0;
                border-bottom: 0;
                border-left: 0;
            '
            onclick='get_view_model_rows_v2(
                `" . $viewModel->id . "`,
                `" . $viewModel->api_key . "`,
                1
            )'
        >";
        $s .= "<i class='fa fa-refresh'></i>";

        if ($searchEnabled) {
            $s .= "<div style='position: relative; min-width: 220px;'>";
            $s .= "<input
                type='text'
                id='search-input-{$viewModel->id}'
                class='form-control'
                style='border-radius: 0; border-top: 0; border-bottom: 0; border-left: 0; padding: 6px 10px;'
                placeholder='" . e($searchPlaceholder) . "'
                onkeydown='if(event.key===\"Enter\"){get_view_model_rows_v2(
                    `" . $viewModel->id . "`,
                    `" . $viewModel->api_key . "`,
                    1
                );}'
            >";
            $s .= "<button
                type='button'
                class='btn btn-primary'
                style='border-radius: 0; border-top: 0; border-bottom: 0; border-left: 0; position: absolute; right: 0; top: 0; height: 100%;'
                onclick='get_view_model_rows_v2(
                    `" . $viewModel->id . "`,
                    `" . $viewModel->api_key . "`,
                    1
                )'
            >";
            $s .= "<i class='fa fa-search'></i>";
            $s .= "</button>";
            $s .= "</div>";

            $s .= "<div id='search-reset-{$viewModel->id}' style='display:none; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: #f8f9fa; border: 1px solid #ddd; border-radius: 3px; padding: 2px 8px; cursor: pointer;' onclick='clearSearch({$viewModel->id}, `" . $viewModel->api_key . "`)'>
                <i class='fa fa-times'></i>
            </div>";
        }

        $s .= "</div>";
        $s .= "</div>";

        if ($viewModel->show_as == 'table') {
            $s .= "<table
                class='table mb-0'
                id='{$viewModel->id}'
                style='width: 100%'
            >";

            $s .= "<thead>";
            $s .= "<tr>";

            foreach ($columns as $column) {
                $column = trim($column);
                if (empty($column)) {
                    continue;
                }

                $columnLabel = trans('fields.' . $column);

                $s .= "<th
                    style='
                        border-top: 0;
                        border-left: solid gray 1px;
                    '
                >";
                $s .= e($columnLabel);
                $s .= "</th>";
            }

            $s .= "<th
                style='border-top: 0;'
            ></th>";

            $s .= "</tr>";
            $s .= "</thead>";

            $s .= "<tbody id='table-body-{$viewModel->id}'></tbody>";

            $s .= "<div id='pagination-{$viewModel->id}' style='padding: 10px; border-top: 1px solid #eee;'>";
            $s .= "<div class='d-flex align-items-center justify-content-between'>";
            $s .= "<span class='text-muted small mb-0'>";
            $s .= "موردی یافت نشد";
            $s .= "</span>";
            $s .= "<nav style='display: inline-block;'>";
            $s .= "<ul class='pagination mb-0'>";
            $s .= "</ul>";
            $s .= "</nav>";
            $s .= "</div>";
            $s .= "</div>";

            $s .= "</table>";

        } elseif ($viewModel->show_as == 'box') {
            $s .= "<div
                id='{$viewModel->id}'
                style='width: 100%'
            ></div>";
        }

        $s .= "</div>";

        $s .= "<script>
            get_view_model_rows_v2(
                `" . $viewModel->id . "`,
                `" . $viewModel->api_key . "`,
                1
            );
        </script>";

        return $s;
    }
}
        $s .= "</button>";