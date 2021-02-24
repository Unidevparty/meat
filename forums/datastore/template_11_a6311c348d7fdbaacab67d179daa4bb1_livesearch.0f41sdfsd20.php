<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_blog_admin_livesearch extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction blog( $blog ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<li class='ipsPad_half ipsClearfix' data-role='result'>\n\t<a href='\nCONTENT;\n\n$return .= htmlspecialchars( \\IPS\\Http\\Url::internal( \"app=blog&module=blogs&controller=blogs&do=form&id=\", null, \"\", array(), 0 ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', TRUE );\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $blog->id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsPos_left'>\nCONTENT;\n$return .= htmlspecialchars( $blog->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n<\/li>\n\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
