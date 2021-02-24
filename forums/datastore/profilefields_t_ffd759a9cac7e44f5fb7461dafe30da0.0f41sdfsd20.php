<?php

return <<<'VALUE'
"\tfunction profilefields_t_ffd759a9cac7e44f5fb7461dafe30da0( $title, $content, $processedContent, $member, $member_id ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<img src='\/\/www.meat-expert.ru\/public\/style_images\/flags\/\nCONTENT;\n$return .= htmlspecialchars( $content, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n.gif' width=\"20px\" style=\"vertical-align: inherit;\">\r\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
