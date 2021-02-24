<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_contact_form( $member, $name, $fromEmail, $message, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_contact_form\", FALSE), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n<br \/>\n<br \/>\n\n<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n\t<tr>\n\t\t<td dir='{dir}' width='40' valign='top' class='hidePhone' style='width: 0; max-height: 0; overflow: hidden; float: left;'>\n\t\t\t<img src='\nCONTENT;\n$return .= htmlspecialchars( $member->get_photo( true, true ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' width='40' height='40' style='border: 1px solid #777777; vertical-align: middle;'>\n\t\t<\/td>\n\t\t<td dir='{dir}' width='30' valign='top' class='hidePhone' style='width: 0; max-height: 0; overflow: hidden; float: left;'>\n\t\t\t<br \/>\n\t\t\t<span style='display: block; width: 0px; height: 0px; border-width: 15px; border-color: transparent #f9f9f9 transparent transparent; border-style: solid'><\/span>\n\t\t<\/td>\n\t\t<td dir='{dir}' valign='top' style='background: #f9f9f9;'>\n\t\t\t<table width='100%' cellpadding='10' cellspacing='0' border='0'>\n\t\t\t\t<tr>\n\t\t\t\t\t<td dir='{dir}'>\n\t\t\t\t\t\t<table width='100%' cellpadding='5' cellspacing='0' border='0'>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td dir='{dir}' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; line-height: 1.5; font-size: 14px;\">\n\t\t\t\t\t\t\t\t\t<strong>\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_contact_said\", FALSE, array( 'sprintf' => array( $name, $fromEmail ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/strong>\n\t\t\t\t\t\t\t\t<\/td>\n\t\t\t\t\t\t\t<\/tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td dir='{dir}' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; line-height: 1.5; font-size: 14px;\">\n\t\t\t\t\t\t\t\t\t{$email->parseTextForEmail( $message, $email->language )}\n\t\t\t\t\t\t\t\t<\/td>\n\t\t\t\t\t\t\t<\/tr>\n\t\t\t\t\t\t<\/table>\n\t\t\t\t\t<\/td>\n\t\t\t\t<\/tr>\n\t\t\t<\/table>\n\t\t<\/td>\n\t<\/tr>\n<\/table>\n<br \/><br \/>\n<em style='color: #8c8c8c'>&mdash; \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n<\/em>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_contact_form( $member, $name, $fromEmail, $message, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_contact_form\", FALSE), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\n----\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_contact_said\", FALSE, array( 'htmlsprintf' => array( $name, $fromEmail ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n\n$return .= \\IPS\\Email::buildPlaintextBody( $message );\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
