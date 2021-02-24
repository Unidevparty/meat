<?php

return <<<'VALUE'
"\tfunction content_pages_19(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\n$return .= \\IPS\\cms\\Blocks\\Block::display( \"sape\" );\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\cms\\Databases\\Dispatcher::i()->setDatabase( \"articles9283759273592\" )->run();\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
