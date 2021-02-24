<?php

return <<<'VALUE'
"\tfunction content_pages_10(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\ninclude_once(\"\/home\/meatexpert5917\/data\/www\/meat-expert.ru\/partner\/lib\/mode.php\");\n$return .= <<<CONTENT\n\r\n\r\n\nCONTENT;\n\n$b2b = new b2bcontext(\"\/home\/meatexpert5917\/data\/www\/meat-expert.ru\/partner\/bponly.php\",1);\n$return .= <<<CONTENT\n\r\n\r\n\/*$this->registry->getClass('output')->setTitle( $b2b->b2bcontext_title );\r\n\/\/$this->registry->getClass('output')->addMetaTag('description', $b2b->b2bcontext_description);\r\n\/\/$this->registry->getClass('output')->addMetaTag('keywords', $b2b->b2bcontext_keywords);\r\n\/\/$this->registry->getClass('output')->addToDocumentHead( 'raw', $b2b->b2bcontext_head );\r\n*\/\r\n\\IPS\\Output::i()->output .= $b2b->b2bcontext_content;\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
