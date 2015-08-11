<?php

require(__DIR__ . '/lib/src/Facebook/autoload.php');

class qa_profile_sharer_page {

	function match_request($request)
	{
		$parts=explode('/', $request);
		return $parts[0]=='fb-share';
	}

	function process_request($request)
	{
		require_once QA_INCLUDE_DIR.'qa-app-users.php';

		$appid = qa_opt('fb_app_id');
		$secret = qa_opt('fb_app_secret');

		$fb = new Facebook\Facebook([
			'app_id'				=> $appid,
			'app_secret'			=> $secret,
			'default_graph_version'	=> 'v2.4',
			]);

		$qa_content = qa_content_prepare();
		$qa_content['title'] = 'Facebook Sharing Page';

		$helper = $fb->getRedirectLoginHelper();
		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo $e->getMessage();
			exit;
		}

		if (isset($accessToken)) {
			$_SESSION['fb_access_token'] = (string) $accessToken;

			$res = $fb->post( '/me/feed', array(
				'link'			=> 'http://nathorr.com/qeta/user/' . qa_get_logged_in_handle() . '/',
				'name'			=> qa_opt('fb_shared_message_title'),
				'picture'		=> qa_opt('fb_shared_message_picture'),
				'description'	=> qa_opt('fb_shared_message_description'),
				'message'		=> 'I have scored ' . qa_get_logged_in_points() . ' points and achieved some nice badges in Nathorr Q&A, check it out!'
				), $accessToken);

			$post = $res->getGraphObject();

			$qa_content['custom']='<a href="http://nathorr.com/qeta/user/' . qa_get_logged_in_handle() . '">Successfully shared, return by clicking here.</a>';
			return $qa_content;

		} else if ($helper->getError()) {
			var_dump($helper->getError());
			echo '<br><br>';
			var_dump($helper->getErrorCode());
			echo '<br><br>';
			var_dump($helper->getErrorReason());
			echo '<br><br>';
			var_dump($helper->getErrorDescription());
			echo '<br><br>';
			echo '<a href="http://nathorr.com/qeta/user/' . qa_get_logged_in_handle() . '/">Something went wrong, return by clicking here.</a>';
			exit;
		}
		http_response_code(400);
		exit;
	}
}