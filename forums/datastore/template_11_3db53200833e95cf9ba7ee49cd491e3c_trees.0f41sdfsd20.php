<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_core_admin_trees extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction row( $url,$id,$title,$hasChildren=FALSE,$buttons=array(),$description='',$icon=NULL,$draggablePosition=NULL,$root=FALSE,$toggleStatus=NULL,$locked=NULL,$badge=NULL,$titleHtml=FALSE,$descriptionHtml=FALSE,$acceptsChildren=TRUE,$canBeRoot=TRUE, $additionalRowHtml=NULL ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsTree_row \nCONTENT;\n\nif ( !$root and $draggablePosition !== NULL ):\n$return .= <<<CONTENT\nipsTree_sortable\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $hasChildren ):\n$return .= <<<CONTENT\nipsTree_parent\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( !$canBeRoot ):\n$return .= <<<CONTENT\nipsTree_noRoot\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $root ):\n$return .= <<<CONTENT\nipsTree_open ipsTree_noToggle ipsTree_root\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $acceptsChildren ):\n$return .= <<<CONTENT\nipsTree_acceptsChildren\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-nodeid='\nCONTENT;\n$return .= htmlspecialchars( $id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-keyNavBlock data-keyAction='right'>\n\t\nCONTENT;\n\nif ( !$root and $draggablePosition !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsTree_drag ipsDrag'>\n\t\t\t<i class='ipsTree_dragHandle ipsDrag_dragHandle fa fa-bars ipsJS_show' data-ipsTooltip data-ipsTooltip-label='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'reorder', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><\/i>\n\t\t\t<noscript>\n\t\t\t\t<input name='order[\nCONTENT;\n$return .= htmlspecialchars( $id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n]' value=\"\nCONTENT;\n$return .= htmlspecialchars( $draggablePosition, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" size='2' type='text'>\n\t\t\t<\/noscript>\n\t\t<\/div>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $icon !== NULL and $icon instanceof \\IPS\\File ):\n$return .= <<<CONTENT\n\n\t\t<img class=\"ipsTree_icon\" src=\"\nCONTENT;\n$return .= htmlspecialchars( $icon->url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<div class='ipsTree_rowData ipsClearfix \nCONTENT;\n\nif ( $hasChildren === FALSE ):\n$return .= <<<CONTENT\nipsContained_container\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\n\t\t<h4 class='ipsContained ipsType_break'>\n\t\t\t\nCONTENT;\n\nif ( $icon !== NULL and !( $icon instanceof \\IPS\\File ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t<span><i class=\"\nCONTENT;\n\nif ( mb_substr( $icon, 0, 3 ) !== 'fa ' ):\n$return .= <<<CONTENT\nfa fa-fw fa-\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $icon, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\"><\/i><\/span>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $hasChildren ):\n$return .= <<<CONTENT\n\n\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'root' => $id ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsJS_preventEvent'>\n\t\t\t\t\t\nCONTENT;\n\nif ( !$titleHtml ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\nCONTENT;\n$return .= htmlspecialchars( $title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t{$title}\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( !$titleHtml ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n$return .= htmlspecialchars( $title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t{$title}\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $toggleStatus !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $locked ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span class='ipsPos_right ipsBadge \nCONTENT;\n\nif ( $toggleStatus ):\n$return .= <<<CONTENT\nipsBadge_positive\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nipsBadge_negative\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n ipsCursor_locked'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'locked', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n\nif ( \\is_array($toggleStatus)  ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<span class='ipsPos_right' data-ipsStatusToggle>\n\t\t\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'do' => 'enableToggle', 'status' => '0', 'id' => $id, 'root' => \\IPS\\Request::i()->root ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' \nCONTENT;\n\nif ( !$toggleStatus['status'] ):\n$return .= <<<CONTENT\nclass='ipsHide'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-ipsDialog-title=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'turn_offline', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-ipsDialog data-ipsTooltip data-state=\"enabled\" title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'node_disable_row', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t\t\t\t<span class='ipsBadge ipsBadge_positive'>\nCONTENT;\n\n$val = \"{$toggleStatus['enabled_lang']}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'do' => 'enableToggle', 'status' => '1', 'id' => $id, 'root' => \\IPS\\Request::i()->root ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' \nCONTENT;\n\nif ( $toggleStatus['status'] ):\n$return .= <<<CONTENT\nclass='ipsHide'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-ipsDialog-title=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'turn_offline', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\" data-ipsDialog data-ipsTooltip data-state=\"disabled\" title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'node_enable_row', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t\t\t\t<span class='ipsBadge ipsBadge_\nCONTENT;\n$return .= htmlspecialchars( $toggleStatus['disabled_badge'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\nCONTENT;\n\n$val = \"{$toggleStatus['disabled_lang']}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t<\/span>\n\t\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<span class='ipsPos_right' data-ipsStatusToggle>\n\t\t\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'do' => 'enableToggle', 'status' => '0', 'id' => $id, 'root' => \\IPS\\Request::i()->root ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' \nCONTENT;\n\nif ( !$toggleStatus ):\n$return .= <<<CONTENT\nclass='ipsHide'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-state=\"enabled\" data-ipsTooltip title='\nCONTENT;\n\n$sprintf = array($title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'node_disable_row', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t\t\t\t<span class='ipsTree_toggleEnable ipsBadge ipsBadge_positive'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'enabled', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'do' => 'enableToggle', 'status' => '1', 'id' => $id, 'root' => \\IPS\\Request::i()->root ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' \nCONTENT;\n\nif ( $toggleStatus ):\n$return .= <<<CONTENT\nclass='ipsHide'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-state=\"disabled\" data-ipsTooltip title='\nCONTENT;\n\n$sprintf = array($title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'node_enable_row', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t\t\t\t<span class='ipsTree_toggleDisable ipsBadge ipsBadge_negative'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'disabled', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t\t<\/a>\n\t\t\t\t\t\t<\/span>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $badge ):\n$return .= <<<CONTENT\n\n\t\t\t\t<span class=\"ipsBadge ipsBadge_\nCONTENT;\n$return .= htmlspecialchars( $badge[0], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n\nif ( !empty($badge[2]) ):\n$return .= <<<CONTENT\n{$badge[2]}\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$val = \"{$badge[1]}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t{$additionalRowHtml}\n\t\t<\/h4>\n\t\t\nCONTENT;\n\nif ( $description ):\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( !$descriptionHtml ):\n$return .= <<<CONTENT\n\n\t\t\t\t<div class=\"ipsType_light ipsContained ipsType_break\">\nCONTENT;\n$return .= htmlspecialchars( $description, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/div>\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t<div class=\"ipsType_light ipsContained ipsType_break\">{$description}<\/div>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n\t<div class='ipsTree_controls'>\n\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->controlStrip( $buttons, $url );\n$return .= <<<CONTENT\n\n\t<\/div>\n<\/div>\n\nCONTENT;\n\nif ( !$hasChildren ):\n$return .= <<<CONTENT\n\n\t<ol class='ipsTree ipsTree_node'><\/ol>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction rows( $rows, $uniqid, $root=false ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( \\count( $rows ) ):\n$return .= <<<CONTENT\n\n\t<ol class='ipsTree ipsTree_node'>\n\t\t\nCONTENT;\n\nforeach ( $rows as $id => $row ):\n$return .= <<<CONTENT\n\n\t\t\t<li id=\"sortable-\nCONTENT;\n$return .= htmlspecialchars( $uniqid, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n_\nCONTENT;\n$return .= htmlspecialchars( $id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" data-role=\"node\" class='mjs-nestedSortable-collapsed'>\n\t\t\t\t{$row}\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\t\n\t<\/ol>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction template( $url,$title,$root,$rootParent,$rows,$rootButtons=array(),$lockParents=FALSE,$protectRoots=FALSE,$searchable=FALSE,$pagination=NULL ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n\nif ( $rootButtons ):\n$return .= <<<CONTENT\n\n\t<div class='ipsClearfix acpTrees_buttons'>\n\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->buttons( $rootButtons, $url );\n$return .= <<<CONTENT\n\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n<div class='acpBlock'>\n\nCONTENT;\n\nif ( ( $searchable && !empty( $rows ) ) or ( \\IPS\\Request::i()->root and !\\IPS\\Request::i()->noshowroot ) ):\n$return .= <<<CONTENT\n\n\t<div class='acpWidgetToolbar ipsClearfix ipsClear'>\n\t\t\nCONTENT;\n\nif ( $searchable && !empty( $rows ) ):\n$return .= <<<CONTENT\n\n\t\t\t<input type='text' class='ipsPos_right acpTable_search ipsJS_show' id='tree_search' placeholder=\"\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'search_prefix_nofield', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\">\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( ( \\IPS\\Request::i()->root and !\\IPS\\Request::i()->noshowroot ) ):\n$return .= <<<CONTENT\n\n\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsButton ipsButton_light ipsButton_verySmall'><i class='fa fa-angle-double-left'><\/i> &nbsp;\nCONTENT;\n\n$val = \"{$title}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\nCONTENT;\n\nif ( $rootParent !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( \\is_object( $rootParent ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'root' => $rootParent->_id ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsButton ipsButton_light ipsButton_verySmall'><i class='fa fa-angle-left'><\/i> \nCONTENT;\n$return .= htmlspecialchars( $rootParent->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'root' => $rootParent ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsButton ipsButton_light ipsButton_verySmall'><i class='fa fa-angle-left'><\/i><\/a>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<div class='ipsTree_wrapper' data-ipsTree \nCONTENT;\n\nif ( $lockParents ):\n$return .= <<<CONTENT\ndata-ipsTree-lockParents\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $protectRoots ):\n$return .= <<<CONTENT\ndata-ipsTree-protectRoots\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n data-ipsTree-url='\nCONTENT;\n\nif ( \\IPS\\Request::i()->root ):\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'root' => \\IPS\\Request::i()->root ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-ipsTree-searchable='#tree_search'>\n\t{$root}\n\t\nCONTENT;\n\nif ( empty( $rows ) ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsType_light ipsPad'>\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'no_results', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t<form accept-charset='utf-8' action=\"\nCONTENT;\n$return .= htmlspecialchars( $url->setQueryString( array( 'root' => \\IPS\\Request::i()->root ?: 0, 'do' => 'reorder' ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" method=\"post\" data-role=\"treeListing\">\n\t\t\t<div class='ipsTree_rows'>\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"trees\", \"core\" )->rows( $rows, mt_rand(), true );\n$return .= <<<CONTENT\n\n\t\t\t<\/div>\n\t\t\t<noscript>\n\t\t\t\t<div class='ipsBlock_actionbar clearfix'>\n\t\t\t\t\t<button class=\"left\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'reorder', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/button>\n\t\t\t\t<\/div>\n\t\t\t<\/noscript>\n\t\t<\/form>\n\t\t\nCONTENT;\n\nif ( $searchable ):\n$return .= <<<CONTENT\n\n\t\t\t<div data-role=\"treeResults\"><\/div>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $pagination ):\n$return .= <<<CONTENT\n\n\t\t<div class=\"ipsSpacer_top\">\n\t\t\t{$pagination}\n\t\t<\/div>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
