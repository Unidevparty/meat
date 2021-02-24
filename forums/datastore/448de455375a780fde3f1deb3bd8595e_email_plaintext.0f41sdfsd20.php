<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_email_address_changed( $member, $oldEmail, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n{$email->language->addToStack(\"email_email_address_changed\", FALSE, array( 'sprintf' => \\IPS\\Settings::i()->board_name ) )}\n<br \/>\n<br \/>\n\n<table width='100%' cellpadding='0' cellspacing='0' border='0' style='background: #f9f9f9;'>\n\t<tr>\n\t\t<td dir='{dir}' height='20' style='line-height: 20px'><img src=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/email\/spacer.png\" alt='' width=\"1\" height=\"1\" \/><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' align='center' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 17px;\"><strong>{$email->language->addToStack(\"email_new_email\", FALSE)}<\/strong><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' height='6' style='line-height: 6px'><img src=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/email\/spacer.png\" alt='' width=\"1\" height=\"1\" \/><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' align='center' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 22px;\">{$member->email}<\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' height='20' style='line-height: 20px'><img src=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/email\/spacer.png\" alt='' width=\"1\" height=\"1\" \/><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' align='center' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 17px;\"><strong>{$email->language->addToStack(\"email_old_email\", FALSE)}<\/strong><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' height='6' style='line-height: 6px'><img src=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/email\/spacer.png\" alt='' width=\"1\" height=\"1\" \/><\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' align='center' style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 22px;\">{$oldEmail}<\/td>\n\t<\/tr>\n\t<tr>\n\t\t<td dir='{dir}' height='6' style='line-height: 6px'><img src=\"\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\napplications\/core\/interface\/email\/spacer.png\" alt='' width=\"1\" height=\"1\" \/><\/td>\n\t<\/tr>\n<\/table>\n\n<br \/><br \/>\n<em style='color: #8c8c8c'>&mdash; \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n<\/em>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_email_address_changed( $member, $oldEmail, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n{$email->language->addToStack(\"email_email_address_changed\", FALSE, array( 'htmlsprintf' => \\IPS\\Settings::i()->board_name ) )}\n\n=====\n{$email->language->addToStack(\"email_new_email\", FALSE)}: {$member->email}\n{$email->language->addToStack(\"email_old_email\", FALSE)}: {$oldEmail}\n=====\n\n-- \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
