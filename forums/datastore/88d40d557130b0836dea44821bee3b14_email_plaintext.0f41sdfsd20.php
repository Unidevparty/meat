<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_lost_password_init( $member, $vid, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n{$email->language->addToStack(\"email_lost_pass\", FALSE)}\n<br \/><br \/>\n\n<table width='100%' cellpadding='15' cellspacing='0' border='0' style='background: #f9f9f9;'>\n\t<tr>\n\t\t<td dir='{dir}' align='center'>\n\t\t\t<a href='\nCONTENT;\n\n$return .= htmlspecialchars( \\IPS\\Http\\Url::internal( \"app=core&module=system&controller=lostpass&do=validate&vid={$vid}&mid={$member->member_id}\", \"front\", \"lostpassword\", array(), 0 ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', TRUE );\n$return .= <<<CONTENT\n' style=\"color: #ffffff; font-family: 'Helvetica Neue', helvetica, sans-serif; text-decoration: none; font-size: 14px; background: \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->email_color;\n$return .= <<<CONTENT\n; line-height: 32px; padding: 0 10px; display: inline-block; border-radius: 3px;\">{$email->language->addToStack(\"email_reset_password\", FALSE)}<\/a>\n\t\t<\/td>\n\t<\/tr>\n<\/table>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_lost_password_init( $member, $vid, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n{$email->language->addToStack(\"email_lost_pass_plain\", FALSE)}\n\n=====\n{$email->language->addToStack(\"email_reset_password\", FALSE)}: \nCONTENT;\n\n$return .= \\IPS\\Http\\Url::internal( \"app=core&module=system&controller=lostpass&do=validate&vid={$vid}&mid={$member->member_id}\", \"front\", \"lostpassword\", array(), 0 );\n$return .= <<<CONTENT\n\n=====\n\n-- \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
