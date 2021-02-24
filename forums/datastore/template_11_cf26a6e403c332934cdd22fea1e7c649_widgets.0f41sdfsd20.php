<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_form_front_widgets extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'ed3c64ff19a712915835e09395045125';\n\tfunction formForm( $container, $form, $orientation='vertical' ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<div class='ipsBox ipsPad'>\n    <div class='ipsType_normal ipsType_richText'>\n        \nCONTENT;\n\nif ( $container->formTranslate( 'rules' ) ):\n$return .= <<<CONTENT\n\n            <div class='ipsMessage ipsMessage_\nCONTENT;\n\nif ( isset( $container->_options->rules_color ) ):\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= htmlspecialchars( $container->_options->rules_color, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\ninfo\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\n                {$container->formTranslate( 'rules', TRUE )}\n            <\/div>\n        \nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n        {$form}\n    <\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
