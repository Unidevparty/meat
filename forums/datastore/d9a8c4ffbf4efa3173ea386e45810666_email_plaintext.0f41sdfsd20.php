<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_digest( $member, $frequency, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n{$email->language->addToStack(\"digest_$frequency\", FALSE)}\n<br \/>\n<br \/>\n<em style='color: #8c8c8c'>&mdash; \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n<\/em>\n\n<br \/>\n<br \/>\n<br \/>\n___digest___\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_digest( $member, $frequency, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n{$email->language->addToStack(\"digest_$frequency\", FALSE)}\n\n___digest___\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
