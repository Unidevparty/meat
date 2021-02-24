<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_downloads_front_submit extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction bulkForm( $form, $category ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->pageHeader( \\IPS\\Member::loggedIn()->language()->addToStack('submit_bulk_information') );\n$return .= <<<CONTENT\n\n<hr class='ipsHr'>\n\n\nCONTENT;\n\nif ( $form->error ):\n$return .= <<<CONTENT\n\n\t<div class=\"ipsMessage ipsMessage_error\">\n\t\t\nCONTENT;\n$return .= htmlspecialchars( $form->error, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t<\/div>\n\t<br>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\n<form accept-charset='utf-8' method=\"post\" action=\"\nCONTENT;\n$return .= htmlspecialchars( $form->action, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" enctype=\"multipart\/form-data\" id='elDownloadsSubmit' data-ipsForm data-ipsFormSubmit>\n\t<input type=\"hidden\" name=\"\nCONTENT;\n$return .= htmlspecialchars( $form->id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n_submitted\" value=\"1\">\n\t\nCONTENT;\n\nforeach ( $form->hiddenValues as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t<input type=\"hidden\" name=\"\nCONTENT;\n$return .= htmlspecialchars( $k, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" value=\"\nCONTENT;\n$return .= htmlspecialchars( $v, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\n\t\nCONTENT;\n\nforeach ( $form->elements as $fileName => $collection ):\n$return .= <<<CONTENT\n\n\t\t<h2 class='ipsType_sectionTitle ipsType_reset'>\nCONTENT;\n$return .= htmlspecialchars( $fileName, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/h2>\n\t\t<div class='ipsAreaBackground ipsPad_half'>\n\t\t\t<div class='ipsAreaBackground_reset ipsPad'>\n\t\t\t\t<ul class='ipsForm ipsForm_vertical'>\n\t\t\t\t\t\nCONTENT;\n\nforeach ( $collection as $input ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $input instanceof \\IPS\\Helpers\\Form\\FormAbstract ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t{$input}\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t\t<\/ul>\n\t\t\t<\/div>\n\t\t<\/div>\n\t\t<br><hr class='ipsHr'><br>\n\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\n\t<div class='ipsType_right'>\n\t\t<button type='submit' class='ipsButton ipsButton_large ipsButton_primary' data-role='submitForm'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'save_and_submit_files', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/button>\n\t<\/div>\n<\/form>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction categorySelector( $form ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( !\\IPS\\Request::i()->isAjax() ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->pageHeader( \\IPS\\Member::loggedIn()->language()->addToStack('select_category') );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t<div class='ipsPad'>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t{$form}\n\nCONTENT;\n\nif ( \\IPS\\Request::i()->isAjax() ):\n$return .= <<<CONTENT\n\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction editDetailsInfo( $file ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsMessage ipsMessage_info'><a href='\nCONTENT;\n$return .= htmlspecialchars( $file->url()->setQueryString( array( 'do' => 'newVersion' ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'upload_new_version_title', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'file_versioning_info_text', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a><\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction linkedScreenshotField( $name, $value ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div data-controller=\"downloads.front.submit.linkedScreenshots\" data-name='\nCONTENT;\n$return .= htmlspecialchars( $name, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-initialValue='\nCONTENT;\n\n$return .= htmlspecialchars( json_encode( $value ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\n\t<ul data-role=\"fieldsArea\" class=\"ipsList_reset\"><\/ul>\n\t<a class=\"ipsButton ipsButton_light ipsButton_small\" data-action=\"addField\"><i class=\"fa fa-plus-circle\"><\/i> \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'stack_add', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction newVersion( $form, $versioning ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsPageHeader ipsSpacer_bottom'>\n\t<h1 class='ipsType_pageTitle'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'upload_new_version', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h1>\n\t\nCONTENT;\n\nif ( $versioning and !\\IPS\\Member::loggedIn()->group['idm_bypass_revision'] ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsLayout_contentSection ipsType_textBlock ipsType_normal'>\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'new_version_versioning', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t\t<br>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/div>\n\n{$form}\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction submitForm( $form, $category, $terms, $bulk=0, $postingInformation='' ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\n$nonInfoFields = array('files', 'import_files', 'url_files', 'screenshots', 'url_screenshots');\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$step = 1;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( $bulk ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->pageHeader( \\IPS\\Member::loggedIn()->language()->addToStack( 'submit_form_desc_bulk', TRUE, array( 'sprintf' => $category->_title ) ) );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->pageHeader( \\IPS\\Member::loggedIn()->language()->addToStack( 'submit_form_desc', TRUE, array( 'sprintf' => $category->_title ) ) );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( $postingInformation ):\n$return .= <<<CONTENT\n\n\t{$postingInformation}\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n<hr class='ipsHr'>\n\n\nCONTENT;\n\nif ( $form->error ):\n$return .= <<<CONTENT\n\n\t<div class=\"ipsMessage ipsMessage_error\">\n\t\t\nCONTENT;\n$return .= htmlspecialchars( $form->error, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t<\/div>\n\t<br>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\n<form accept-charset='utf-8' method=\"post\" action=\"\nCONTENT;\n$return .= htmlspecialchars( $form->action, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" id='elDownloadsSubmit' enctype=\"multipart\/form-data\" \nCONTENT;\n\nif ( $category->bitoptions['allowss'] AND $category->bitoptions['reqss'] ):\n$return .= <<<CONTENT\ndata-screenshotsReq='1'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $bulk ):\n$return .= <<<CONTENT\ndata-bulkUpload='1'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-ipsForm data-ipsFormSubmit data-controller='downloads.front.submit.main'>\n\t<input type=\"hidden\" name=\"\nCONTENT;\n$return .= htmlspecialchars( $form->id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n_submitted\" value=\"1\">\n\t\nCONTENT;\n\nforeach ( $form->hiddenValues as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t<input type=\"hidden\" name=\"\nCONTENT;\n$return .= htmlspecialchars( $k, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" value=\"\nCONTENT;\n$return .= htmlspecialchars( $v, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\n\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t<div class='ipsColumn ipsColumn_veryNarrow ipsType_center'>\n\t\t\t<span class='cDownloadsSubmit_step'>\nCONTENT;\n$return .= htmlspecialchars( $step, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$step++;\n$return .= <<<CONTENT\n<\/span>\n\t\t<\/div>\n\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\t<div class='ipsBox'>\n\t\t\t\t<h3 class='ipsType_sectionTitle ipsType_reset'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'select_your_files', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\t<div class='ipsAreaBackground ipsPad ipsClearfix'>\n\t\t\t\t\t\nCONTENT;\n\nif ( isset( $form->elements['']['import_files'] ) || isset( $form->elements['']['url_files'] ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<ul class='ipsList_inline ipsClearfix ipsType_right'>\n\t\t\t\t\t\t\t\nCONTENT;\n\nif ( isset( $form->elements['']['url_files'] ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_veryLight ipsButton_verySmall' id='elURLFiles' data-ipsMenu data-ipsMenu-closeOnClick='false' data-ipsMenu-appendTo='#elDownloadsSubmit'>\n\t\t\t\t\t\t\t\t\t\t<i class='fa fa-globe'><\/i>&nbsp; \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'add_files_by_url', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <i class='fa fa-caret-down'><\/i>\n\t\t\t\t\t\t\t\t\t\t<span class='ipsNotificationCount \nCONTENT;\n\nif ( !\\count( $form->elements['']['url_files']->value ) ):\n$return .= <<<CONTENT\nipsHide\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-role='fileCount'>\nCONTENT;\n\n$return .= htmlspecialchars( \\count( $form->elements['']['url_files']->value ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t\t\t\t<div id='elURLFiles_menu' class='ipsMenu ipsMenu_normal ipsHide ipsPad'>\n\t\t\t\t\t\t\t\t\t\t<ul class='ipsFieldRow_fullWidth'>\n\t\t\t\t\t\t\t\t\t\t\t{$form->elements['']['url_files']}\n\t\t\t\t\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\t\t\t\t\t<hr class='ipsHr'>\n\t\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_fullWidth ipsButton_important' data-action='confirmUrls'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'submit_menu_confirm', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\t\t<\/li>\n\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\nCONTENT;\n\nif ( isset( $form->elements['']['import_files'] ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_veryLight ipsButton_verySmall' id='elImportFiles' data-ipsMenu data-ipsMenu-closeOnClick='false' data-ipsMenu-appendTo='#elDownloadsSubmit'>\n\t\t\t\t\t\t\t\t\t\t<i class='fa fa-folder'><\/i>&nbsp; \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'add_files_by_path', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <i class='fa fa-caret-down'><\/i>\n\t\t\t\t\t\t\t\t\t\t<span class='ipsNotificationCount \nCONTENT;\n\nif ( !\\count( $form->elements['']['import_files']->value ) ):\n$return .= <<<CONTENT\nipsHide\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-role='fileCount'>\nCONTENT;\n\n$return .= htmlspecialchars( \\count( $form->elements['']['import_files']->value ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t\t\t\t<div id='elImportFiles_menu' class='ipsMenu ipsMenu_normal ipsHide ipsPad'>\n\t\t\t\t\t\t\t\t\t\t<ul class='ipsFieldRow_fullWidth'>\n\t\t\t\t\t\t\t\t\t\t\t{$form->elements['']['import_files']}\n\t\t\t\t\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\t\t\t\t\t<hr class='ipsHr'>\n\t\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_fullWidth ipsButton_important' data-action='confirmImports'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'submit_menu_confirm', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\t\t<\/li>\n\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t<div id='elDownloadsSubmit_progress' class='ipsClear' data-ipsSticky>\n\t\t\t\t\t\t<div class='ipsProgressBar ipsProgressBar_animated ipsClear' >\n\t\t\t\t\t\t\t<div class='ipsProgressBar_progress' data-progress='0%'><\/div>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div id='elDownloadsSubmit_uploader' class='ipsClear'>\n\t\t\t\t\t\t{$form->elements['']['files']->html( $form )}\n\t\t\t\t\t\t<button type='button' class='ipsButton ipsButton_veryLight ipsButton_verySmall ipsHide' data-action='uploadMore'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'upload_more_files', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/button>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\t\t<\/div>\n\t<\/div>\n\n\t\nCONTENT;\n\nif ( !$bulk  ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( $category->bitoptions['allowss']  ):\n$return .= <<<CONTENT\n\n\t\t\t<div id='elDownloadsSubmit_screenshots'>\n\t\t\t\t<br><br>\n\t\t\t\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t\t\t\t<div class='ipsColumn ipsColumn_veryNarrow ipsType_center'>\n\t\t\t\t\t\t<span class='cDownloadsSubmit_step'>\nCONTENT;\n$return .= htmlspecialchars( $step, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$step++;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\t\t\t\t<div class='ipsBox'>\n\t\t\t\t\t\t\t<h3 class='ipsType_sectionTitle ipsType_reset'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'add_screenshots', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\t\t\t\t<div class='ipsAreaBackground ipsPad_half'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( isset( $form->elements['']['url_screenshots'] ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<ul class='ipsList_inline ipsClearfix ipsType_right'>\n\t\t\t\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_veryLight ipsButton_verySmall' id='elURLScreenshots' data-ipsMenu data-ipsMenu-closeOnClick='false' data-ipsMenu-appendTo='#elDownloadsSubmit_screenshots'>\n\t\t\t\t\t\t\t\t\t\t\t\t<i class='fa fa-globe'><\/i>&nbsp; \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'add_screenshots_by_url', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <i class='fa fa-caret-down'><\/i>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class='ipsNotificationCount \nCONTENT;\n\nif ( $form->elements['']['url_screenshots']->value === NULL OR !\\count( $form->elements['']['url_screenshots']->value ) ):\n$return .= <<<CONTENT\nipsHide\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-role='fileCount'>\nCONTENT;\n\n$return .= htmlspecialchars( \\count( $form->elements['']['url_screenshots']->value ?: array() ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t\t\t\t\t\t<div id='elURLScreenshots_menu' class='ipsMenu ipsMenu_wide ipsHide ipsPad'>\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class='ipsFieldRow_fullWidth'>\n\t\t\t\t\t\t\t\t\t\t\t\t\t{$form->elements['']['url_screenshots']}\n\t\t\t\t\t\t\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\t\t\t\t\t\t\t<hr class='ipsHr'>\n\t\t\t\t\t\t\t\t\t\t\t\t<a href='#' class='ipsButton ipsButton_fullWidth ipsButton_important' data-action='confirmScreenshotUrls'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'submit_menu_confirm', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\t\t\t\t<\/li>\n\t\t\t\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( isset( $form->elements['']['screenshots'] ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t<div id='elDownloadsSubmit_screenshots'>\n\t\t\t\t\t\t\t\t\t{$form->elements['']['screenshots']->html( $form )}\n\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\t\t<div id='elDownloadsSubmit_otherinfo'>\n\t\t\t<br><br>\n\t\t\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t\t\t<div class='ipsColumn ipsColumn_veryNarrow ipsType_center'>\n\t\t\t\t\t<span class='cDownloadsSubmit_step'>\nCONTENT;\n$return .= htmlspecialchars( $step, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$step++;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\t\t\t<div class='ipsBox'>\n\t\t\t\t\t\t<h3 class='ipsType_sectionTitle ipsType_reset'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'submit_file_information', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\t\t\t<div class='ipsPad'>\n\t\t\t\t\t\t\t<ul class='ipsForm ipsForm_vertical'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nforeach ( $form->elements as $collection ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t\nCONTENT;\n\nforeach ( $collection as $fieldName => $input ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( !\\in_array( $fieldName, $nonInfoFields ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t\t\t{$input}\n\t\t\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/ul>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\n\t\t\t\nCONTENT;\n\nif ( $terms ):\n$return .= <<<CONTENT\n\n\t\t\t\t<br><br>\n\t\t\t\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t\t\t\t<div class='ipsColumn ipsColumn_veryNarrow ipsType_center'>\n\t\t\t\t\t\t<span class='cDownloadsSubmit_step'>\nCONTENT;\n$return .= htmlspecialchars( $step, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$step++;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\t\t\t\t<div class='ipsBox ipsPad'>\n\t\t\t\t\t\t\t<h3 class='ipsType_sectionHead'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'csubmissionterms_placeholder', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\t\t\t\t<br><br>\n\t\t\t\t\t\t\t<div class='ipsType_richText'>\n\t\t\t\t\t\t\t\t{$terms}\n\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/div>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t\nCONTENT;\n\nelseif ( $terms ):\n$return .= <<<CONTENT\n\n\t\t<br>\n\t\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t\t<div class='ipsColumn ipsColumn_veryNarrow ipsType_center'>\n\t\t\t\t<span class='cDownloadsSubmit_step'>\nCONTENT;\n$return .= htmlspecialchars( $step, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$step++;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t<\/div>\n\t\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\t\t<div class='ipsBox ipsPad'>\n\t\t\t\t\t<h3 class='ipsType_sectionHead'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'csubmissionterms_placeholder', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\t\t<br><br>\n\t\t\t\t\t<div class='ipsType_richText'>\n\t\t\t\t\t\t{$terms}\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\t\t<\/div>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\t<hr class='ipsHr'>\n\t<div class='ipsType_right'>\n\t\t<button type='submit' class='ipsButton ipsButton_large ipsButton_primary' data-role='submitForm'>\nCONTENT;\n\nif ( $bulk ):\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'continue', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'save_and_submit_files', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/button>\n\t<\/div>\n<\/form>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction topic( $file ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsAreaBackground_light ipsPad'>\n\nCONTENT;\n\nif ( $file->container()->bitoptions['topic_screenshot'] and $file->primary_screenshot ):\n$return .= <<<CONTENT\n\n\t<div class='ipsColumns ipsColumns_collapsePhone'>\n\t\t<div class='ipsColumn ipsColumn_medium ipsType_center'>\n\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $file->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\nCONTENT;\n\n$image = ( $file->primary_screenshot instanceof \\IPS\\File ) ? (string) $file->primary_screenshot->url : $file->primary_screenshot;\n$return .= <<<CONTENT\n\n\t\t\t\t<img src='\nCONTENT;\n\n$return .= \\IPS\\File::get( \"downloads_Screenshots\", $image )->url;\n$return .= <<<CONTENT\n' alt='\nCONTENT;\n$return .= htmlspecialchars( $file->name, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\n\t\t\t<\/a>\n\t\t\t<br><br>\n\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $file->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" class='ipsButton ipsButton_primary ipsButton_fullWidth ipsButton_small'>\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'view_file', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t<\/a>\n\t\t<\/div>\n\t\t<div class='ipsColumn_fluid'>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<h3 class='ipsType_sectionHead'>\nCONTENT;\n$return .= htmlspecialchars( $file->name, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/h3>\n\t\t\t\nCONTENT;\n\nif ( !$file->container()->bitoptions['topic_screenshot'] or !$file->primary_screenshot ):\n$return .= <<<CONTENT\n\n\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $file->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" class='ipsButton ipsButton_primary ipsButton_fullWidth ipsButton_small'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'view_file', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<hr class='ipsHr'>\n\t\t\t<div class='ipsType_normal ipsType_richText ipsContained ipsType_break'>\n\t\t\t\t{$file->desc}\n\t\t\t<\/div>\n\t\t\t<hr class='ipsHr'>\n\t\t\t<ul class='ipsDataList ipsDataList_reducedSpacing ipsDataList_collapsePhone'>\n\t\t\t\t<li class='ipsDataItem'>\n\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size5'>\n\t\t\t\t\t\t<strong>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'file_submitter', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/strong>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t{$file->author()->link()}\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/li>\n\t\t\t\t<li class='ipsDataItem'>\n\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size5'>\n\t\t\t\t\t\t<strong>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'file_submitted', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/strong>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t\nCONTENT;\n\n$return .= htmlspecialchars( \\IPS\\DateTime::ts( $file->submitted )->localeDate(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/li>\n\t\t\t\t<li class='ipsDataItem'>\n\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size5'>\n\t\t\t\t\t\t<strong>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'file_cat', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/strong>\n\t\t\t\t\t<\/div>\n\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $file->container()->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n$return .= htmlspecialchars( $file->container()->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/li>\n\t\t\t\t\nCONTENT;\n\nforeach ( $file->customFields( TRUE ) as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<li class='ipsDataItem'>\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size5'>\n\t\t\t\t\t\t\t<strong>\nCONTENT;\n\n$val = \"downloads_{$k}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/strong>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t\t{$v}\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/li>\n\t\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t<\/ul>\n\nCONTENT;\n\nif ( $file->container()->bitoptions['topic_screenshot'] and $file->primary_screenshot ):\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/div>\n<p>&nbsp;<\/p>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction wizardForm( $stepNames, $activeStep, $output, $baseUrl, $showSteps ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nforeach ( $stepNames as $step => $name ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $activeStep == $name ):\n$return .= <<<CONTENT\n\n\t\t{$output}\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;