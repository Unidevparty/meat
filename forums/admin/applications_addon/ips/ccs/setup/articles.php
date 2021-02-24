<?php

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class setup_articles
{
	/**
	 * Execute selected method
	 *
	 * @param	object		Registry object
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		/* Make object */
		$this->registry =  $registry;
		$this->DB       =  $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->cache    =  $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
		
		$this->createDatabases();
	}
	
	/**
	 * We need to create the article database
	 *
	 * @return	array
	 */
	public function createDatabases()
	{
		/* Get Data */
		$data			= $this->getDatabaseData();
		$_fieldIds		= array();
		$_catIds		= array();
		$_databaseId	= 0;
		
		//-----------------------------------------
		// If a new install, set database search properly
		//-----------------------------------------
		
		if( !defined('CCS_UPGRADE') OR !CCS_UPGRADE )
		{
			$data['databases']['articles']['database_search']		= 1;
			$data['databases']['media']['database_search']			= 1;
			
			$data['fields']['articles'][1]['field_topic_format']	= '{value}';
			$data['fields']['media'][2]['field_topic_format']		= '{value}';
		}

		//-----------------------------------------
		// Articles
		//-----------------------------------------
		
		/* Insert DB, need to fix: database_database, database_field_title, database_field_content */
		$this->DB->insert( "ccs_databases", $data['databases']['articles'] );

		$_databaseId	= $this->DB->getInsertId();
		$driver         = $this->registry->dbFunctions()->getDriverType();
		
		/* Now the fields */
		foreach( $data['fields']['articles'] as $field )
		{
			$field['field_database_id']	= $_databaseId;
			
			$this->DB->insert( "ccs_database_fields", $field );
			
			$_fieldId	= $this->DB->getInsertId();
			$_fieldIds[ $field['field_key'] ]	= 'field_' . $_fieldId;
		}

		/* Update database */
		$_dbUpdate	= array(
							'database_database'			=> "ccs_custom_database_" . $_databaseId,
							'database_field_title'		=> $_fieldIds['article_title'],
							'database_field_content'	=> $_fieldIds['article_body'],
							);

		$this->DB->update( "ccs_databases", $_dbUpdate, "database_id=" . $_databaseId );
		
		$data['databases']['articles']['database_id']				= $_databaseId;
		$data['databases']['articles']['database_database']			= "ccs_custom_database_" . $_databaseId;
		$data['databases']['articles']['database_field_title']		= $_fieldIds['article_title'];
		$data['databases']['articles']['database_field_content']	= $_fieldIds['article_body'];

		/* Try to figure out "smart" permissions */
		$_normal	= array();

		$this->DB->build( array( 'select' => 'g_id', 'from' => 'groups', 'where' => "g_is_supmod=1 OR g_access_cp=1 OR g_edit_profile=1" ) );
		$this->DB->execute();

		while( $r = $this->DB->fetch() )
		{
			$_normal[ $r['g_id'] ]	= $r['g_id'];
		}

		$_normal	= count($_normal) ? ',' . implode( ',', $_normal ) . ',' : '';
		
		/* Permission index */
		$_permissions	= array(
								'app'				=> 'ccs',
								'perm_type'			=> 'databases',
								'perm_type_id'		=> $_databaseId,
								'perm_view'			=> $_normal,
								'perm_2'			=> $_normal,
								'perm_3'			=> $_normal,
								'perm_4'			=> $_normal,
								'perm_5'			=> $_normal,
								'perm_6'			=> $_normal,
								);

		$this->DB->insert( "permission_index", $_permissions );
		
		/* Now the categories */
		foreach( $data['categories']['articles'] as $category )
		{
			$category['category_database_id']	= $_databaseId;
			
			/* Fix parent id */
			if( $category['category_parent_id'] )
			{
				$category['category_parent_id']	= $_catIds[ $category['category_parent_id'] ];
			}
			
			$this->DB->insert( "ccs_database_categories", $category );
			
			/* Store parent id */
			$_catId	= $this->DB->getInsertId();
			$_catIds[ '__' . $category['category_furl_name'] . '__' ]	= $_catId;
		}
		
		/* Get driver-specific create table file */
		require_once( IPS_ROOT_PATH . '/applications_addon/ips/ccs/setup/articles_' . $driver . '.php' );/*noLibHook*/
		$_className	= "articleTables_" . $driver;
		
		$_tableCreator	= new $_className( $this->registry );
		$_tableCreator->createTable( $_databaseId, $_fieldIds );
		
		/* Populate the table */
		foreach( $data['records']['articles'] as $record )
		{
			$record['category_id']	= $_catIds[ $record['category_id'] ];
			$newRecord				= array();
			
			foreach( $record as $k => $v )
			{
				$k	= str_replace( "_field_title", $_fieldIds['article_title'], $k );
				$k	= str_replace( "_field_body", $_fieldIds['article_body'], $k );
				$k	= str_replace( "_field_date", $_fieldIds['article_date'], $k );
				$k	= str_replace( "_field_homepage", $_fieldIds['article_homepage'], $k );
				$k	= str_replace( "_field_comments", $_fieldIds['article_comments'], $k );
				$k	= str_replace( "_field_image", $_fieldIds['article_image'], $k );
				$k	= str_replace( "_field_expiry", $_fieldIds['article_expiry'], $k );
				
				$newRecord[ $k ]	= $v;
			}

			$this->DB->insert( "ccs_custom_database_{$_databaseId}", $newRecord );
		}

		//-----------------------------------------
		// Media
		//-----------------------------------------
		
		/* Do media database if this is new install */
		if( !defined('CCS_UPGRADE') OR !CCS_UPGRADE )
		{
			$_fieldIds	= array();
			
			/* Insert DB, need to fix: database_database, database_field_title, database_field_content */
			$this->DB->insert( "ccs_databases", $data['databases']['media'] );
			
			$_databaseId	= $this->DB->getInsertId();
			
			/* Now the fields */
			foreach( $data['fields']['media'] as $field )
			{
				$field['field_database_id']	= $_databaseId;
				
				$this->DB->insert( "ccs_database_fields", $field );
				
				$_fieldId	= $this->DB->getInsertId();
				$_fieldIds[ $field['field_key'] ]	= 'field_' . $_fieldId;
			}
			
			/* Update database */
			$_dbUpdate	= array(
								'database_database'			=> "ccs_custom_database_" . $_databaseId,
								'database_field_title'		=> $_fieldIds['video_title'],
								'database_field_content'	=> $_fieldIds['video_description'],
								);
	
			$this->DB->update( "ccs_databases", $_dbUpdate, "database_id=" . $_databaseId );
			
			$data['databases']['media']['database_id']				= $_databaseId;
			$data['databases']['media']['database_database']		= "ccs_custom_database_" . $_databaseId;
			$data['databases']['media']['database_field_title']		= $_fieldIds['video_title'];
			$data['databases']['media']['database_field_content']	= $_fieldIds['video_description'];

			/* Try to figure out "smart" permissions */
			$_normal	= array();

			$this->DB->build( array( 'select' => 'g_id', 'from' => 'groups', 'where' => "g_is_supmod=1 OR g_access_cp=1 OR g_edit_profile=1" ) );
			$this->DB->execute();

			while( $r = $this->DB->fetch() )
			{
				$_normal[ $r['g_id'] ]	= $r['g_id'];
			}

			$_normal	= count($_normal) ? ',' . implode( ',', $_normal ) . ',' : '';
			
			/* Permission index */
			$_permissions	= array(
									'app'				=> 'ccs',
									'perm_type'			=> 'databases',
									'perm_type_id'		=> $_databaseId,
									'perm_view'			=> $_normal,
									'perm_2'			=> $_normal,
									'perm_3'			=> $_normal,
									'perm_4'			=> $_normal,
									'perm_5'			=> $_normal,
									'perm_6'			=> $_normal,
									);
	
			$this->DB->insert( "permission_index", $_permissions );
			
			/* Now the categories */
			foreach( $data['categories']['media'] as $category )
			{
				$category['category_database_id']	= $_databaseId;
				
				/* Fix parent id */
				if( $category['category_parent_id'] )
				{
					$category['category_parent_id']	= $_catIds[ $category['category_parent_id'] ];
				}
				
				$this->DB->insert( "ccs_database_categories", $category );
				
				/* Store parent id */
				$_catId	= $this->DB->getInsertId();
				$_catIds[ '__' . $category['category_furl_name'] . '__' ]	= $_catId;
			}
			
			/* Create table */
			$_tableCreator->createTable( $_databaseId, $_fieldIds );
			
			/* Populate the table */
			foreach( $data['records']['media'] as $record )
			{
				$record['category_id']	= $_catIds[ $record['category_id'] ];
				$newRecord				= array();
				
				foreach( $record as $k => $v )
				{
					$k	= str_replace( "_field_title", $_fieldIds['video_title'], $k );
					$k	= str_replace( "_field_ytid", $_fieldIds['video_id'], $k );
					$k	= str_replace( "_field_description", $_fieldIds['video_description'], $k );
					$k	= str_replace( "_field_image", $_fieldIds['video_thumb'], $k );
					$k	= str_replace( "_field_length", $_fieldIds['video_length'], $k );
					
					$newRecord[ $k ]	= $v;
				}
				
				$this->DB->insert( "ccs_custom_database_{$_databaseId}", $newRecord );
			}
		}
		
		//-----------------------------------------
		// Caches
		//-----------------------------------------
		
		/* Rebuild category data */
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir('ccs') . '/sources/databases/categories.php', 'ccs_categories', 'ccs' );
		$catsLib	 = new $classToLoad( $this->registry );
		$catsLib->init( $data['databases']['articles'] );
		$this->recacheCategories( $catsLib );
		
		/* Do media database if this is new install */
		if( !defined('CCS_UPGRADE') OR !CCS_UPGRADE )
		{
			$catsLib->init( $data['databases']['media'] );
			$this->recacheCategories( $catsLib );
		}
		
		/* And rebuild caches */
		/* Caches already automatically rebuilt later in upgrade routine */
		//$this->cache->rebuildCache( 'ccs_databases' );
		//$this->cache->rebuildCache( 'ccs_fields' );

		/* Create or update front page cache */
		$templates	= $this->getTemplates();
		
		$cache		= array(
							'categories'		=> '*',
							'limit'				=> 10,
							'sort'				=> 'record_updated',
							'order'				=> 'desc',
							'pinned'			=> 1,
							'pagination'		=> 1,
							'template'			=> $templates['frontpage_blog_format'],
							);

		$this->cache->setCache( 'ccs_frontpage', $cache, array( 'array' => 1 ) );
	}

	/**
	 * Recache categories in a database
	 *
	 * @note	Copied from categories class to prevent issues with changing fields in newer versions
	 * @link	http://community.invisionpower.com/resources/bugs.html/_/ip-content/pre-20-upgrades-r41180
	 * @param	object	Category object
	 * @return	@e void
	 */
	public function recacheCategories( $catsLib )
	{
		$_categories	= array_keys( $catsLib->categories );

		foreach( $_categories as $_cat )
		{
			$_category	= $catsLib->categories[ $_cat ];
			
			if( !$_category['category_database_id'] )
			{
				continue;
			}

			$_update	= array(
								'category_records'					=> 0,
								'category_last_record_id'			=> 0,
								'category_last_record_date'			=> 0,
								'category_last_record_member'		=> 0,
								'category_last_record_name'			=> '',
								'category_last_record_seo_name'		=> '',
								);

			if( $this->DB->checkForField( 'category_rss_cache', 'ccs_database_categories' ) )
			{
				$_update['category_rss_cache']	= null;
			}

			if( $this->DB->checkForField( 'category_rss_cached', 'ccs_database_categories' ) )
			{
				$_update['category_rss_cached']	= 0;
			}
	
			$latest		= $this->DB->buildAndFetch( array(
														'select'	=> 'r.*',
														'from'		=> array( 'ccs_custom_database_' . $_category['category_database_id'] => 'r' ),
														'where'		=> 'r.record_approved=1 AND r.category_id=' . $_cat,
														'order'		=> 'r.record_updated DESC',
														'limit'		=> array( 0, 1 ),
														'add_join'	=> array(
																			array(
																				'select'	=> 'm.*',
																				'from'		=> array( 'members' => 'm' ),
																				'where'		=> 'm.member_id=r.member_id',
																				'type'		=> 'left',
																				)
																			)
												)		);
	
			$_update['category_last_record_id']			= intval($latest['primary_id_field']);
			$_update['category_last_record_date']		= intval($latest['record_updated']);
			$_update['category_last_record_member']		= intval($latest['member_id']);
			$_update['category_last_record_name']		= $latest['members_display_name'];
			$_update['category_last_record_seo_name']	= $latest['members_seo_name'];
			
			$count		= $this->DB->buildAndFetch( array( 'select' => 'count(*) as total', 'from' => 'ccs_custom_database_' . $_category['category_database_id'], 'where' => 'record_approved=1 AND category_id=' . $_cat ) );
			
			$_update['category_records']				= intval($count['total']);

			$this->DB->update( 'ccs_database_categories', $_update, 'category_id=' . $_cat );
			
			$catsLib->categories[ $_cat ]	= array_merge( $catsLib->categories[ $_cat ], $_update );
		}

		$this->DB->update( 'ccs_databases', array( 'database_rss_cache' => null, 'database_rss_cached' => 0 ) );
		
		return;
	}
	
	/**
	 * Define the data for the databases to be inserted
	 *
	 * @return	array
	 */
	public function getDatabaseData()
	{
		/* Get templates */
		$templates	= $this->getTemplates();
		$member		= $this->getMember();
		
		/**
		 * Define fields
		 */
		$fields			= array(
								/* Articles */
								'articles' => array(
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Заголовок",
										'field_description'			=> '',
										'field_key'					=> 'article_title',
										'field_type'				=> "input",
										'field_required'			=> 1,
										'field_user_editable'		=> 1,
										'field_position'			=> 1,
										'field_max_length'			=> 500,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 50,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Тизер",
										'field_description'			=> '',
										'field_key'					=> 'teaser_paragraph',
										'field_type'				=> "editor",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 2,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										'field_topic_format'		=> '{value}<br /><br />',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Текст",
										'field_description'			=> '',
										'field_key'					=> 'article_body',
										'field_type'				=> "editor",
										'field_required'			=> 1,
										'field_user_editable'		=> 1,
										'field_position'			=> 3,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										'field_topic_format'		=> '{value}',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Дата публикации",
										'field_description'			=> '',
										'field_key'					=> 'article_date',
										'field_type'				=> "date",
										'field_required'			=> 1,
										'field_user_editable'		=> 1,
										'field_position'			=> 3,
										'field_max_length'			=> 0,
										'field_extra'				=> 'short',
										'field_html'				=> 0,
										'field_is_numeric'			=> 1,
										'field_truncate'			=> 0,
										'field_default_value'		=> 'Now',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Дата архивации",
										'field_description'			=> 'При наступлении этой даты запись будет перемещена в архив',
										'field_key'					=> 'article_expiry',
										'field_type'				=> "date",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 4,
										'field_max_length'			=> 0,
										'field_extra'				=> 'short',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 0,
										'field_display_display'		=> 0,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Дата запрета комментариев",
										'field_description'			=> 'При наступлении этой даты больше нельзя добавлять новые комментарии',
										'field_key'					=> 'article_cutoff',
										'field_type'				=> "date",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 5,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 0,
										'field_display_display'		=> 0,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Отображать на домашней странице",
										'field_description'			=> '',
										'field_key'					=> 'article_homepage',
										'field_type'				=> "checkbox",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 6,
										'field_max_length'			=> 0,
										'field_extra'				=> '1=Да',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '1',
										'field_display_listing'		=> 0,
										'field_display_display'		=> 0,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Разрешить комментарии?",
										'field_description'			=> '',
										'field_key'					=> 'article_comments',
										'field_type'				=> "radio",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 7,
										'field_max_length'			=> 0,
										'field_extra'				=> "1=Да\n0=Нет",
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '1',
										'field_display_listing'		=> 0,
										'field_display_display'		=> 0,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Картинка для статьи",
										'field_description'			=> '',
										'field_key'					=> 'article_image',
										'field_type'				=> "upload",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 8,
										'field_max_length'			=> 0,
										'field_extra'				=> 'gif,jpg,jpeg,png',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									),

								/* Media */
								'media' => array(
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Заголовок видео",
										'field_description'			=> 'Основной заголовок видео-файла',
										'field_key'					=> 'video_title',
										'field_type'				=> "input",
										'field_required'			=> 1,
										'field_user_editable'		=> 1,
										'field_position'			=> 1,
										'field_max_length'			=> 80,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "YouTube ID",
										'field_description'			=> 'Идентификатор видео на YouTube',
										'field_key'					=> 'video_id',
										'field_type'				=> "input",
										'field_required'			=> 1,
										'field_user_editable'		=> 1,
										'field_position'			=> 2,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Описание",
										'field_description'			=> 'Краткое описание видео',
										'field_key'					=> 'video_description',
										'field_type'				=> "textarea",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 3,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Эскиз",
										'field_description'			=> 'Изображение для отображения на страницах системы',
										'field_key'					=> 'video_thumb',
										'field_type'				=> "upload",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 4,
										'field_max_length'			=> 0,
										'field_extra'				=> 'gif,jpg,jpeg,png',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									array(
										'field_database_id'			=> 0,				// Fix this
										'field_name'				=> "Длина",
										'field_description'			=> 'Длина видео',
										'field_key'					=> 'video_length',
										'field_type'				=> "input",
										'field_required'			=> 0,
										'field_user_editable'		=> 1,
										'field_position'			=> 5,
										'field_max_length'			=> 0,
										'field_extra'				=> '',
										'field_html'				=> 0,
										'field_is_numeric'			=> 0,
										'field_truncate'			=> 0,
										'field_default_value'		=> '',
										'field_display_listing'		=> 1,
										'field_display_display'		=> 1,
										'field_format_opts'			=> '',
										'field_validator'			=> '',
										),
									),
								);

		/**
		 * Define databases
		 */
		$databases		= array(
								/* Articles */
								'articles' => array(
									'database_name'					=> "Статьи",
									'database_key'					=> "articles9283759273592",	// Random to prevent conflict
									'database_database'				=> "",						// Fix this
									'database_description'			=> "Управление статьями",
									'database_field_count'			=> count($fields['articles']),
									'database_record_count'			=> 8,
									'database_template_categories'	=> $templates['article_categories'],
									'database_template_listing'		=> $templates['article_archives'],
									'database_template_display'		=> $templates['article_view'],
									'database_all_editable'			=> 0,
									'database_revisions'			=> 1,
									'database_field_title'			=> "field_",				// Fix this
									'database_field_sort'			=> "record_updated",
									'database_field_direction'		=> "desc",
									'database_field_perpage'		=> 25,
									'database_comment_approve'		=> 0,
									'database_record_approve'		=> 1,
									'database_rss'					=> 0,
									'database_rss_cache'			=> null,
									'database_field_content'		=> "field_",				// Fix this
									'database_lang_sl'				=> "статья",
									'database_lang_slr'				=> "статьи",
									'database_lang_slv'				=> "статью",
									'database_lang_pl'				=> "статьи",
									'database_lang_plr'				=> "статей",
									'database_lang_plt'				=> "статьями",
									'database_lang_su'				=> "Статья",
									'database_lang_pu'				=> "Статьи",
									'database_comment_bump'			=> 0,
									'database_is_articles'			=> 1,
									'database_featured_article'		=> 0,
									'database_forum_record'			=> 0,
									'database_forum_comments'		=> 0,
									'database_forum_delete'			=> 0,
									'database_forum_forum'			=> 0,
									'database_forum_prefix'			=> '',
									'database_forum_suffix'			=> '',
									),

									
								/* Media */
								'media' => array(
									'database_name'					=> "Демо Медиа",
									'database_key'					=> "media_demo",
									'database_database'				=> "",						// Fix this
									'database_description'			=> "Медиа-раздел, может содержать видео. Демонстрация возможностей системы баз данных IP.Content.",
									'database_field_count'			=> count($fields['media']),
									'database_record_count'			=> 3,
									'database_template_categories'	=> $templates['media_categories'],
									'database_template_listing'		=> $templates['media_listing'],
									'database_template_display'		=> $templates['media_display'],
									'database_all_editable'			=> 0,
									'database_revisions'			=> 0,
									'database_field_title'			=> "field_",				// Fix this
									'database_field_sort'			=> "record_updated",
									'database_field_direction'		=> "desc",
									'database_field_perpage'		=> 25,
									'database_comment_approve'		=> 1,
									'database_record_approve'		=> 0,
									'database_rss'					=> 0,
									'database_rss_cache'			=> null,
									'database_field_content'		=> "field_",				// Fix this
									'database_lang_sl'				=> "видео",
									'database_lang_slr'				=> "видео",
									'database_lang_slv'				=> "видео",
									'database_lang_pl'				=> "видео",
									'database_lang_plr'				=> "видео",
									'database_lang_plt'				=> "видео",
									'database_lang_su'				=> "Видео",
									'database_lang_pu'				=> "Видео",
									'database_comment_bump'			=> 0,
									'database_is_articles'			=> 0,
									'database_featured_article'		=> 0,
									'database_forum_record'			=> 0,
									'database_forum_comments'		=> 0,
									'database_forum_delete'			=> 0,
									'database_forum_forum'			=> 0,
									'database_forum_prefix'			=> '',
									'database_forum_suffix'			=> '',
									),
								);

		/**
		 * Define the categories
		 */
		$categories		= array(
								/* Articles */
								'articles'	=> array(
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Статьи",
										'category_parent_id'			=> 0,
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Статьи с описанием возможностей модуля статей IP.Content",
										'category_position'				=> 1,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "articles",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Интеграция с форумом",
										'category_parent_id'			=> '__articles__',
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Изучение возможностей интеграции с форумом",
										'category_position'				=> 2,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "forum",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Первая полоса",
										'category_parent_id'			=> '__articles__',
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Изучение построения первой полосы в IP.Content",
										'category_position'				=> 3,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "frontpage",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Страницы",
										'category_parent_id'			=> 0,
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Статьи по управлению страницами",
										'category_position'				=> 4,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "pages",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Дополнительные функции",
										'category_parent_id'			=> 0,
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Статьи с описанием возможностей других модулей системы IP.Content",
										'category_position'				=> 5,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "misc",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Базы данных",
										'category_parent_id'			=> '__misc__',
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Статьи по базам данных IP.Content",
										'category_position'				=> 6,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "databases",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Шаблоны",
										'category_parent_id'			=> '__misc__',
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Настройте шаблоны, чтобы выжать максимум из IP.Content",
										'category_position'				=> 7,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "templates",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Медиа",
										'category_parent_id'			=> '__misc__',
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Управление медиа-контентом в IP.Content",
										'category_position'				=> 8,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "media",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> $templates['frontpage_blog_format'],
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									),
									
								/* Media */
								'media'	=> array(
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Новые возможности",
										'category_parent_id'			=> 0,
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Узнайте об использовании новых возможностей IP.Content",
										'category_position'				=> 1,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "new",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> 0,
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									array(
										'category_database_id'			=> 0,				// Fix this
										'category_name'					=> "Прочие",
										'category_parent_id'			=> 0,
										'category_last_record_id'		=> 0,
										'category_last_record_date'		=> 0,
										'category_last_record_member'	=> 0,
										'category_last_record_name'		=> '',
										'category_last_record_seo_name'	=> '',
										'category_description'			=> "Еще несколько видео, которые могут быть вам интересны",
										'category_position'				=> 2,
										'category_records'				=> 0,
										'category_show_records'			=> 1,
										'category_has_perms'			=> 0,
										'category_rss'					=> 20,
										'category_rss_cache'			=> null,
										'category_furl_name'			=> "other",
										'category_meta_keywords'		=> '',
										'category_meta_description'		=> '',
										'category_template'				=> 0,
										'category_forum_override'		=> 0,
										'category_forum_record'			=> 0,
										'category_forum_comments'		=> 0,
										'category_forum_delete'			=> 0,
										'category_forum_forum'			=> 0,
										'category_forum_prefix'			=> '',
										'category_forum_suffix'			=> '',
										),
									),
								);

		/* Records */
		$records		= array(
								/* Articles */
								'articles'	=> array(
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '259487fad808714985558fe5d59a51e3',
										'category_id'			=> '__forum__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'promoting-posts-to-articles',
										'_field_title'			=> "Создание статей из сообщений",
										'_field_body'			=> "IP.Content позволяет вам из сообщений вашего форума создавать статьи в базе данных Статьи IP.Content.<br />
<br />
Администратор может настроить особенности этой процедуры в АдминЦентре в меню Другие приложения -&gt; IP.Content -&gt; Статьи -&gt; Настройки создания статей.  Вы можете включить или отключить сервис, контролировать, какие группы могут копировать и перемещать сообщения в статью, и указать еще несколько особенностей.  В IP.Content уже встроен новый хук, который добавит кнопку &quot;Создать статью&quot; под каждым сообщением.  Эта кнопка будет видна только тем пользователям, у которых есть право ее использовать согласно настройкам в АдминЦентре.<br />
<br />
Нажав на эту кнопку, вы будете перемещены на форму, где сможете обозначить все детали, необходимые для создания статьи.  Здесь можно подправить текст и заголовок, загрузить изображения и указать другие дополнительные детали. Если вам разрешено создавать статьи и копированием, и перемещением, вам также будет предложено выбрать один из этих способов.  После отправки формы IP.Content займется всем остальным.<br />
<br />
Эта новая процедура может быть весьма полезна для выделения важного контента вашего форума среди остального путем представления его на вашей главной странице. Также за вами остаются решения, копировать ли нужное сообщение (сохраняя оригинал) или перемещать и оставлять ли перекрестные ссылки на местах.  Мы уверены, что благодаря гибкости в настройках, вы найдете множество применений этой замечательной утилите, доступной теперь в IP.Content.",
										'_field_date'			=> '1268694000',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "2d39a5ac26bb53702ae33132e7ae5b4e.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> 'c9be451b983a9595b775a1db471fb7e3',
										'category_id'			=> '__frontpage__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'what-is-a-frontpage',
										'_field_title'			=> "Что такое первая полоса?",
										'_field_body'			=> "В IP.Content 2.0 термин &quot;первая полоса&quot; используется как для обозначения главной страницы модуля статей, так и для обозначения основной страницы каждой категории. Мы представили эту новую навигационную структуру для того, чтобы предоставить вам возможность лучше выделять ключевой контент, используя стандартизированный формат, доступный и понятный вашим пользователям без дополнительных пояснений. <br />
<br />
Прежде всего, у вас теперь есть возможность в АдминЦентре определить шаблоны &quot;первой полосы&quot; для модуля статей. В комплект IP.Content включены 3 стандартных макета:<br />
 [list]<br />
[*][b]Макет 1x2x2[/b]<br />
Этот макет отображает статьи в традиционном 'новостном' стиле.<br />
[*][b]Формат блога[/b]<br />
Этот формат отображает статьи в стиле блога.<br />
[*][b]Одна колонка[/b]<br />
Этот макет расположит статьи в одной колонке, одна над другой.<br />
[/list]<br />
Вы можете использовать один или несколько из этих макетов, а можете создавать свои. Эксперименты с отображением статей в разных форматах на вашей главной странице помогут определить, какой макет вашим пользователям нравится больше. <br />
<br />
Статьи должны быть отмечены флагом &quot;Показывать на первой полосе&quot;, чтобы попасть на первую полосу главной страницы.<br />
<br />
В дополнение к первой полосе главной страницы, у каждой категории есть своя собственная первая полоса. Первые полосы категорий идентичны по функциональности первой полосе главной страницы, за исключением двух факторов:<br />
 [list]<br />
[*]Будут отображены только записи этой категории (и ее подкатегорий)<br />
[*]Флаг &quot;показывать на первой полосе&quot; не учитывается для первой полосы категории<br />
[/list]<br />
Вы можете комфортно просматривать и управлять настройками статей для отображения на первой полосе из нового раздела в АдминЦентре &quot;Менеджер первой полосы&quot;. Мы уверены, что эта новая зона раздела статей поможет вам выделить ключевые статьи и увеличить взаимодействие пользователей с вашим разделом статей.",
										'_field_date'			=> '1268780400',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "9a9fa3438b80e405b838b7c7227785c8.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> 'fdba36f3e898e8149622704f4c7ee6ce',
										'category_id'			=> '__media__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'media-management',
										'_field_title'			=> "Управление Медиа",
										'_field_body'			=> "Медиа-менеджер в АдминЦентре IP.Content позволяет легко и быстро управлять файлами мультимедиа, которые вам может понадобится использовать в IP.Content. Конечно, вы могли бы загрузить эти файлы по FTP или использовать ссылку на файлы другого сайта, но гораздо удобнее может оказаться загрузка таких файлов через медиа-менеджер в АдминЦентре с копированием ссылки на них для последующего использования в страницах, шаблонах и блоках. Кроме того, медиа-файлы, загруженные через Медиа-менеджер IP.Content, проще вставлять одним кликом, используя вспомогательное окно Тег Шаблона, доступное во время редактирования страниц, блоков и шаблонов.<br />
<br />
Используя медиа-менеджер, вы можете создавать папки, загружать файлы, а также перемещать, переименовывать и удалять файлы и папки. При просмотре списка файлов вы можете видеть их предварительный просмотр (если таковой для файла доступен), а выделение файла покажет вам некоторую дополнительную информацию по нему. Так же вы можете кликнуть по файлу правой кнопкой и использовать пункт контекстного меню 'Копировать адрес ссылки', чтобы быстро получить ссылку на файл.<br />
<br />
Эта утилита может сэкономить немало времени, когда вам нужно просто быстро загрузить изображение для страницы, блока или шаблона. Медиа-папка определена в файле media_path.php корневой директории вашего форума, давая тем самым вам свободу в перемещении и организации ваших путей нужным вам образом.",
										'_field_date'			=> '1268780400',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "4453d2bd25df7d318936bb300ef94203.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> 'd367083d21baf5bac9eeffff0b703843',
										'category_id'			=> '__pages__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'page-management',
										'_field_title'			=> "Управление страницами",
										'_field_body'			=> "В интерфейс менеджера страниц встроено множество полезных утилит, призванных помочь вам эффективно работать со страницами и папками IP.Content.<br />
<br />
Прежде всего, навигацию в папке обеспечивает AJAX, что позволяет загрузку содержимого папок и их просмотр осуществлять без необходимости перехода на новую страницу.<br />
<br />
Мы также обновили некоторые общие процедуры управления, внедрив в них поддержку AJAX, что помогло облегчить процесс управления вашими страницами. Такие действия, например, как очистка и удаление папок, производятся теперь без необходимости обновления страницы, что позволяет вашей управленческой деятельности протекать более плавно и быстро. <br />
<br />
Кроме того, интерфейс в целом был обновлен, предоставив более красивые и гладкие зоны управления страницами. Мы обнаружили, что на практике многие администраторы проводят львиную долю времени, настраивая и используя IP.Content в зонах управления страницами. Это вызвало у нас желание обновить пользовательский интерфейс, сделав подобную деятельность настолько легкой и приятной, насколько это вообще возможно. Мелкие детали, типа диалогов подтверждения, были обновлены, чтобы сделать все вместе более последовательным.<br />
<br />
Новая панель фильтра с поддержкой AJAX, что позволяет возвращать результаты без необходимости обновления страницы, также была добавлена в зоны управления страницами. Вы можете начать набирать имя страницы, и живой поиск начнет работать в фоновом режиме, показывая результаты по мере набора текста. Если у вас множество страниц и папок (и множество страницы в этим множестве папок), вы по достоинству оцените ускорение навигации в зоне управления страницами IP.Content, которое дает использование новой панели фильтра при определении местонахождения ваших страниц.<br />
<br />
Помимо этого, мы модернизировали зону управления страницами IP.Content, отточив мелкие детали, в попытке сделать вашу деятельность еще более приятной.",
										'_field_date'			=> '1268694000',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "01f350478adfbe32287d8e1bbbaa78d1.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '169cbc593b8a742bebe36fd26ffdedd4',
										'category_id'			=> '__templates__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'template-management',
										'_field_title'			=> "Управление шаблонами",
										'_field_body'			=> "Вы могли заметить, что АдминЦентре IP.Content существуют 4 отдельных секции шаблонов:<br />
 [list]<br />
[*][b]Шаблоны блоков[/b]<br />
[*][b]Шаблоны страниц[/b]<br />
[*][b]Шаблоны баз данных[/b]<br />
[*][b]Шаблоны статей[/b]<br />
[/list]<br />
В любой из секций шаблонов вы можете создавать категории для объединения ваших шаблонов в логические группы. Например, вы можете создать группу для каждой созданной базы данных, а затем распределить все шаблоны баз данных по своим собственным категориям. Или, может, вам захочется создать множество шаблонов первой полосы и объединить их в зоне шаблонов статей. Вы можете использовать категории так, как вам удобно, или не использовать их вовсе - это ваш выбор&#33;<br />
<br />
Порядок расположения шаблонов может быть легко изменен перетаскиванием строк вниз или вверх, таким же образом их можно перетащить из категории в категорию.<br />
<br />
При создании новых шаблонов баз данных или статей сохраняется некоторая часть мета данных о шаблонах, позволяя IP.Content адаптировать другие зоны АдминЦентра для большего удобства. Например, при создании нового шаблона в программе запоминается его тип. Это позволяет нам показывать только шаблоны &quot;списка категорий&quot; в выпадающем списке &quot;шаблон списка категорий&quot;. Точно так же, панель помощи тегов шаблона может автоматически узнать, какой тип шаблона вы редактируете, не уточняя у вас эту информацию.<br />
<br />
Правильное использование шаблонов поможет вам быстро и легко выпускать готовые страницы единой формы, а не &quot;изобретать велосипед&quot; каждый раз, когда требуется опубликовать новую страницу.",
										'_field_date'			=> '1268694000',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "18283c56b6a7e9de70581ed1c5554381.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '8b277b0e2c676c18ffe5810aacae92fa',
										'category_id'			=> '__forum__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'store-comments-in-forum',
										'_field_title'			=> "Хранение комментариев на форуме",
										'_field_body'			=> "Статьи и базы данных IP.Content вы можете отражать в темы на форуме каждый раз, когда появляется новая статья или запись в базе данных. Кроме того, IP.Content может использовать эти же автоматически сгенерированные темы в качестве хранилища комментариев этих статей или записей. Когда оставляют комментарий к статье, на самом деле он появляется как ответ в теме форума. Обратное тоже верно: ответы напрямую в теме форума также видны как комментарии к записи.<br />
<br />
Подобный функционал может быть включен на уровне базы данных или категории. Вы можете указать отдельный форум для каждой категории в вашей секции статей, например, а можете выключить комментарии для конкретной категории, оставив их включенными для всех остальных.<br />
<br />
Некоторые дополнительные опции настройки, такие как разрешение автоматически удалять тему при удалении записи или указание префиксов и&#47;или суффиксов для заголовков тем, чтобы ваши пользователи могли легче отличать темы, созданные из секции статей, дают лучший контроль над обработкой этих автоматически создаваемых тем.<br />
<br />
Форумные перекрестные ссылки позволяют администратору теснее связывать статьи и форум, давая вам лучшую возможность распространять ваш контент на более широкую аудиторию. В дополнение к этому, управление комментариями на форуме позволяет упростить обслуживание и усилить управленческие опции, используя мощный и проверенный набор функций IP.Board.",
										'_field_date'			=> '1268780400',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "024a5a7b7d7495bd655e688c9b8ed57c.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> 'f990eb16268cb7b4d305fe2960ce96ca',
										'category_id'			=> '__databases__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'sharing-links',
										'_field_title'			=> "Обмен ссылками",
										'_field_body'			=> "IP.Board 3.1 представил новую возможность, доступную любому приложению: [url=http://community.invisionpower.com/index.php?app=blog&amp;blogid=1174&amp;showentry=4162']обмен ссылками[/url].  Использование этой возможности в пользовательских модулях баз данных (и статей) IP.Content позволит вам еще проще распространять ваш контент на более широкую аудиторию.<br />
<br />
Наряду с поддержкой обмена контентом со сторонними приложениями, такими как Facebook или Twitter, вы можете также отправить статью по email, распечатать или скачать ее одним щелчком мыши по соответствующей иконке под телом статьи. Дополнительная возможность скачивания и печати позволяет делиться контентом как онлайн, так и оффлайн.<br />
<br />
Что касается именно модуля статей, то изображение к статье, которое вы загружаете во время ее создания (по выбору), автоматически попадает в Facebook, когда кто-нибудь делится в нем ссылкой. Это гарантирует что в Facebook будет использовано нужное изображение для отображения другим пользователям. Также мы извлекаем соответствующую выдержку из текстовой информации для использования в Facebook. Если пользователь авторизован в Twitter или Facebook, делиться контентом становится еще проще, вам даже не требуется покидать сайт.<br />
<br />
Мы надеемся, что предоставленные инструменты для более простого обмена контентом на вашем сайте поможет вам распространить его на более широкую аудиторию, увеличив при этом трафик и сделав контент легче и доступнее для всего мира.",
										'_field_date'			=> '1268780400',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "3eacb41863f20759407ea5587ea8c3bc.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '33910ca4a412925080cd64dc61734be6',
										'category_id'			=> '__templates__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'template-variables-help',
										'_field_title'			=> "Справка по шаблонным переменным",
										'_field_body'			=> "Когда вы редактируете содержимое в IP.Content, не важно блоки это, шаблоны или страницы, вам может понадобится много встроенных тегов для создания соответствующего контента. У блоков есть переменные, содержащие данные, которые могут оказаться полезными для ваших пользователей. Шаблоны страниц содержат переменные, выполняющие важные функции, такие как вставка заголовка страницы или пометка места, где содержимое страницы будет отображено. Почти невозможно просто запомнить каждую переменную, которая может оказаться полезной для ваших страниц и шаблонов.<br />
<br />
В IP.Content можно использовать справочную панель &quot;Теги шаблонов&quot; для облегчения этой проблемы. Панель может быть свернута, если вы в ней не нуждаетесь (и ваш выбор будет запомнен, так что вам не понадобится сворачивать ее каждый раз при загрузке нового шаблона для редактирования). Панель снабжена вкладками, предоставляющими вам различные теги на выбор в зависимости от специфики контента, который вы редактируете. Шаблоны баз данных покажут вам нужные теги для баз данных, тогда как блоки покажут теги, полезные для шаблонов блоков. При необходимости вам доступна навигация по вкладкам для лучшего определения переменных, соответствующих редактируемой зоне.<br />
<br />
Рядом с каждым тегом есть маленькая иконка, нажатие на которую приведет к автоматической вставке этого тега в место расположения курсора. Вам нет нужды вручную копировать и вставлять каждый тег - просто щелкните&#33;<br />
<br />
У некоторых тегов есть дополнительная информация или, возможно, соответствующий пример представляемых им данных. Рядом с такими тегами есть стрелка, говорящая о том, что вы можете на нее кликнуть для просмотра этих подробных данных указанного тега.<br />
<br />
Эта панель всегда доступна и динамически приспосабливается к типу редактируемого вами контента. Это просто еще одна возможность, доступная в IP.Content и созданная для того, чтобы вы могли построить ваш сайт удобным вам способом настолько эффективно, насколько это возможно.",
										'_field_date'			=> '1268784000',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "ccf9e6113c134dafd5ebe33b58a1479e.png",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '5352dc0d094b5c1494e001e404aa0cf9',
										'category_id'			=> '__templates__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'navigation-menu',
										'_field_title'			=> "Меню навигации",
										'_field_body'			=> "В IP.Content 2.3 добавлен новый функционал, призванный помочь вам в построении ваших форумов и сайта, - управление вашим главным меню навигации.<br />
<br />
Главное меню навигации - это панель &quot;вкладок&quot; наверху страницы, состоящая из ссылок на главные секции вашего сайта.  IP.Board строит ее автоматически на основе установленных приложений, но, используя IP.Content, вы сможете самостоятельно создавать быстрые ссылки на нужные вам страницы, расположив их на этой панели.  Конечно, для этого всегда можно и вручную редактировать шаблон стиля, на подобные действия влекут за собой несколько проблем:[list]<br />
[*]В будущем ваши изменения шаблонов придется вносить заново в случае обновления форума или стиля. <br />
[*]Вам придется разбираться в логике включения и выключения &quot;подсветки&quot; ваших вкладок во время их активности (а также убедиться, что у других вкладок в то же время &quot;подсветки&quot; не будет).<br />
[*]Повторять этот процесс каждый раз для новой страницы довольно трудоемко.<br />
[/list]<br />
С IP.Content 2.3 эти проблемы больше таковыми не являются. Теперь вы можете посетить страницу &quot;Меню навигации&quot;, доступную в разделе &quot;Настройки&quot; меню IP.Content в АдминЦентре, и легко создать вкладку, используя доступный интерфейс. Вы можете управлять, в каком порядке эти вкладки будут отображаться, и даже разместить их до или между вкладками стандартных приложений. Вы можете контролировать почти все аспекты вкладки: заголовок, всплывающее описание, любые нестандартные атрибуты (например, JavaScript обработчик нажатий, который будет писать лог нажатий в программе аналитики) и другие. Вы даже можете создать подменю, которые будут отображаться при наведении курсора или нажатии, включая множество ссылок под одной вкладкой.<br />
<br />
Также вы можете модифицировать множество аспектов ваших стандартных вкладок, выходящих за рамки того, что позволяет настраивать IP.Board по умолчанию. Например, используя эту утилиту, вы можете у ваших вкладок приложений создать дополнительные атрибуты, сменить заголовок и поменять описание, всплывающее при наведении курсора на вкладку.<br />
<br />
А самая лучшая часть этого нового функционала - IP.Content автоматически разберется, какую вкладку &quot;подсвечивать&quot; без дополнительных усилий с вашей стороны&#33;<br />
<br />
Мы надеемся, эта новая возможность в IP.Content 2.3 позволит вам легко превратить ваш сайт в такой, каким бы вы хотели его видеть.",
										'_field_date'			=> '1268784000',
										'_field_homepage'		=> ',1,',
										'_field_comments'		=> 1,
										'_field_expiry'			=> 0,
										'_field_image'			=> "802d7349f4e726497c3c07172c71b778.png",
										),
									),
									
								/* Media */
								'media'		=> array(
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '6bdf2e08d61f80097f6380746d13d904',
										'category_id'			=> '__new__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'promote-to-article',
										'record_static_furl'	=> 'promote-article',
										'_field_title'			=> "Promote to Article",
										'_field_ytid'			=> "uzxNFpG7ems",
										'_field_description'	=> "Learn how to use the new &quot;Promote to Article&quot; feature to copy a post to the articles section.",
										'_field_image'			=> "90000532aa1cc479ab9039f6fb3e168f.png",
										'_field_length'			=> "1:14",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> '58dcd38dcf6393e2bf47a9ebccded85a',
										'category_id'			=> '__new__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'article-management',
										'record_static_furl'	=> 'articles',
										'_field_title'			=> "Article Management",
										'_field_ytid'			=> "AznH-MXILXg",
										'_field_description'	=> "This video shows off some of the user interface you can expect to see in the article management area of the ACP.",
										'_field_image'			=> "fc04831cf5b06052b2c04930feecaed9.png",
										'_field_length'			=> "0:54",
										),
									array(
										'member_id'				=> $member['member_id'],
										'record_saved'			=> time(),
										'record_updated'		=> time(),
										'post_key'				=> 'dad19507a140862322082fdc46df1835',
										'category_id'			=> '__other__',
										'record_approved'		=> 1,
										'record_dynamic_furl'	=> 'latest-topics',
										'record_static_furl'	=> 'latest-topics',
										'_field_title'			=> "Latest Topics",
										'_field_ytid'			=> "YXTPDMDHz4I",
										'_field_description'	=> "This video shows how to create a latest topics block, showing the full post, and then adding that block to a new page.",
										'_field_image'			=> "5de8a6e992ef6124306ea1c5d480bb39.png",
										'_field_length'			=> "1:53",
										),
									),
								);

		return array( 'databases' => $databases, 'categories' => $categories, 'fields' => $fields, 'records' => $records );
	}	
	
	/**
	 * Get the templates
	 *
	 * @return	array
	 */
	public function getTemplates()
	{
		static $templates	= array();
		
		if( count($templates) )
		{
			return $templates;
		}
		
		$this->DB->build( array( 'select' => 'template_id,template_key', 'from' => 'ccs_page_templates' ) );
		$this->DB->execute();
		
		while( $r = $this->DB->fetch() )
		{
			$templates[ $r['template_key'] ]	= $r['template_id'];
		}
		
		return $templates;
	}
	
	/**
	 * Get member data for the first root admin we can find
	 *
	 * @return	array
	 */
	public function getMember()
	{
		$member	= $this->DB->buildAndFetch( array( 'select' => 'member_id,members_display_name', 'from' => 'members', 'where' => 'member_group_id=' . $this->settings['admin_group'], 'limit' => array( 0, 1 ) ) );
		
		if( !count($member) )
		{
			$member	= $this->DB->buildAndFetch( array( 'select' => 'member_id,members_display_name', 'from' => 'members', 'where' => 'member_id=1' ) );
		}
		
		if( !count($member) )
		{
			$member	= array( 'member_id' => 1, 'members_display_name' => 'Admin' );
		}

		return $member;
	}
}