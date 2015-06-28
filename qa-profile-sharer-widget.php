<?php

require(__DIR__ . '/lib/src/Facebook/autoload.php');

class qa_profile_sharer_widget {

	function allow_template($template)
	{
		return ($template=='user');
	}

	function allow_region($region)
	{
		return ($region=='main');
	}
	
	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		require_once QA_INCLUDE_DIR.'qa-app-users.php';

		$allowEdit=!qa_user_permit_error('fb_share_permit_edit');
		$parts=explode('/', qa_self_html());
		if($allowEdit && $parts[2] == qa_get_logged_in_handle()) {
			$appid = qa_opt('fb_app_id');
			$secret = qa_opt('fb_app_secret');

			$fb = new Facebook\Facebook([
				'app_id'				=> $appid,
				'app_secret'			=> $secret,
				'default_graph_version'	=> 'v2.3',
				]);

			$helper = $fb->getRedirectLoginHelper();
			$permissions 	= ['email', 'publish_actions'];
			$callback		= 'http://nathorr.com/qeta/fb-share/' . qa_get_logged_in_handle() . '/';
			$loginUrl		= $helper->getLoginUrl($callback, $permissions);
			echo '<a href="' . $loginUrl . '"><img src="http://oi57.tinypic.com/f1xlbt.jpg"></a>';
		}
	}

	function option_default($option)
	{
		if ($option=='fb_share_permit_edit') {
			require_once QA_INCLUDE_DIR.'qa-app-options.php';
			return QA_PERMIT_USERS;
		}
		return null;
	}

	function admin_form() {

		require_once QA_INCLUDE_DIR.'qa-app-admin.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';

		$permitoptions=qa_admin_permit_options(QA_PERMIT_USERS, QA_PERMIT_SUPERS, false, false);

		$saved=false;

		if (qa_clicked('fb_save_button')) {
			qa_opt('fb_app_id', qa_post_text('fb_app_id_field'));
			qa_opt('fb_app_secret', qa_post_text('fb_app_secret_field'));
			qa_opt('fb_share_permit_edit', (int)qa_post_text('fb_share_pe_field'));
			$saved=true;
		}

		$ready=strlen(qa_opt('fb_app_id')) && strlen(qa_opt('fb_app_secret'));
		return array(
			'ok' => $saved ? 'Facebook application details saved' : null,
			'fields' => array(
				array(
					'label' => 'Facebook App ID:',
					'value' => qa_html(qa_opt('fb_app_id')),
					'tags' => 'name="fb_app_id_field"',
					),
				array(
					'label' => 'Facebook App Secret:',
					'value' => qa_html(qa_opt('fb_app_secret')),
					'tags' => 'name="fb_app_secret_field"',
					'error' => $ready ? null : 'To use Facebook Login, please <a href="http://developers.facebook.com/setup/" target="_blank">set up a Facebook application</a>.',
					),
				array(
					'label' => 'Allow using:',
					'type' => 'select',
					'value' => @$permitoptions[qa_opt('fb_share_permit_edit')],
					'options' => $permitoptions,
					'tags' => 'name="fb_share_pe_field"',
					),
				),
			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="fb_save_button"',
					),
				),
			);
	}
}

