/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

var BASE_URL = 'http://127.0.0.1:8000'

// eslint-disable-next-line no-undef
CKEDITOR.editorConfig = function (config) {
  config.filebrowserBrowseUrl = `${BASE_URL}/laravel-filemanager?type=Files`
  config.filebrowserUploadUrl = `${BASE_URL}/laravel-filemanager/upload?type=Files&_token=`
  config.filebrowserImageBrowseUrl = `${BASE_URL}/laravel-filemanager?type=Images`
  config.filebrowserImageUploadUrl = `${BASE_URL}/laravel-filemanager/upload?type=Images&_token=`

  config.removeDialogTabs = 'link:upload;image:Upload'
  config.extraAllowedContent = 'iframe[*]'
}
