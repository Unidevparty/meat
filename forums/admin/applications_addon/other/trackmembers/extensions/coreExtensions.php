<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */

class trackMemberMapping
{
	public function functionRemapToPrettyList()
	{
		$elements = array();

		$elements = array(
			'account_actions' => array(
				'onLogin'		=> 'sign_in',
				'onLogOut'		=> 'sign_out',
				'onNameChange'	=> 'change_dn',
				'onPassChange'	=> 'change_pw',
				'onEmailChange'	=> 'change_email',
			),
			'forum_actions' => array(
				'addTopic'		=> 'create_topic',
				'addReply'		=> 'reply_topic',
				'editPost'		=> 'edit_post',
				'topicSetUp'	=> 'view_topic',
				'_save'			=> 'follow_topicforum',
				'addRate'		=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system' : 'reputation_system',
				'forum_search'	=> 'forum_search',
			),
			'profile_actions' => array(
				'sendNewPersonalTopic'	=> 'new_pm',
				'sendReply'				=> 'reply_pm',
				'_viewModern'			=> 'view_profile',
				'addFriend'				=> 'new_friend',
				'removeFriend'			=> 'removed_friend',
				'create'				=> 'new_su',
				'reply'					=> 'reply_su',
				'deleteReply'			=> 'delete_su_reply',
				'deleteStatus'			=> 'delete_su',
				'member_search'			=> 'member_search',
			),
		);

		if ( IPSLib::appIsInstalled('calendar') )
		{
        	$elements['calendar_actions'] = array(
            	'addEvent'				=> 'add_event',
				'editEvent'				=> 'edit_event',
            	'addEventComment'		=> 'add_event_comment',
				'addRate_event'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_event' : 'reputation_event',
				'addRate_event_comment' => ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_event_comments' : 'reputation_event_comment',
            	'_save'					=> 'follow_event',
            	'calendar_search'		=> 'calendar_search'
            );
        }

		if ( IPSLib::appIsInstalled('downloads') )
		{
        	$elements['downloads_actions'] = array(
            	'addFile'				=> 'add_file',
            	'editFile'				=> 'edit_file',
            	'addComment'			=> 'add_file_comment',
            	'_reportBroken'			=> 'report_file_broken',
            	'addRate_file_comments'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_file_review' : 'reputation_file_review',
            	'_save'					=> 'follow_file',
            );
        }

		if ( IPSLib::appIsInstalled('gallery') )
		{
        	$elements['gallery_actions'] = array(
            	'createAlbum'		=> 'create_album',
				'addImage'			=> 'add_image',
            	'editImage'			=> 'edit_image',
            	'addImageComment'	=> 'add_image_comment',
            	'addRate_images'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_images' : 'reputation_system_images',
            	'addRate_comments'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_comments' : 'reputation_system_comments',
            	'_save'				=> 'follow_image',
            	'gallery_search'	=> 'gallery_search'
            );
        }

		if ( IPSLib::appIsInstalled('blog') )
		{
        	$elements['blog_actions'] = array(
            	'createBlog'			=> 'create_blog',
				'addEntry'				=> 'add_entry',
            	//'editEntry'			=> 'edit_entry',
            	'addEntryComment'		=> 'add_entry_comment',
            	'addRate_entry'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_entry' : 'reputation_system_entry',
            	'addRate_entry_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_entry_comments' : 'reputation_system_entry_comments',
            	'_save'					=> 'follow_entry',
            	'blog_search'			=> 'blog_search'
            );
        }

		if ( IPSLib::appIsInstalled('classifieds') )
		{
        	$elements['classifieds_actions'] = array(
            	'addAdvert'			=> 'add_advert',
				'editAdvert'		=> 'edit_advert',
				'advert_search'		=> 'advert_search'
            );
        }

		if ( IPSLib::appIsInstalled('tutorials') )
		{
        	$elements['tutorials_actions'] = array(
            	'addTutorial'				=> 'add_tutorial',
				'editTutorial'				=> 'edit_tutorial',
				'addTutorialComment'		=> 'add_tutorial_comment',
				'addRate_tutorial'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_tutorials' : 'reputation_system_tutorials',
				'addRate_tutorial_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_tutorials_comments' : 'reputation_system_tutorials_comments',
				'_save'						=> 'follow_tutorial',
				'tutorials_search'			=> 'tutorials_search'
            );
        }

		if ( IPSLib::appIsInstalled('links') )
		{
        	$elements['links_actions'] = array(
            	'addLink'				=> 'add_link',
				'editLink'				=> 'edit_link',
				'addLinkComment'		=> 'add_link_comment',
				'addRate_link'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_links' : 'reputation_system_links',
				'addRate_links_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_links_comments' : 'reputation_system_links_comments',
				'_save'					=> 'follow_link',
				'links_search'			=> 'links_search'
            );
        }

		if ( IPSLib::appIsInstalled('videos') )
		{
        	$elements['videos_actions'] = array(
            	'addVideo'				=> 'add_video',
				'editVideo'				=> 'edit_video',
				'addVideoComment'		=> 'add_video_comment',
				'addRate_video'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_videos' : 'reputation_system_videos',
				'addRate_video_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_videos_comments' : 'reputation_system_videos_comments',
				'_save'					=> 'follow_video',
				'videos_search'			=> 'videos_search',
            );
        }

		return $elements;
	}
	
