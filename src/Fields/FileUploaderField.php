<?php

namespace MyFormBuilder\Fields;

class FileUploaderField extends AbstractField
{
    public function render(): string
    {
        $readonly = $this->attributes['readonly'] ?? '';
        $value = $this->attributes['value'] ?? '';
        $uploadUrl = $this->attributes['upload_url'] ?? route('file.upload');
        $acceptedFiles = $this->attributes['accepted_files'] ?? '*';
        $fieldId = $this->name . '_file_uploader';
        $hiddenId = $this->name . '_file_url';

        $s = '<div class="file-uploader-wrapper">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if ($this->attributes['required'] == 'on' && $readonly != 'on') {
            $s .= ' <span class="required">*</span>';
        }
        $s .= '</label>';

        if ($value && is_string($value)) {
            $downloadUrl = str_contains($value, 'http') ? $value : url($value);
            $s .= '<div class="file-uploader-preview" id="' . $fieldId . '_preview">';
            $s .= '<div class="file-uploader-preview-header">';
            $s .= '<i class="fa fa-file-o file-uploader-preview-icon"></i>';
            $s .= '<span class="file-uploader-preview-name">' . basename($value) . '</span>';
            $s .= '</div>';
            $s .= '<div class="file-uploader-preview-actions">';
            $s .= '<a href="' . $downloadUrl . '" target="_blank" download class="file-uploader-download-link">';
            $s .= '<i class="fa fa-download"></i> ' . trans('fields.Download');
            $s .= '</a>';
            $s .= '</div>';
            $s .= '</div>';
        }

