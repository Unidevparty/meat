<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_cms_database_listing\n{\n\tfunction categoryFooter( $category, $table, $activeFilters ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( ! \\IPS\\Request::i()->isAjax() AND ! isset( \\IPS\\Request::i()->advancedSearchForm ) AND $category->show_records ):\n$return .= <<<CONTENT\n\n\t<ul class=\"ipsToolList ipsToolList_horizontal ipsClearfix ipsSpacer_both ipsResponsive_showPhone ipsResponsive_block\">\n\t\t\nCONTENT;\n\nif ( $category->can('add') ):\n$return .= <<<CONTENT\n\n\t\t\t<li class='ipsToolList_primaryAction'>\n\t\t\t\t<a class=\"ipsButton ipsButton_medium ipsButton_important ipsButton_fullWidth\" href=\"\nCONTENT;\n$return .= htmlspecialchars( $category->url()->setQueryString( array( 'do' => 'form', 'd' => \\IPS\\cms\\Databases\\Dispatcher::i()->databaseId ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n\n$sprintf = array(\\IPS\\cms\\Databases::load( $category->database_id )->recordWord( 1 )); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_add_new_record_button', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( \\IPS\\Member::loggedIn()->member_id ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<a class=\"ipsButton ipsButton_medium ipsButton_fullWidth ipsButton_link\" href=\"\nCONTENT;\n$return .= htmlspecialchars( $category->url()->setQueryString( 'do', 'markRead' )->csrf(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\"><i class=\"fa fa-check\"><\/i> \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_mark_read', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/ul>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( !\\IPS\\Request::i()->advancedSearchForm ):\n$return .= <<<CONTENT\n\n\t<div class=\"ipsResponsive_showPhone ipsResponsive_block ipsSpacer ipsSpacer_both ipsClearfix\">\n\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->follow( 'cms','categories' . $category->database_id, $category->_id, \\IPS\\cms\\Records::containerFollowerCount( $category ) );\n$return .= <<<CONTENT\n\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction categoryHeader( $category, $table, $activeFilters ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( !\\IPS\\Request::i()->advancedSearchForm ):\n$return .= <<<CONTENT\n\n\t<div class=\"ipsPageHeader ipsClearfix ipsSpacer_bottom\">\n\t\t<h1 class=\"ipsType_pageTitle\">\nCONTENT;\n$return .= htmlspecialchars( $category->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/h1>\n\t\t<div class='ipsPos_right ipsResponsive_noFloat ipsResponsive_hidePhone'>\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->follow( 'cms','categories' . $category->database_id, $category->_id, \\IPS\\cms\\Records::containerFollowerCount( $category ) );\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t\t<div class='ipsType_richText ipsType_normal'>\n\t\t\t{$category->_description}\n\t\t<\/div>\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( $category->hasChildren() AND ! isset( \\IPS\\Request::i()->advancedSearchForm ) ):\n$return .= <<<CONTENT\n\n\t<div class=\"ipsBox ipsSpacer_bottom\">\n\t\t<h2 class='ipsType_sectionTitle ipsType_reset'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'content_subcategories_title', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h2>\n\t\t<ol class=\"ipsDataList\">\n\t\t\t\nCONTENT;\n\nforeach ( $category->children() as $cat ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"category_index\", \"cms\", 'database' )->categoryRow( $cat );\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t<\/ol>\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( ! \\IPS\\Request::i()->isAjax() AND ! isset( \\IPS\\Request::i()->advancedSearchForm ) AND $category->show_records ):\n$return .= <<<CONTENT\n\n\t<ul class=\"ipsToolList ipsToolList_horizontal ipsClearfix ipsSpacer_both ipsResponsive_hidePhone\">\n\t\t\nCONTENT;\n\nif ( $category->can('add') ):\n$return .= <<<CONTENT\n\n\t\t\t<li class='ipsToolList_primaryAction'>\n\t\t\t\t<a class=\"ipsButton ipsButton_medium ipsButton_important ipsButton_fullWidth\" href=\"\nCONTENT;\n$return .= htmlspecialchars( $category->url()->setQueryString( array( 'do' => 'form', 'd' => \\IPS\\cms\\Databases\\Dispatcher::i()->databaseId ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n\n$sprintf = array(\\IPS\\cms\\Databases::load( $category->database_id )->recordWord( 1 )); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_add_new_record_button', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( \\IPS\\Member::loggedIn()->member_id ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<a class=\"ipsButton ipsButton_medium ipsButton_fullWidth ipsButton_link\" href=\"\nCONTENT;\n$return .= htmlspecialchars( $category->url()->setQueryString( 'do', 'markRead' )->csrf(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\"><i class=\"fa fa-check\"><\/i> \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_mark_read', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/ul>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( \\count( $activeFilters )  AND ! isset( \\IPS\\Request::i()->advancedSearchForm ) ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"listing\", \"cms\", 'database' )->filterMessage( $activeFilters, $category );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction categoryTable( $table, $headers, $rows, $quickSearch ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsBox'  data-baseurl='\nCONTENT;\n$return .= htmlspecialchars( $table->baseUrl, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-resort='\nCONTENT;\n$return .= htmlspecialchars( $table->resortKey, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-controller='core.global.core.table\nCONTENT;\n\nif ( $table->canModerate() ):\n$return .= <<<CONTENT\n,core.front.core.moderation\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\n\t\nCONTENT;\n\nif ( $table->canModerate() ):\n$return .= <<<CONTENT\n\n\t\t<form action=\"\nCONTENT;\n$return .= htmlspecialchars( $table->baseUrl->csrf(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" method=\"post\" data-role='moderationTools' data-ipsPageAction>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( ! count($rows) ):\n$return .= <<<CONTENT\n\n\t\t\t\t<div class=\"ipsPad\">\n\t\t\t\t\t\nCONTENT;\n\n$sprintf = array(\\IPS\\cms\\Databases::load( \\IPS\\cms\\Databases\\Dispatcher::i()->databaseId )->recordWord()); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_no_records_to_show', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t<ol class='ipsDataList ipsDataList_zebra ipsClear cCmsListing \nCONTENT;\n\nforeach ( $table->classes as $class ):\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $class, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n \nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n' id='elTable_\nCONTENT;\n$return .= htmlspecialchars( $table->uniqueId, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-role=\"tableRows\">\n\t\t\t\t\t\nCONTENT;\n\n$return .= $table->rowsTemplate[0]->{$table->rowsTemplate[1]}( $table, $headers, $rows );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/ol>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $table->canModerate() ):\n$return .= <<<CONTENT\n\n\t\t\t<div class=\"ipsAreaBackground ipsPad ipsClearfix\" data-role=\"pageActionOptions\">\n\t\t\t\t<div class=\"ipsPos_right\">\n\t\t\t\t\t<select name=\"modaction\" data-role=\"moderationAction\">\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('unhide') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<option value='approve' data-icon='check-circle'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'approve', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('feature') or $table->canModerate('unfeature') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<optgroup label=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'feature', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-icon='star' data-action='feature'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('feature') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='feature'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'feature', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('unhide') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='unfeature'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'unfeature', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/optgroup>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('pin') or $table->canModerate('unpin') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<optgroup label=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pin', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-icon='thumb-tack' data-action='pin'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('pin') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='pin'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pin', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('unpin') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='unpin'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'unpin', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/optgroup>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('hide') or $table->canModerate('unhide') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<optgroup label=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hide', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-icon='eye' data-action='hide'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('hide') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='hide'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hide', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('unhide') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='unhide'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'unhide', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/optgroup>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('lock') or $table->canModerate('unlock') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<optgroup label=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'lock', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-icon='lock' data-action='lock'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('lock') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='lock'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'lock', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('unlock') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='unlock'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'unlock', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/optgroup>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('move') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<option value='move' data-icon='arrow-right'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'move', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('split_merge') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<option value='merge' data-icon='level-up'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'merge', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('delete') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<option value='delete' data-icon='trash'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'delete', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->canModerate('future_publish') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<option data-icon=\"arrow-circle-o-up\" value='publish'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'publish', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $table->savedActions ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<optgroup label=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'saved_actions', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-icon='tasks' data-action='saved_actions'>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nforeach ( $table->savedActions as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<option value='savedAction-\nCONTENT;\n$return .= htmlspecialchars( $k, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\nCONTENT;\n$return .= htmlspecialchars( $v, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/option>\n\t\t\t\t\t\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/optgroup>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t<\/select>\n\t\t\t\t\t<button type=\"submit\" class=\"ipsButton ipsButton_alternate ipsButton_verySmall\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'submit', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/button>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\t\t<\/form>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\n\t\nCONTENT;\n\nif ( $table->pages > 1 ):\n$return .= <<<CONTENT\n\n\t\t\n\t\t\t<div data-role=\"tablePagination\">\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\", 'global' )->pagination( $table->baseUrl, $table->pages, $table->page, $table->limit, TRUE, $table->getPaginationKey() );\n$return .= <<<CONTENT\n\n\t\t\t<\/div>\n\t\t\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction filterMessage( $activeFilters, $category ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsMessage ipsMessage_general'>\n\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $category->url()->csrf()->setQueryString( array( \"do\" => \"clearFilters\", 'd' => \\IPS\\cms\\Databases\\Dispatcher::i()->databaseId ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsPos_right ipsButton ipsButton_veryLight ipsButton_verySmall'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_database_filtered_clear', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_database_filtered_by', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nforeach ( $activeFilters as $id => $data ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n$return .= htmlspecialchars( $data['field']->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n (\nCONTENT;\n$return .= htmlspecialchars( $data['value'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n)\n\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction recordRow( $table, $headers, $rows ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\n$rowIds = array();\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nforeach ( $rows as $row ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$idField = $row::$databaseColumnId;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$rowIds[] = $row->$idField;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$iposted = ( $table AND method_exists( $table, 'container' ) AND $table->container() !== NULL ) ? $table->container()->contentPostedIn( null, $rowIds ) : array();\n$return .= <<<CONTENT\n\n<section class='ipsType_normal ipsSpacer_both'>\n\nCONTENT;\n\nforeach ( $rows as $row ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$idField = $row::$databaseColumnId;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$record = $row;\n$return .= <<<CONTENT\n\n\t<article class='cCmsCategoryFeaturedEntry ipsClear ipsClearfix \nCONTENT;\n\nif ( $record->hidden() ):\n$return .= <<<CONTENT\nipsModerated\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\n\t<!-- <header class='ipsPhotoPanel ipsPhotoPanel_notPhone ipsPhotoPanel_small ipsSpacer_bottom'>\n\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->userPhoto( $record->author() );\n$return .= <<<CONTENT\n\n\t\t<div> \n\t\t\t<h2 class='ipsType_pageTitle'>\n\t\t\t\t\nCONTENT;\n\nif ( $record->prefix() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->prefix( $record->prefix( TRUE ), $record->prefix() );\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('pinned') || $record->mapped('featured') || $record->hidden() === -1 || $record->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->hidden() === -1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n$return .= htmlspecialchars( $record->hiddenBlurb(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'><i class='fa fa-eye-slash'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nelseif ( $record->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_approval', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-warning'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('pinned') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pinned', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-thumb-tack'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('featured') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'featured', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-star'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\n\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $record->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" title=\"\nCONTENT;\n\n$sprintf = array($record->_title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'read_more_about', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->unread() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class='ipsItemStatus' data-ipsTooltip title=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_unread_record', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\"><i class=\"fa fa-circle\"><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\n$return .= htmlspecialchars( $record->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\t\t\t<\/h2>\n\t\t\t<p class='ipsType_light ipsType_reset'>\n\t\t\t\t\nCONTENT;\n\n$htmlsprintf = array($record->author()->link(), $record->container()->url(), $record->container()->_title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_byline', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'htmlsprintf' => $htmlsprintf ) );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$val = ( $record->record_publish_date instanceof \\IPS\\DateTime ) ? $record->record_publish_date : \\IPS\\DateTime::ts( $record->record_publish_date );$return .= $val->html();\n$return .= <<<CONTENT\n\n\t\t\t<\/p>\n\t\t<\/div>\n\t<\/header> -->\n  <div class='wa_theme_desc'>\n  <h2 class='ipsType_pageTitle'>\n\t\t\t\t\nCONTENT;\n\nif ( $record->prefix() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->prefix( $record->prefix( TRUE ), $record->prefix() );\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('pinned') || $record->mapped('featured') || $record->hidden() === -1 || $record->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->hidden() === -1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n$return .= htmlspecialchars( $record->hiddenBlurb(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'><i class='fa fa-eye-slash'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nelseif ( $record->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_approval', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-warning'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('pinned') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pinned', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-thumb-tack'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->mapped('featured') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'featured', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-star'><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\n\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $record->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" title=\"\nCONTENT;\n\n$sprintf = array($record->_title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'read_more_about', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\t\nCONTENT;\n\nif ( $record->unread() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class='ipsItemStatus' data-ipsTooltip title=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'cms_unread_record', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\"><i class=\"fa fa-circle\"><\/i><\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\n$return .= htmlspecialchars( $record->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\t\t\t<\/h2>\n\t\nCONTENT;\n\nif ( count( $record->customFieldsForDisplay('listing') ) ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsDataItem_meta'>\n          <a href=\"\nCONTENT;\n$return .= htmlspecialchars( $record->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\t\nCONTENT;\n\nforeach ( $record->customFieldsForDisplay('listing') as $fieldId => $fieldValue ):\n$return .= <<<CONTENT\n\n\t\t\t\n            \nCONTENT;\n\nif ( ($fieldId == 'teaser_paragraph' && mb_strlen(trim($fieldValue)) < 63) ):\n$return .= <<<CONTENT\n\n \n                \nCONTENT;\n\n$fieldValue = false;\n$return .= <<<CONTENT\n\n                <div data-ipsTruncate data-ipsTruncate-type=\"remove\" data-ipsTruncate-size=\"2 lines\">{$record->truncated()}<\/div>\n            \nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n            \nCONTENT;\n\nif ( $fieldValue ):\n$return .= <<<CONTENT\n\n\t\t\t\t{$fieldValue}\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n          <\/a>\n\t\t<\/div>\n\n    <!--  \nCONTENT;\n\nif ( strlen($record->customFieldDisplayByKey('teaser_paragraph'))<30 ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nforeach ( $record->customFieldsForDisplay('listing') as $fieldId => $fieldValue ):\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $fieldValue ):\n$return .= <<<CONTENT\n\n\t\t\t\t{$fieldValue}\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n            \nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n              {$record->truncated()}\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n --->\n    \n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n  <div \nCONTENT;\n\nif ( $record->record_image ):\n$return .= <<<CONTENT\nclass='ipsColumns ipsColumns_collapsePhone'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n>\n\t\t\n\t\t\t<div class='ipsColumn ipsColumn_fluid'>\n\t\t\n\n\t\t\t\t<!-- <section class='ipsType_normal ipsType_richText ipsType_break ipsSpacer_bottom' data-ipsTruncate data-ipsTruncate-size='7 lines' data-ipsTruncate-type='remove'>\n\t\t\t\t\t{$record->truncated()}\n\t\t\t\t<\/section> -->\n\n\t\t\t\t<!-- \nCONTENT;\n\nif ( count( $record->tags() ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->tags( $record->tags() );\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n -->\n\t\t\t\t<ul class='ipsList_inline ipsClear'>\n\t\t\t\t\t<li title='\u0414\u0430\u0442\u0430' data-ipstooltip=\"\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"><\/i> \nCONTENT;\n\n$val = ( $record->record_publish_date instanceof \\IPS\\DateTime ) ? $record->record_publish_date : \\IPS\\DateTime::ts( $record->record_publish_date );$return .= $val->html();\n$return .= <<<CONTENT\n \nCONTENT;\n\n$return .= htmlspecialchars( \\IPS\\DateTime::ts( $record->record_publish_date )->format( 'H:i' ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t\t\nCONTENT;\n\nif ( $record::database()->options['reviews'] ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<li>\nCONTENT;\n\n$return .= \\IPS\\cms\\_Theme::i()->getTemplate( \"global\", \"core\" )->rating( 'small', $record->averageReviewRating(), \\IPS\\Settings::i()->reviews_rating_out_of );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n                    <!--\nCONTENT;\n\nif ( !\\IPS\\Member::loggedIn()->inGroup( array( 15, 14, 21, 20, 10, 12, 8, 16, 7, 17, 3, 19, 2, 22, 23, 24, 25 ) ) ):\n$return .= <<<CONTENT\n\n                    <li title='\u041f\u0440\u043e\u0441\u043c\u043e\u0442\u0440\u044b' class='ipsType_light'><i class=\"fa fa-eye\" aria-hidden=\"true\"><\/i> \nCONTENT;\n$return .= htmlspecialchars( $record->record_views, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n-->\n                    \nCONTENT;\n\nif ( \\IPS\\Member::loggedIn()->inGroup( array( 13, 4 ) ) ):\n$return .= <<<CONTENT\n\n                    <li title='\u041f\u0440\u043e\u0441\u043c\u043e\u0442\u0440\u044b' class='ipsType_light'><i class=\"fa fa-eye\" aria-hidden=\"true\"><\/i> \nCONTENT;\n$return .= htmlspecialchars( $record->record_views, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n                    <li title='\u041e\u0442\u0432\u0435\u0442\u044b' data-ipstooltip=\"\"class='wa_news_comments'><a href='\nCONTENT;\n$return .= htmlspecialchars( $record->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n#comments' title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'view_comments', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-commenting'><\/i> \nCONTENT;\n$return .= htmlspecialchars( $record->record_comments, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a><\/li>\n\t\t\t\t\t<li title='\u041a\u0430\u0442\u0435\u0433\u043e\u0440\u0438\u044f' data-ipstooltip=\"\" class='ipsType_light'><i class=\"fa fa-folder-open\" aria-hidden=\"true\"><\/i> <a href=\"\nCONTENT;\n$return .= htmlspecialchars( $record->container()->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n$return .= htmlspecialchars( $record->container()->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a><\/li>\n\t\t\t\t<\/ul>\n\n\t\t\nCONTENT;\n\nif ( $record->record_image ):\n$return .= <<<CONTENT\n\n\t\t\t<\/div>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n  <\/div>\n  \n  <div class='wa_theme_image'>\n  \nCONTENT;\n\nif ( $record->record_image ):\n$return .= <<<CONTENT\n\n\t\t\t<div class='ipsColumn ipsColumn_medium'>\n\t\t\t\t<div class=\"cCmsRecord_image\">\n                  <a href=\"\nCONTENT;\n$return .= htmlspecialchars( $record->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\t<img class=\"ipsImage\" src=\"\nCONTENT;\n\n$return .= \\IPS\\File::get( \"cms_Records\", $record->_record_image_thumb )->url;\n$return .= <<<CONTENT\n\">\n                    <\/a>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n      \nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n    <\/div>\n\t\n<\/article>\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n<\/section>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction rssItemWithImage( $content, $image ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<p><img src=\"\nCONTENT;\n\n$return .= \\IPS\\File::get( \"cms_Records\", $image )->url;\n$return .= <<<CONTENT\n\" \/><\/p>\n{$content}\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;