	public function getDefaultSettings()
	{
		$settings = array();

		$settings =   array(
			'onLogin'				=> '1',
			'onLogOut'				=> '1',
			'onNameChange'			=> '1',
			'onPassChange'			=> '1',
			'onEmailChange'			=> '1',
			'addTopic'				=> '1',
			'addReply'				=> '1',
			'editPost'				=> '1',
			'topicSetUp'			=> '1',
			'_save'					=> '1',
			'addRate'				=> '1',
			'forum_search'			=> '1',
			'sendNewPersonalTopic'	=> '1',
			'sendReply'				=> '1',
			'_viewModern'			=> '1',
			'addFriend'				=> '1',
			'removeFriend'			=> '1',
			'create'				=> '1',
			'reply'					=> '1',
			'deleteReply'			=> '1',
			'deleteStatus'			=> '1',
			'member_search'			=> '1',
		);

		if ( IPSLib::appIsInstalled('calendar') )
		{
        	$calendar = array(
				'addEvent' 				=> '1',
				'editEvent'				=> '1',
				'addEventComment'		=> '1',
				'addRate_event'			=> '1',
				'addRate_event_comment' => '1',
				'_save'					=> '1',
				'calendar_search'		=> '1'
			);

			$settings = array_merge( $settings, $calendar );
        }

		if ( IPSLib::appIsInstalled('downloads') )
		{
        	$downloads = array(
				'addFile' 				=> '1',
				'editFile'				=> '1',
				'addComment'			=> '1',
				'_reportBroken'			=> '1',
				'addRate_file_comments'	=> '1',
				'_save'					=> '1',
			);

			$settings = array_merge( $settings, $downloads );
        }

		if ( IPSLib::appIsInstalled('gallery') )
		{

        	$gallery = array(
				'createAlbum'		=> '1',
				'addImage' 			=> '1',
				'editImage'			=> '1',
				'addImageComment'	=> '1',
				'addRate_images'	=> '1',
				'addRate_comments'	=> '1',
				'_save'				=> '1',
				'gallery_search'	=> '1'
			);

			$settings = array_merge( $settings, $gallery );
		}

		if ( IPSLib::appIsInstalled('blog') )
		{

        	$blog = array(
				'createBlog'			=> '1',
				'addEntry' 				=> '1',
				//'editEntry'			=> '1',
				'addEntryComment'		=> '1',
				'addRate_entry'			=> '1',
				'addRate_entry_comment'	=> '1',
				'_save'					=> '1',
				'blog_search'			=> '1'
			);

			$settings = array_merge( $settings, $blog );
		}

		if ( IPSLib::appIsInstalled('classifieds') )
		{

        	$classifieds = array(
				'addAdvert'			=> '1',
				'editAdvert'		=> '1',
				'advert_search'		=> '1'
			);

			$settings = array_merge( $settings, $classifieds );
		}

		if ( IPSLib::appIsInstalled('tutorials') )
		{
        	$tutorials = array(
            	'addTutorial'				=> '1',
				'editTutorial'				=> '1',
				'addTutorialComment'		=> '1',
				'addRate_tutorial'			=> '1',
				'addRate_tutorial_comment'	=> '1',
				'_save'						=> '1',
				'tutorials_search'			=> '1'
            );

			$settings = array_merge( $settings, $tutorials );
        }

		if ( IPSLib::appIsInstalled('links') )
		{
        	$links = array(
            	'addLink'				=> '1',
				'editLink'				=> '1',
				'addLinkComment'		=> '1',
				'addRate_link'			=> '1',
				'addRate_links_comment'	=> '1',
				'_save'					=> '1',
				'links_search'			=> '1'
            );

			$settings = array_merge( $settings, $links );
        }

		if ( IPSLib::appIsInstalled('videos') )
		{
        	$videos = array(
            	'addVideo'				=> '1',
				'editVideo'				=> '1',
				'addVideoComment'		=> '1',
				'addRate_video'			=> '1',
				'addRate_video_comment'	=> '1',
				'_save'					=> '1',
				'videos_search'			=> '1'
            );

			$settings = array_merge( $settings, $videos );
        }

		return $settings;
	}
	
