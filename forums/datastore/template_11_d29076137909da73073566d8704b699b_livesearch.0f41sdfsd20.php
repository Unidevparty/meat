<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_forums_admin_livesearch extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction forum( $forum ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<li class='ipsPad_half ipsClearfix' data-role='result'>\n\t<a href='\nCONTENT;\n\n$return .= htmlspecialchars( \\IPS\\Http\\Url::internal( \"app=forums&module=forums&controller=forums&do=form&id=\", null, \"\", array(), 0 ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', TRUE );\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $forum->id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsPos_left'>\nCONTENT;\n$return .= htmlspecialchars( $forum->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n<\/li>\n\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
