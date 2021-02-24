<?php

if ( ! defined( 'IN_IPB' ) )
{
    print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
    exit();
}

class regnotifications
{
	/**#@+
     * Registry Object Shortcuts
     *
     * @access  protected
     * @var     object
     */
    protected $registry;
    protected $DB;
    protected $settings;
    protected $request;
    protected $lang;
    protected $member;
    protected $cache;
    protected $caches;
    private   $storage;
    /**#@-*/    
    
    /**
     * Setup registry classes
     *
     * @access  public
     * @param   ipsRegistry $registry
     * @return  void
     */
    public function __construct( ipsRegistry $registry )
    {
        /* Make registry objects */
        $this->registry   =  $registry;
        $this->DB         =  $this->registry->DB();
        $this->settings   =& $this->registry->fetchSettings();
        $this->request    =& $this->registry->fetchRequest();
        $this->lang       =  $this->registry->getClass('class_localization');
        $this->member     =  $this->registry->member();
        $this->memberData =& $this->registry->member()->fetchMemberData();
        $this->cache      =  $this->registry->cache();
        $this->caches     =& $this->registry->cache()->fetchCaches();

        /**
         * Хранилище настроек
         */
        $this->storage = array(
        						'active' => $this->settings['mdmx_regnotifications_on'],
        						'groups' => $this->settings['mdmx_regnotifications_groups'],
        						'members' => $this->settings['mdmx_regnotifications_members'],
        						'template' => $this->settings['mdmx_regnotifications_template'],
        						'subject' => "Регистрация на {$this->settings['board_name']}"
        						);

    }

    /**
     * Отправка писем с информацие о пользователе
     * 
     * @access public
     * @param  array $member memberData
     * @return void
     */
    public function sendMail($member)
    {

        $to = array();

        $on_id = IPSMember::load( explode(',', $this->storage['members'] ));

        $on_group = explode(',', $this->storage['groups']);
        $on_group = array_filter( $on_group );

        if( !empty( $on_group ) )
        {
            $this->DB->build( array( 'select' => "member_id, email", 'from' => "members", 'where' => "member_group_id IN (" . implode(',', $on_group) . ")" ) );
            $this->DB->execute();   
            if( $this->DB->getTotalRows() > 0 )
            {
                while( $m = $this->DB->fetch() ) 
                {
                    $to[$m['member_id']] = $m['email'];
                }
            }
        }

        if( !empty( $on_id ) )
        {
            foreach ($on_id as $m) {
                $to[$m['member_id']] = $m['email'];
            }
        }

        if( empty( $to ) ) {
            return;
        }

        $fields = array('NAME' => $member['name'],
                        'EMAIL' => $member['email'],
                        'DISPLAYNAME' => $member['members_display_name'],
                        'DATE' => date("d.m.Y G:i", $member['joined']),
                        'IP' => $member['ip_address'],
                        'PROFILE_LINK' => $this->registry->output->buildSeoUrl( "showuser={$member['member_id']}", 'public', $member['members_seo_name'], 'showuser' )
                        );

        $profilefields = $this->profileFields();

        if( !empty( $profilefields ) )
        {
            foreach ($profilefields as $r) {
                $fields["FIELD_" . strtoupper($r['id'])] = ( $r['type'] == 'drop' ) ? $r['content'][$member["field_{$r['id']}"]] : $member["field_{$r['id']}"];
            }
        }
        
        foreach ($to as $member) {
            IPSText::getTextClass('email')->clearContent();
    		IPSText::getTextClass('email')->setHtmlEmail( false );
    		IPSText::getTextClass('email')->setPlainTextTemplate( $this->storage['template'] );
    		IPSText::getTextClass( 'email' )->buildMessage( $fields );
            IPSText::getTextClass( 'email' )->subject = $this->storage['subject'];
            IPSText::getTextClass( 'email' )->to = $member;
            IPSText::getTextClass( 'email' )->sendMail();
        }
    }

    public function profileFields()
    {
        if( !$this->caches['profilefields'] )
        {
            $this->caches['profilefields'] = $this->cache->getCache('profilefields');
        }

        if( empty( $this->caches['profilefields'] ) ) 
        {
            return;
        }

        $fields = array();

        foreach ($this->caches['profilefields'] as $f) {
            $data = array( 'id' => $f['pf_id'], 'title' => $f['pf_title'], 'type' => $f['pf_type'] );
            if( $f['pf_type'] == 'drop' )
            {
                $values = ($f['pf_content']) ? explode('|', $f['pf_content']) : array();
                if( !empty( $values ) )
                {
                    $content = array();
                    foreach ($values as $value) {
                        $value = explode('=', $value);
                        $data['content'][$value[0]] = $value[1];
                    }
                }
            }
            $fields[] = $data;
        }

        return $fields;
    }
}