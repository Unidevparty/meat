<?php

return <<<'VALUE'
"\tfunction profilefields_t_1f50979dddc3c0469bac71e4289bfd4f( $title, $content, $processedContent, $member, $member_id ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n$return .= htmlspecialchars( $content, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
