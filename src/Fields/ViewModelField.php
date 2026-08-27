<?php

namespace MyFormBuilder\Fields;

use Behin\SimpleWorkflow\Models\Core\ViewModel;

class ViewModelField extends AbstractField
{
    public function render(): string
    {
        $id = $this->attributes['id'];
        $viewModelId = $this->attributes['view_model_id'];
        $style = $this->attributes['style'] ?? '';

        $s = "";

        /*
         * دریافت ViewModel
         */
        $viewModel = ViewModel::find($viewModelId);

        if (!$viewModel) {
            return '';
        }


        /*
         * ستون‌ها
         */
        $columns = explode(',', $viewModel->default_fields ?? '');


        /*
         * =========================================
         * تنظیمات Header
         * =========================================
         */


        /*
         * رنگ هدر
         *
         * اگر header_color وجود نداشته باشد
         * یا مقدار آن خالی باشد، رنگ پیش‌فرض
         * استفاده می‌شود.
         */
        $headerColor = $viewModel->getAttribute('header_color');

        if (empty($headerColor)) {
            $headerColor = '#f5f5f5';
        }


        /*
         * عنوان ViewModel
         *
         * اولویت:
         *
         * label
         * ↓
         * name
         */
        $title = $viewModel->getAttribute('label');

        if (empty($title)) {
            $title = trans('fields.' . $viewModel->name);
        }


        /*
         * =========================================
         * شروع Card
         * =========================================
         */

        $s .= "<div
            class='table-responsive card p-0'
            style='" . e($style) . "'
        >";


        /*
         * =========================================
         * Header
         * =========================================
         *
         * padding:0 باعث می‌شود دکمه‌ها
         * کاملاً به لبه چپ کارت بچسبند.
         */

        $s .= "<div
            class='card-header d-flex align-items-center'
            style='
                background-color: " . e($headerColor) . ";
                min-height: 50px;
                border-bottom: 1px solid rgba(0,0,0,.1);
                padding: 0;
            '
        >";


        /*
         * =========================================
         * عنوان - سمت راست
         * =========================================
         *
         * flex-grow-1 باعث می‌شود فضای باقی‌مانده
         * را بگیرد و دکمه‌ها به سمت چپ بروند.
         */

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


        /*
         * =========================================
         * دکمه‌ها - سمت چپ
         * =========================================
         */

        $s .= "<div
            class='d-flex align-items-stretch'
            style='
                align-self: stretch;
                direction: ltr;
            '
        >";


        /*
         * -----------------------------------------
         * دکمه ایجاد رکورد جدید
         *
         * محتوا توسط get_view_model_rows
         * بر اساس can_create ساخته می‌شود.
         * -----------------------------------------
         */

        if ($viewModel->allow_create_row) {

            $s .= "<div
                id='create-view-model-row-{$viewModel->id}'
                class='d-flex align-items-center'
            ></div>";
        }


        /*
         * -----------------------------------------
         * دکمه Refresh
         * -----------------------------------------
         */

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
            onclick='get_view_model_rows(
                \"{$viewModel->id}\",
                \"{$viewModel->api_key}\"
            )'
        >";

        $s .= "<i class='fa fa-refresh'></i>";

        $s .= "</button>";


        /*
         * پایان بخش دکمه‌ها
         */

        $s .= "</div>";


        /*
         * پایان Header
         */

        $s .= "</div>";


        /*
         * =========================================
         * Content
         * =========================================
         */

        if ($viewModel->show_as == 'table') {

            $s .= "<table
                class='table mb-0'
                id='{$viewModel->id}'
                style='width: 100%'
            >";

            /*
             * Table Header
             */

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


            /*
             * ستون عملیات
             */

            $s .= "<th
                style='border-top: 0;'
            ></th>";


            $s .= "</tr>";

            $s .= "</thead>";


            /*
             * Table Body
             */

            $s .= "<tbody></tbody>";


            /*
             * پایان Table
             */

            $s .= "</table>";

        } elseif ($viewModel->show_as == 'box') {

            /*
             * حالت Box
             */

            $s .= "<div
                id='{$viewModel->id}'
                style='width: 100%'
            ></div>";
        }


        /*
         * =========================================
         * پایان Card
         * =========================================
         */

        $s .= "</div>";


        /*
         * =========================================
         * دریافت اولیه رکوردها
         * =========================================
         */

        $s .= "<script>
            get_view_model_rows(
                `{$viewModel->id}`,
                `{$viewModel->api_key}`
            );
        </script>";


        return $s;
    }
}