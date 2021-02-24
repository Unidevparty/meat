<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_digest( $member, $frequency, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"digest_$frequency\", FALSE), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n<br \/>\n<br \/>\n<em style='color: #8c8c8c'>&mdash; \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n<\/em>\n\n<br \/>\n<br \/>\n<br \/>\n___digest___\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_digest( $member, $frequency, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"digest_$frequency\", FALSE), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\n___digest___\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