        if ($readonly == 'on') {
            $s .= '<div class="file-uploader-readonly">';
            $s .= e($value ?: 'بدون فایل');
            $s .= '</div>';
            $s .= '<input type="hidden" name="' . $this->name . '" id="' . $this->name . '" value="' . e($value) . '">';
        } else {
            $s .= '<div class="file-uploader-box" id="' . $fieldId . '_box">';
            $s .= '<i class="fa fa-cloud-upload-alt file-uploader-icon"></i>';
            $s .= '<span class="file-uploader-text">' . trans('fields.Click_to_upload_or_drag_file') . '</span>';
            $s .= '<span class="file-uploader-hint">' . trans('fields.Or_drag_and_drop_here') . '</span>';
            $s .= '<input type="file" id="' . $fieldId . '_input" class="file-uploader-input" accept="' . e($acceptedFiles) . '">';
            $s .= '<div class="file-uploader-progress" id="' . $fieldId . '_progress">';
            $s .= '<div class="file-uploader-progress-bar" id="' . $fieldId . '_progress_bar"></div>';
            $s .= '</div>';
            $s .= '<div class="file-uploader-message" id="' . $fieldId . '_message"></div>';
            $s .= '</div>';
            $s .= '<input type="hidden" name="' . $this->name . '" id="' . $this->name . '" ';
            if ($value) {
                $s .= 'value="' . e($value) . '" ';
            }
            $s .= '>';

            $s .= $css;

            $s .= '<script>';
            $s .= '(function() {';
            $s .= 'var box = document.getElementById("' . $fieldId . '_box");';
            $s .= 'var fileInput = document.getElementById("' . $fieldId . '_input");';
            $s .= 'var progressBox = document.getElementById("' . $fieldId . '_progress");';
            $s .= 'var progressBar = document.getElementById("' . $fieldId . '_progress_bar");';
            $s .= 'var messageBox = document.getElementById("' . $fieldId . '_message");';
            $s .= 'var hiddenInput = document.getElementById("' . $hiddenId . '");';
            $s .= 'var previewBox = document.getElementById("' . $fieldId . '_preview");';
            $s .= 'var uploadUrl = "' . $uploadUrl . '";';

            $s .= 'box.addEventListener("click", function() { fileInput.value = ""; fileInput.click(); });';
            $s .= 'box.addEventListener("dragover", function(e) { e.preventDefault(); box.classList.add("dragover"); });';
            $s .= 'box.addEventListener("dragleave", function() { box.classList.remove("dragover"); });';
            $s .= 'box.addEventListener("drop", function(e) {';
            $s .= 'e.preventDefault(); box.classList.remove("dragover");';
            $s .= 'var files = e.dataTransfer.files;';
            $s .= 'if (files.length) handleFile(files[0]);';
            $s .= '});';

            $s .= 'fileInput.addEventListener("change", function() {';
            $s .= 'if (fileInput.files.length) handleFile(fileInput.files[0]);';
            $s .= '});';

            $s .= 'function handleFile(file) {';
            $s .= 'messageBox.innerHTML = "";';
            $s .= 'messageBox.className = "file-uploader-message info";';
            $s .= 'messageBox.textContent = "در حال آپلود...";';
            $s .= 'progressBox.style.display = "block";';
            $s .= 'progressBar.style.width = "0%";';

            $s .= 'var formData = new FormData();';
            $s .= 'formData.append("file", file);';
            $s .= 'formData.append("field_name", "' . $this->name . '");';

            $s .= 'var xhr = new XMLHttpRequest();';
            $s .= 'xhr.open("POST", uploadUrl, true);';
            $s .= 'xhr.upload.onprogress = function(e) {';
            $s .= 'if (e.lengthComputable) {';
            $s .= 'var percent = Math.round((e.loaded / e.total) * 100);';
            $s .= 'progressBar.style.width = percent + "%";';
            $s .= '}';
            $s .= '};';
            $s .= 'xhr.onload = function() {';
            $s .= 'progressBox.style.display = "none";';
            $s .= 'if (xhr.status >= 200 && xhr.status < 300) {';
            $s .= 'try {';
            $s .= 'var response = JSON.parse(xhr.responseText);';
            $s .= 'if (response.url) {';
            $s .= 'hiddenInput.value = response.url;';
            $s .= 'if (previewBox) {';
            $s .= 'previewBox.innerHTML = \'<div class="file-uploader-preview-header"><i class="fa fa-file-o file-uploader-preview-icon"></i><span class="file-uploader-preview-name">\' + file.name + \'</span></div><div class="file-uploader-preview-actions"><a href="\' + response.url + \'" target="_blank" download class="file-uploader-download-link"><i class="fa fa-download"></i> \' + file.name + \'</a></div>\';';
            $s .= 'previewBox.style.display = "block";';
            $s .= '}';
            $s .= 'messageBox.innerHTML = "\' + trans("fields.Upload_successful") + \'";';
            $s .= 'messageBox.className = "file-uploader-message success";';
            $s .= '} else if (response.error) {';
            $s .= 'messageBox.innerHTML = response.error;';
            $s .= 'messageBox.className = "file-uploader-message error";';
            $s .= '}';
            $s .= '} catch (err) {';
            $s .= 'messageBox.innerHTML = "\' + trans("fields.Invalid_server_response") + \'";';
            $s .= 'messageBox.className = "file-uploader-message error";';
            $s .= '}';
            $s .= '} else {';
            $s .= 'messageBox.innerHTML = "\' + trans("fields.Upload_error") + \'";';
            $s .= 'messageBox.className = "file-uploader-message error";';
            $s .= '}';
            $s .= '};';
            $s .= 'xhr.onerror = function() {';
            $s .= 'progressBox.style.display = "none";';
            $s .= 'messageBox.innerHTML = "\' + trans("fields.Network_error") + \'";';
            $s .= 'messageBox.className = "file-uploader-message error";';
            $s .= '};';
            $s .= 'var csrfMeta = document.querySelector(\'meta[name="csrf-token"]\');';
            $s .= 'if (csrfMeta) xhr.setRequestHeader("X-CSRF-TOKEN", csrfMeta.content);';
            $s .= 'xhr.send(formData);';
            $s .= '}';
            $s .= '})();';
            $s .= '</script>';
        }

        $s .= '</div>';
        return $s;
    }
}