	public function functionToLangStrings()
	{
		$lang = array();

		$lang =  array(
			'onLogin'				=> 'sign_in',
			'onLogOut'				=> 'sign_out',
			'onNameChange'			=> 'change_dn',
			'onPassChange'			=> 'change_pw',
			'onEmailChange'			=> 'change_email',
			'createtopic'			=> 'create_topic',
			'replytopic'			=> 'reply_topic',
			'editpost'				=> 'edit_post',
			'topicSetUp'			=> 'view_topic',
			'_save'					=> 'follow_topicforum',
			'addRate'				=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system' : 'reputation_system',
			'forum_search'			=> 'forum_search',
			'sendNewPersonalTopic'	=> 'new_pm',
			'sendReply'				=> 'reply_pm',
			'_viewModern'			=> 'view_profile',
			'addFriend'				=> 'new_friend',
			'removeFriend'			=> 'removed_friend',
			'create'				=> 'new_su',
			'reply'					=> 'reply_su',
			'deleteReply'			=> 'delete_su_reply',
			'deleteStatus'			=> 'delete_su',
			'member_search'			=> 'member_search',
		);

		if ( IPSLib::appIsInstalled('calendar') )
		{
        	$lang_calendar = array(
				'addEvent' 				=> 'add_event',
				'editFile'				=> 'edit_event',
				'addEventComment'		=> 'add_event_comment',
				'addRate_event'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_event' : 'reputation_system_event',
				'addRate_event_comment' => ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_event_comments' : 'reputation_system_event_comments',
				'follow'				=> 'follow_event',
				'calendar_search'		=> 'calendar_search'
			);

        	$lang = array_merge( $lang, $lang_calendar );
        }

		if ( IPSLib::appIsInstalled('downloads') )
		{
        	$lang_downloads = array(
				'addFile' 				=> 'add_file',
				'editFile'				=> 'edit_file',
				'addComment'			=> 'add_file_comment',
				'_reportBroken'			=> 'report_file_broken',
				'addRate_file_comments'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_file_review' : 'reputation_file_review',
				'follow'				=> 'follow_file',
			);

        	$lang = array_merge( $lang, $lang_downloads );
        }

		if ( IPSLib::appIsInstalled('gallery') )
		{
        	$lang_gallery = array(
				'createAlbum'		=> 'create_album',
				'addImage' 			=> 'add_image',
				'editImage'			=> 'edit_image',
				'addImageComment'	=> 'add_image_comment',
				'addRate_images'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_images' : 'reputation_system_images',
				'addRate_comments'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_comments' : 'reputation_system_comments',
				'follow'			=> 'follow_image',
				'gallery_search'	=> 'gallery_search'
			);

        	$lang = array_merge( $lang, $lang_gallery );
        }

		if ( IPSLib::appIsInstalled('blog') )
		{
        	$lang_blog = array(
				'createBlog'			=> 'create_blog',
				'addEntry' 				=> 'add_entry',
				//'editEntry'				=> 'edit_entry',
				'addEntryComment'		=> 'add_entry_comment',
				'addRate_entry'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_entry' : 'reputation_system_entry',
				'addRate_entry_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_entry_comments' : 'reputation_system_entry_comments',
				'follow'				=> 'follow_entry',
				'blog_search'			=> 'blog_search'
			);

        	$lang = array_merge( $lang, $lang_blog );
        }

		if ( IPSLib::appIsInstalled('classifieds') )
		{
        	$lang_classifieds = array(
				'addAdvert'			=> 'add_advert',
				'editAdvert' 		=> 'edit_advert',
				'advert_search'		=> 'advert_search'
			);

        	$lang = array_merge( $lang, $lang_classifieds );
        }

		if ( IPSLib::appIsInstalled('tutorials') )
		{
        	$lang_tutorials = array(
				'addTutorial'				=> 'add_tutorial',
				'editTutorial' 				=> 'edit_tutorial',
				'addTutorialComment'		=> 'add_tutorial_comment',
				'addRate_tutorial'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_tutorials' : 'reputation_system_tutorials',
				'addRate_tutorial_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_tutorials_comments' : 'reputation_system_tutorials_comments',
				'_save'						=> 'follow_tutorial',
				'tutorials_search'			=> 'tutorials_search'
			);

        	$lang = array_merge( $lang, $lang_tutorials );
        }

		if ( IPSLib::appIsInstalled('links') )
		{
        	$lang_links = array(
            	'addLink'				=> 'add_link',
				'editLink'				=> 'edit_link',
				'addLinkComment'		=> 'add_link_comment',
				'addRate_link'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_links' : 'reputation_system_links',
				'addRate_links_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_links_comments' : 'reputation_system_links_comments',
				'_save'					=> 'follow_link',
				'links_search'			=> 'links_search'
            );

        	$lang = array_merge( $lang, $lang_links );
        }

		if ( IPSLib::appIsInstalled('videos') )
		{
        	$lang_video = array(
            	'addVideo'				=> 'add_video',
				'editVideo'				=> 'edit_video',
				'addVideoComment'		=> 'add_video_comment',
				'addRate_video'			=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_videos' : 'reputation_system_videos',
				'addRate_video_comment'	=> ( ipsRegistry::$settings['reputation_point_types'] == 'like' ) ? 'like_system_videos_comments' : 'reputation_system_videos_comments',
				'_save'					=> 'follow_video',
				'videos_search'			=> 'videos_search',
            );

        	$lang = array_merge( $lang, $lang_video );
        }

		return $lang;
	}
}