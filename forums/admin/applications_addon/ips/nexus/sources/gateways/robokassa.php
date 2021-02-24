<?php 
/*
Robokassa Gateway v1.3.0 for IP.Nexus v1.5.x
Author: Anton Medvedev
Email: anton@elfet.ru
--------------------------------------------------
*/

class gateway_robokassa extends gatewayCore
{
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->ipnUrl = ipsRegistry::$settings['base_url'] . 'app=nexus&module=payments&section=receive&validate=robokassa';
        $this->returnUrl = ipsRegistry::$settings['base_url'] . 'app=nexus&module=payments&section=receive';
        $this->cancelUrl = ipsRegistry::$settings['base_url'] . 'app=nexus&module=clients&section=invoices';

        if(ipsRegistry::$settings['nexus_https'] == 'https')
        {
            $this->returnUrl = str_replace('http://', 'https://', $this->returnUrl);
        }

    }


    protected function getLangShort()
    {
        $lang_short = false;
        $cache = $this->caches['lang_data'];

        if(is_array($cache) AND count($cache))
        {
            foreach($cache as $data)
            {
                if($this->member->language_id == $data['lang_id'])
                {
                    $lang_short = $data['lang_short'];
                }
                else if(!$this->member->language_id AND $data['lang_default'])
                {
                    $lang_short = $data['lang_short'];
                }
            }
        }

        return $lang_short;
    }

    public function payScreen()
    {
        $member = IPSMember::load($this->invoice->member);
        $lang_short = $this->getLangShort();

        /**
         * Далее геренация формы робокассы
         */

        $mrh_login = ipsRegistry::$settings['robokassa_login'];
        $mrh_pass1 = ipsRegistry::$settings['robokassa_password_1'];

        // Оплата заданной суммы с выбором валюты на сайте мерчанта

        // номер заказа
        $inv_id = $this->invoice->id;

        // описание заказа
        $inv_desc = $this->invoice->title;

        // сумма заказа [in WMZ]
        $out_summ = $this->invoice->total * $this->method['m_settings']['rate'];

        // предлагаемая валюта платежа
        $in_curr = $this->method['m_settings']['currency_of_payment'];

        // язык
        $culture = (strpos($lang_short, 'ru') === false ? 'en' : 'ru');

        // кодировка
        $encoding = "utf-8";

        // изменили формат транзакции, но ничго не сказали об этом в документации.
        $transaction_id = $this->transaction['t_id'];


        // формирование подписи
        $crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:shpNexusTransaction=$transaction_id");


        // ROBOKASSA form
        $button = $this->buildForm(            
            array(
                 "MrchLogin" => $mrh_login,
                 "OutSum" => $out_summ,
                 "InvId" => $inv_id,
                 "Desc" => $inv_desc,
                 "Email" => $member['email'],
                 "SignatureValue" => $crc,
                 "IncCurrLabel" => $in_curr,
                 "Culture" => $culture,
                 "shpNexusTransaction" => $transaction_id
            )
        );


        return array(
            'button' => $button,
            'formUrl' =>  ipsRegistry::$settings['robokassa_url'], 
        );
    }

    public function validatePayment()
    {
        // пароль #2
        $mrh_pass2 = ipsRegistry::$settings['robokassa_password_2'];

        // чтение параметров
        $out_summ = $_POST["OutSum"];
        $transaction_id = intval($_POST['shpNexusTransaction']);
        $inv_id = intval($_POST["InvId"]);
        $crc = $_POST["SignatureValue"];

        $crc = strtoupper($crc);
        $my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:shpNexusTransaction=$transaction_id"));

        // проверка корректности подписи
        if($my_crc != $crc)
        {
            echo "bad sign\n";
            return array($transaction_id, 'fail', $this->lang->words['gateway_robokassa_bad_sign']);
        }

        $transaction = $this->DB->buildAndFetch(array(
                                                     'select' => '*',
                                                     'from' => 'nexus_transactions',
                                                     'where' => "t_id={$transaction_id}"
                                                ));


        if(!$transaction['t_id'])
        {
            echo "data base error\n";
            return array($transaction_id, 'fail', $this->lang->words['gateway_robokassa_no_transaction']);
        }

        $invoice = new invoice( $transaction['t_invoice'] );
		if ( !$invoice->id )
		{
            echo "data base error\n";
			return array( $transaction_id, 'fail', $this->lang->words['gateway_robokassa_no_invoice'] );
		}

        // признак успешно проведенной операции
        echo "OK{$inv_id}\n";
        // тут к сожелению не завершить программу, тк нексусу нужно показать какюто хрень.
        return array('gw_id' => $transaction_id, 'status' => "okay", 'amount' => $out_summ, 'note' => '');
    }

    public function getTransactionId()
    {
        return $this->request['shpNexusTransaction'];
    }
}