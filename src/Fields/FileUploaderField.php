<?php

namespace MyFormBuilder\Fields;

class FileUploaderField extends AbstractField
{
    public function render(): string
    {
        $readonly = $this->attributes['readonly'] ?? '';
        $value = $this->attributes['value'] ?? '';
        $uploadUrl = $this->attributes['upload_url'] ?? route('file-uploader.upload');
        $acceptedFiles = $this->attributes['accepted_files'] ?? '*';
        $fieldId = $this->name . '_file_uploader';
        $hiddenId = $this->name . '_file_url';

        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if ($this->attributes['required'] == 'on' && $readonly != 'on') {
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label><br>';

        if ($value && is_string($value)) {
            $downloadUrl = str_contains($value, 'http') ? $value : url($value);
            $s .= '<div class="file-uploader-preview" id="' . $fieldId . '_preview">';
            $s .= '<a href="' . $downloadUrl . '" target="_blank" download class="file-uploader-download-link">';
            $s .= '<i class="fa fa-download"></i> ' . trans('fields.Download') . ' ' . basename($value);
            $s .= '</a>';
            $s .= '</div><br>';
        }

        if ($readonly == 'on') {
            $s .= '<input type="hidden" name="' . $this->name . '" value="' . e($value) . '">';
        } else {
            $s .= '<div class="file-uploader-box" id="' . $fieldId . '_box">';
            $s .= '<div class="file-uploader-drop-zone" id="' . $fieldId . '_dropzone">';
            $s .= '<i class="fa fa-cloud-upload-alt"></i><br>';
            $s .= trans('fields.Click_to_upload_or_drag_file');
            $s .= '</div>';
            $s .= '<input type="file" id="' . $fieldId . '_input" accept="' . e($acceptedFiles) . '" style="display: none;">';
            $s .= '<div class="file-uploader-progress" id="' . $fieldId . '_progress" style="display: none;">';
            $s .= '<div class="file-uploader-progress-bar" id="' . $fieldId . '_progress_bar"></div>';
            $s .= '</div>';
            $s .= '<div class="file-uploader-message" id="' . $fieldId . '_message"></div>';
            $s .= '</div>';
            $s .= '<input type="hidden" name="' . $this->name . '" id="' . $hiddenId . '" ';
            if ($value) {
                $s .= 'value="' . e($value) . '" ';
            }
            $s .= '>';

            $s .= '<script>';
            $s .= '(function() {';
            $s .= 'var dropZone = document.getElementById("' . $fieldId . '_dropzone");';
            $s .= 'var fileInput = document.getElementById("' . $fieldId . '_input");';
            $s .= 'var progressBox = document.getElementById("' . $fieldId . '_progress");';
            $s .= 'var progressBar = document.getElementById("' . $fieldId . '_progress_bar");';
            $s .= 'var messageBox = document.getElementById("' . $fieldId . '_message");';
            $s .= 'var hiddenInput = document.getElementById("' . $hiddenId . '");';
            $s .= 'var previewBox = document.getElementById("' . $fieldId . '_preview");';
            $s .= 'var uploadUrl = "' . $uploadUrl . '";';

            $s .= 'dropZone.addEventListener("click", function() { fileInput.click(); });';
            $s .= 'dropZone.addEventListener("dragover", function(e) { e.preventDefault(); dropZone.classList.add("dragover"); });';
            $s .= 'dropZone.addEventListener("dragleave", function() { dropZone.classList.remove("dragover"); });';
            $s .= 'dropZone.addEventListener("drop", function(e) {';
            $s .= 'e.preventDefault(); dropZone.classList.remove("dragover");';
            $s .= 'var files = e.dataTransfer.files;';
            $s .= 'if (files.length) handleFile(files[0]);';
            $s .= '});';

            $s .= 'fileInput.addEventListener("change", function() {';
            $s .= 'if (fileInput.files.length) handleFile(fileInput.files[0]);';
            $s .= '});';

            $s .= 'function handleFile(file) {';
            $s .= 'messageBox.innerHTML = "";';
            $s .= 'progressBox.style.display = "block";';
            $s .= 'progressBar.style.width = "0%";';

            $s .= 'var formData = new FormData();';
            $s .= 'formData.append("file", file);';
            $s .= 'formData.append("field_name", "' . $this->name . '");';

            $s .= 'var xhr = new XMLHttpRequest();';
            $s .= 'xhr.open("POST", uploadUrl, true);';
            $s .= 'xhr.upload.onprogress = function(e) {';
            $s .= 'if (e.lengthComputable) {';
            $s .= 'var percent = (e.loaded / e.total) * 100;';
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
            $s .= 'previewBox.innerHTML = \'<a href="\' + response.url + \'" target="_blank" download class="file-uploader-download-link"><i class="fa fa-download"></i> \' + \'download\': \' + file.name + \'</a>\';';
            $s .= 'previewBox.style.display = "block";';
            $s .= '}';
            $s .= 'messageBox.innerHTML = \'<span style="color: green;">\' + \'Upload successful.\' + \'</span>\';';
            $s .= '} else if (response.error) {';
            $s .= 'messageBox.innerHTML = \'<span style="color: red;">\' + response.error + \'</span>\';';
            $s .= '}';
            $s .= '} catch (err) {';
            $s .= 'messageBox.innerHTML = \'<span style="color: red;">\' + \'Invalid server response.\' + \'</span>\';';
            $s .= '}';
            $s .= '} else {';
            $s .= 'messageBox.innerHTML = \'<span style="color: red;">\' + \'Upload error.\' + \'</span>\';';
            $s .= '}';
            $s .= '};';
            $s .= 'xhr.onerror = function() {';
            $s .= 'progressBox.style.display = "none";';
            $s .= 'messageBox.innerHTML = \'<span style="color: red;">\' + \'Network error.\' + \'</span>\';';
            $s .= '};';
            $s .= 'xhr.send(formData);';
            $s .= '}';
            $s .= '})();';
            $s .= '</script>';
        }

        $s .= '</div>';
        return $s;
    }
}