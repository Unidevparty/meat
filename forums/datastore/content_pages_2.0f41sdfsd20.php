<?php

return <<<'VALUE'
"\tfunction content_pages_2(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n<div class='ipsLayout ipsLayout_withright ipsLayout_largeright'>\n        <div class='ipsLayout_right'>\n                \nCONTENT;\n\n$return .= \\IPS\\cms\\Blocks\\Block::display( \"artmedlink\" );\n$return .= <<<CONTENT\n \n                \nCONTENT;\n\n$return .= \\IPS\\cms\\Blocks\\Block::display( \"media_categories\" );\n$return .= <<<CONTENT\n\n                \nCONTENT;\n\n$return .= \\IPS\\cms\\Blocks\\Block::display( \"top_videos\" );\n$return .= <<<CONTENT\n \n        <\/div>\n        <div class='ipsLayout_content'>\n                \nCONTENT;\n\n$return .= \\IPS\\cms\\Databases\\Dispatcher::i()->setDatabase( \"media_demo\" )->run();\n$return .= <<<CONTENT\n\n        <\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
