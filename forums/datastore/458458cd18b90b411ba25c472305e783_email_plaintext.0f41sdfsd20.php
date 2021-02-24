<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_forums_digests__comment( $comment, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<tr style='border-bottom: 1px solid #eeeeee'>\n\t<td dir='{dir}' width='40' align='center' valign='top' class=' '>\n\t\t<img src='{$comment->author()->get_photo( true, true )}' width='32' height='32' style='border: 1px solid #000; vertical-align: middle;'>\n\t<\/td>\n\t<td dir='{dir}' align='left'>\n\t\t<p style='font-family: \"Helvetica Neue\", helvetica, sans-serif; margin: 0; font-size: 13px; font-weight: bold'>\n\t\t\t\nCONTENT;\n\nif ( $comment->item()->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t{$email->language->addToStack(\"x_answered_a_question\", FALSE, array( 'sprintf' => array( $comment->author()->name ) ) )}\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t{$email->language->addToStack(\"x_replied_to_a_topic\", FALSE, array( 'sprintf' => array( $comment->author()->name ) ) )}\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/p>\n\t\t<h2 style=\"font-family: 'Helvetica Neue', helvetica, sans-serif; font-size: 15px; font-weight: 500 !important; margin: 0;\">\n\t\t\t<a href='{$comment->url()}' style='text-decoration: none;'>{$comment->item()->mapped('title')}<\/a>\n\t\t<\/h2>\n\t\t<div style='font-family: \"Helvetica Neue\", helvetica, sans-serif; line-height: 22px; margin-top: 5px; margin-bottom: 5px; border-left: 3px solid #8c8c8c; padding-left: 15px; font-size: 14px; margin-left: 15px'>\n\t\t\t{$email->parseTextForEmail( $comment->content(), $email->language )}\n\t\t<\/div>\n\t<\/td>\n<\/tr>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_forums_digests__comment( $comment, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( $comment->item()->isQuestion() ):\n$return .= <<<CONTENT\n{$email->language->addToStack(\"x_answered_a_question\", FALSE, array( 'htmlsprintf' => array( $comment->author()->name ) ) )}\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n{$email->language->addToStack(\"x_replied_to_a_topic\", FALSE, array( 'htmlsprintf' => array( $comment->author()->name ) ) )}\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n: {$comment->item()->mapped('title')} ({$comment->url()})\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;