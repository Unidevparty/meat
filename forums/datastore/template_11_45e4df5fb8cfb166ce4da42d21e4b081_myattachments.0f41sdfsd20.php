<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_core_front_myattachments extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction rows( $table, $headers, $rows ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nforeach ( $rows as $attachment ):\n$return .= <<<CONTENT\n\n\t<div class='ipsDataItem ipsAttach ipsAttach_done'>\n\t\t<div class='ipsDataItem_generic ipsDataItem_size3 ipsResponsive_hidePhone ipsResponsive_block ipsType_center'>\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/file\/attachment.php?id=\nCONTENT;\n$return .= htmlspecialchars( $attachment['attach_id'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\nCONTENT;\n\nif ( $attachment['attach_is_image'] ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<img src=\"\nCONTENT;\n\n$return .= \\IPS\\File::get( \"core_Attachment\", $attachment['attach_location'] )->url;\n$return .= <<<CONTENT\n\" alt='' class='ipsImage ipsThumb_small' data-ipsLightbox data-ipsLightbox-group=\"myAttachments\">\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t<i class='fa fa-file ipsType_large'><\/i>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<\/a>\n\t\t<\/div>\n\t\t<div class='ipsDataItem_main'>\n\t\t\t<h2 class='ipsDataItem_title ipsType_reset ipsType_medium ipsAttach_title ipsContained_container ipsType_blendLinks'><span class='ipsType_break ipsContained'><a href=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/file\/attachment.php?id=\nCONTENT;\n$return .= htmlspecialchars( $attachment['attach_id'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n$return .= htmlspecialchars( $attachment['attach_file'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a><\/span><\/h2>\n\t\t\t<p class='ipsDataItem_meta ipsType_light'>\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Output\\Plugin\\Filesize::humanReadableFilesize( $attachment['attach_filesize'] );\n$return .= <<<CONTENT\n &middot; \nCONTENT;\n\n$htmlsprintf = array(\\IPS\\DateTime::ts( $attachment['attach_date'] )->html()); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'my_attachment_uploaded', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'htmlsprintf' => $htmlsprintf ) );\n$return .= <<<CONTENT\n\n\t\t\t<\/p>\n\t\t<\/div>\n\t\t<div class='ipsDataItem_generic ipsDataItem_size9 ipsType_light'>\n\t\t\t{$attachment['attach_content']}\n\t\t<\/div>\n\t\t<div class='ipsDataItem_stats ipsType_light'>\n\t\t\t\nCONTENT;\n\nif ( !$attachment['attach_is_image'] ):\n$return .= <<<CONTENT\n\nCONTENT;\n\n$pluralize = array( $attachment['attach_hits'] ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'attach_hits_count', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\t\t\nCONTENT;\n\nif ( $table->canModerate() ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsDataItem_modCheck'>\n\t\t\t<span class='ipsCustomInput'>\n\t\t\t\t<input type='checkbox' data-role='moderation' name=\"moderate[\nCONTENT;\n$return .= htmlspecialchars( $attachment['attach_id'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n]\" data-actions=\"\nCONTENT;\n\n$return .= htmlspecialchars( implode( ' ', $table->multimodActions() ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\"  data-state=''>\n\t\t\t\t<span><\/span>\n\t\t\t<\/span>\n\t\t<\/div>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction template( $table, $used, $count ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<h1 class=\"ipsType_pageTitle\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'my_attachments', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h1>\n<br>\n\n\nCONTENT;\n\nif ( \\IPS\\Member::loggedIn()->group['g_attach_max'] > 0 ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$percentage = round( ( $used \/ ( \\IPS\\Member::loggedIn()->group['g_attach_max'] * 1024 ) ) * 100 );\n$return .= <<<CONTENT\n\n\t<div class='ipsBox ipsPad'>\n\t\t<h2 class='ipsType_minorHeading'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'my_attachment_quota', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h2>\n\t\t<div class='ipsProgressBar ipsProgressBar_fullWidth ipsClear \nCONTENT;\n\nif ( $percentage >= 90 ):\n$return .= <<<CONTENT\nipsProgressBar_warning\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' >\n\t\t\t<div class='ipsProgressBar_progress' style=\"width: \nCONTENT;\n$return .= htmlspecialchars( $percentage, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n%\">\n\t\t\t\t<span data-role=\"percentage\">\nCONTENT;\n$return .= htmlspecialchars( $percentage, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/span>%\n\t\t\t<\/div>\n\t\t<\/div>\n\t\t<br>\n\t\t<p class='ipsType_reset ipsType_center'>\n\t\t\t\nCONTENT;\n\n$sprintf = array(\\IPS\\Output\\Plugin\\Filesize::humanReadableFilesize( $used ), \\IPS\\Output\\Plugin\\Filesize::humanReadableFilesize( \\IPS\\Member::loggedIn()->group['g_attach_max'] * 1024 )); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'my_attachments_blurb', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\n\t\t<\/p>\n\t<\/div>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<br>\n\n<div class='ipsBox'>\n\t<h2 class='ipsType_sectionTitle ipsType_medium ipsType_reset'>\nCONTENT;\n\n$pluralize = array( $count ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'my_attachments_count', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n<\/h2>\n\t{$table}\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
