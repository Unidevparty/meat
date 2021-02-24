<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_forums_digests__item( $topic, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<tr style='border-bottom: 1px solid #eeeeee'>\n\t<td dir='{dir}' width='40' align='center' valign='top' class=' '>\n\t\t<img src='{$topic->author()->get_photo( true, true )}' width='32' height='32' style='border: 1px solid #000; vertical-align: middle;'>\n\t<\/td>\n\t<td dir='{dir}' align='left'>\n\t\t<p style='font-family: \"Helvetica Neue\", helvetica, sans-serif; margin: 0; font-size: 13px; font-weight: bold'>\n\t\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t{$email->language->addToStack(\"x_asked_new_question\", FALSE, array( 'sprintf' => array( $topic->author()->name, $topic->container()->getTitleForLanguage( $email->language ) ) ) )}\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t{$email->language->addToStack(\"x_created_topic_in\", FALSE, array( 'sprintf' => array( $topic->author()->name, $topic->container()->getTitleForLanguage( $email->language ) ) ) )}\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/p>\n\t\t<h2 style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 20px; font-weight: 500 !important; margin: 0;\">\n\t\t\t<a href='{$topic->url()}' style='text-decoration: none;'>{$topic->title}<\/a>\n\t\t\t\nCONTENT;\n\nif ( $topic->mapped('featured') ):\n$return .= <<<CONTENT\n&nbsp;&nbsp;<span style='color: #68a72f; font-weight: bold; font-size: 12px; text-transform: uppercase;'>{$email->language->addToStack(\"featured\", FALSE)}<\/span>\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/h2>\n\t\t<div style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; line-height: 22px; margin-top: 10px; margin-bottom: 10px;\">\n\t\t\t{$email->parseTextForEmail( $topic->content(), $email->language )}\n\t\t<\/div>\n\t\t<p style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; margin: 0; font-size: 13px;\">\n\n\t\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t<strong style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; text-decoration: none; font-size: 12px; color: #4a7c20; display: inline-block;\">&#10003; {$email->language->addToStack(\"answered\", FALSE)}<\/strong>&nbsp;&nbsp;&nbsp;&nbsp;\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\t\t\t\nCONTENT;\n\nif ( $topic->container()->forum_allow_rating ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nforeach ( range( 1, 5 ) as $i ):\n$return .= <<<CONTENT\n\nCONTENT;\n\nif ( $i <= $topic->averageRating() ):\n$return .= <<<CONTENT\n<img src='\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->resource( \"email\/rating_on.png\", \"core\", 'interface', false );\n$return .= <<<CONTENT\n' width='14' height='13' style='vertical-align: middle; margin-right: 2px'>\nCONTENT;\n\nelseif ( ( $i - 0.5 ) <= $topic->averageRating() ):\n$return .= <<<CONTENT\n<img src='\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->resource( \"email\/rating_half.png\", \"core\", 'interface', false );\n$return .= <<<CONTENT\n' width='14' height='13' style='vertical-align: middle; margin-right: 2px'>\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n<img src='\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->resource( \"email\/rating_off.png\", \"core\", 'interface', false );\n$return .= <<<CONTENT\n' width='14' height='13' style='vertical-align: middle; margin-right: 2px'>\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\t\t&nbsp;&nbsp;&nbsp;&nbsp;\n \t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\n\t\t\t<strong>\n\t\t\t\t<img src='\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->resource( \"email\/comment.png\", \"core\", 'interface', false );\n$return .= <<<CONTENT\n' width='13' height='12' style='vertical-align: middle'>&nbsp; \n\t\t\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t{$email->language->addToStack(\"answers_number\", FALSE, array( 'pluralize' => array( ( $topic->posts ) ? $topic->posts - 1 : 0 ) ) )}\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t{$email->language->addToStack(\"replies_number\", FALSE, array( 'pluralize' => array( ( $topic->posts ) ? $topic->posts - 1 : 0 ) ) )}\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<\/strong>\n\t\t<\/p>\n\t<\/td>\n<\/tr>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_forums_digests__item( $topic, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n{$email->language->addToStack(\"x_asked_new_question\", FALSE, array( 'htmlsprintf' => array( $topic->author()->name, $topic->container()->getTitleForLanguage( $email->language ) ) ) )}\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n{$email->language->addToStack(\"x_created_topic_in\", FALSE, array( 'htmlsprintf' => array( $topic->author()->name, $topic->container()->getTitleForLanguage( $email->language ) ) ) )}\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n: {$topic->title} ({$topic->url()})\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
