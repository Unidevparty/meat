<?php

namespace App\Helpers;

use App\Article;
use App\Company;
use App\Interview;
use App\News;
use App\Settings;
use Illuminate\Support\Facades\Mail;

class NotifyCompany {
    public static function notify($company_id, $published_object) {
        if ($published_object->email_sent_to_manager) return false; // Письмо уже отправили

        if (!$published_object->published) return false; // Объект не опубликован

        $company = Company::find($company_id);
        if (!$company || !$company->manager_email) return false; // Компания не найдена или не указан email менеджера

        $email_data = [
            'company_name' => $company->name,
            'name' => $published_object->name,
        ];

        $template = '';
        $subject = '';
        if ($published_object instanceof News) {
            $subject = 'Публикация новости на мясном эксперте';
            $template = 'email.note_company.news';
            $email_data['link'] = route('news.show_by_id', $published_object->id);
        } elseif ($published_object instanceof Article) {
            $subject = 'Публикация статьи на мясном эксперте';
            $template = 'email.note_company.article';
            $email_data['link'] = route('articles.show_by_id', $published_object->id);
        } elseif ($published_object instanceof Interview) {
            $subject = 'Публикация интервью на мясном эксперте';
            $template = 'email.note_company.interview';
            $email_data['link'] = route('interviews.show_by_id', $published_object->id);
        }

        if (!$template) return false;

        $email = explode(',', $company->manager_email);


            Mail::send($template, $email_data, function($message) use ($email, $subject) {
                $from = Settings::byKey('note_company_from_email')->value;

                $message->to($email)->subject($subject);
                $message->from($from);
            });




        $published_object->email_sent_to_manager = 1;
        $published_object->save();

        return true;
    }
}

