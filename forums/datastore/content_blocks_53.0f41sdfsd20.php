<?php

return <<<'VALUE'
"\tfunction content_blocks_53(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'ad_news_sidebar_1' );\n$return .= <<<CONTENT\n\r\n<br>\r\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'ad_news_sidebar_obor' );\n$return .= <<<CONTENT\n\r\n<br>\r\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'ad_forumagroyug' );\n$return .= <<<CONTENT\n\r\n<br>\r\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'expocrimea' );\n$return .= <<<CONTENT\n\r\n<br>\r\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'halalprodexpo' );\n$return .= <<<CONTENT\n\r\n<br>\r\n\nCONTENT;\n\n$return .= \\IPS\\core\\Advertisement::loadByLocation( 'krasfair' );\n$return .= <<<CONTENT\n\r\n<br>\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
