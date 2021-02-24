<?php

return <<<'VALUE'
"\tfunction content_pages_8(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\ninclude_once(\"\/home\/meatexpert5917\/data\/www\/meat-expert.ru\/partner\/lib\/mode.php\");\r\n\r\n$b2b = new b2bcontext(\"\/home\/meatexpert5917\/data\/www\/meat-expert.ru\/partner\/nobp.php\",1);\r\n\r\n$this->registry->getClass('output')->setTitle( $b2b->b2bcontext_title );\r\n$this->registry->getClass('output')->addMetaTag('description', $b2b->b2bcontext_description);\r\n$this->registry->getClass('output')->addMetaTag('keywords', $b2b->b2bcontext_keywords);\r\n$this->registry->getClass('output')->addToDocumentHead( 'raw', $b2b->b2bcontext_head );\r\n$this->registry->getClass('output')->addContent($b2b->b2bcontext_content);\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
