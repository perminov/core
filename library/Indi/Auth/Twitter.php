<?php
class Indi_Auth_Twitter
{
	const URL_REQUEST_TOKEN	= 'https://api.twitter.com/oauth/request_token';
	const URL_AUTHORIZE		= 'https://api.twitter.com/oauth/authenticate';
	const URL_ACCESS_TOKEN	= 'https://api.twitter.com/oauth/access_token';
	const URL_ACCOUNT_DATA	= 'https://api.twitter.com/1/users/show';
	
	// ��������� ����� � ������ ��������
	private $_consumer_key = '';
	private $_consumer_secret = '';
	private $_url_callback = '';
	
	// ����� ��������� ������ oauth
	private $_oauth = array();
	
	// ������������� �������-������������
	private $_user_id = 0;
	private $_screen_name = '';
	
	// ��������� �������������
	private $_text_support = false;
	
	public function __construct($consumerkey, $consumersecret, $urlcallback)
	{
		$this->_consumer_key = $consumerkey;
		$this->_consumer_secret = $consumersecret;
		$this->_url_callback = $urlcallback;
	}
	
	
	/**
	 * ��������� ��������� ������������� �����������
	 *
	 */
	public function text_support($value)
	{
		$this->_text_support = $value;
	}
	
	
	/**
	 * ������ ����
	 *
	 */
	public function request_token()
	{
		$this->_init_oauth();
		
		// ������� ���������� ������ ���� ������ �����!
		// �.�. ������ oauth_callback -> oauth_consumer_key -> ... -> oauth_version.
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode(self::URL_REQUEST_TOKEN)."&";
		$oauth_base_text .= urlencode("oauth_callback=".urlencode($this->_url_callback)."&");
		$oauth_base_text .= urlencode("oauth_consumer_key=".$this->_consumer_key."&");
		$oauth_base_text .= urlencode("oauth_nonce=".$this->_oauth['nonce']."&");
		$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
		$oauth_base_text .= urlencode("oauth_timestamp=".$this->_oauth['timestamp']."&");
		$oauth_base_text .= urlencode("oauth_version=1.0");
		
		// ��������� ����
		// �� ����� ������-����� ������ ���� ��������� & !!!
		$key = $this->_consumer_secret."&";
		
		// ��������� oauth_signature
		$this->_oauth['signature'] = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
		
		// ��������� GET-������
		$url = self::URL_REQUEST_TOKEN;
		$url .= '?oauth_callback='.urlencode($this->_url_callback);
		$url .= '&oauth_consumer_key='.$this->_consumer_key;
		$url .= '&oauth_nonce='.$this->_oauth['nonce'];
		$url .= '&oauth_signature='.urlencode($this->_oauth['signature']);
		$url .= '&oauth_signature_method=HMAC-SHA1';
		$url .= '&oauth_timestamp='.$this->_oauth['timestamp'];
		$url .= '&oauth_version=1.0';
		
		// ��������� ������
		$response = file_get_contents($url);
		
		// ������ ������ ������
		parse_str($response, $result);
		
		// ���������� � ������
		$_SESSION['user']['oauth_token'] = $this->_oauth['token'] = $result['oauth_token'];
		$_SESSION['user']['oauth_token_secret'] = $this->_oauth['token_secret'] = $result['oauth_token_secret'];
		
		// ��������� �������������
		if ($this->_text_support)
		{
			echo '<p><strong>base_text:</strong> '.$oauth_base_text.'</p>';
			echo '<p><strong>key:</strong> '.$key.'</p>';
			echo '<p><strong>oauth_signature:</strong> '.$this->_oauth['signature'].'</p>';
			echo '<p><strong>Request url:</strong> '.$url.'</p>';
			echo '<p><strong>Response result:</strong> '.$response.'</p>';
			echo '<pre><strong>Parsed result:</strong>';
			print_r($result);
			echo '</pre>';
		}
	}
	
	
	/**
	 * ������ ����
	 *
	 */
	public function authorize()
	{
		// ��������� GET-������
		$url = self::URL_AUTHORIZE;
		$url .= '?oauth_token='.$this->_oauth['token'];
		
		if ($this->_text_support)
		{
			echo '<p><strong>Authorize URL:</strong> <a href="'.$url.'">'.$url.'</a></p>';
		}
		else
		{
			header('Location: '.$url.'');
		}
	}
	
	
	/**
	 * ������ ����
	 *
	 */
	public function access_token($token, $verifier)
	{
		$this->_init_oauth();
		
		// ����� �� ���-�������
		$this->_oauth['token'] = $token;
		
		// ���������� oauth_token_secret �� ������ (��. ������� request_token)
		$this->_oauth['token_secret'] = $_SESSION['user']['oauth_token_secret'];
		
		// ����� �� ���-�������
		$this->_oauth['verifier'] = $verifier;
		
		// ������� ���������� ������ ���� ������ �����!
		// �.�. ������ oauth_callback -> oauth_consumer_key -> ... -> oauth_version.
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode(self::URL_ACCESS_TOKEN)."&";
		$oauth_base_text .= urlencode("oauth_consumer_key=".$this->_consumer_key."&");
		$oauth_base_text .= urlencode("oauth_nonce=".$this->_oauth['nonce']."&");
		$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
		$oauth_base_text .= urlencode("oauth_token=".$this->_oauth['token']."&");
		$oauth_base_text .= urlencode("oauth_timestamp=".$this->_oauth['timestamp']."&");
		$oauth_base_text .= urlencode("oauth_verifier=".$this->_oauth['verifier']."&");
		$oauth_base_text .= urlencode("oauth_version=1.0");
		
		// ��������� ���� (Consumer secret + '&' + oauth_token_secret)
		$key = $this->_consumer_secret."&".$this->_oauth['token_secret'];
		
		// ��������� auth_signature
		$this->_oauth['signature'] = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
		
		// ��������� GET-������
		$url = self::URL_ACCESS_TOKEN;
		$url .= '?oauth_nonce='.$this->_oauth['nonce'];
		$url .= '&oauth_signature_method=HMAC-SHA1';
		$url .= '&oauth_timestamp='.$this->_oauth['timestamp'];
		$url .= '&oauth_consumer_key='.$this->_consumer_key;
		$url .= '&oauth_token='.urlencode($this->_oauth['token']);
		$url .= '&oauth_verifier='.urlencode($this->_oauth['verifier']);
		$url .= '&oauth_signature='.urlencode($this->_oauth['signature']);
		$url .= '&oauth_version=1.0';
		
		// ��������� ������
		$response = file_get_contents($url);
		
		// ������ ��������� �������
		parse_str($response, $result);
		
		// �������� ������������� �������-������������ �� ���������� �������
		$this->_user_id = $result['user_id'];
		$this->_screen_name = $result['screen_name'];
		
		// ��������� �������������
		if ($this->_text_support)
		{
			echo '<p><strong>base_text:</strong> '.$oauth_base_text.'</p>';
			echo '<p><strong>key:</strong> '.$key.'</p>';
			echo '<p><strong>oauth_signature:</strong> '.$this->_oauth['signature'].'</p>';
			echo '<p><strong>request url:</strong> '.$url.'</p>';
			echo '<p><strong>Response result:</strong> '.$response.'</p>';
			echo '<pre><strong>Parsed result:</strong>';
			print_r($result);
			echo '</pre>';
		}
	}
	
	
	/**
	 * ��������� ����. ��������� ������ ������������
	 *
	 */
	public function user_data($format='json')
	{
		switch ($format)
		{
			case 'xml':
			case 'json':
				$url = self::URL_ACCOUNT_DATA . '.' . $format;
				break;
			
			default:
				return false;
				break;
		}
		
		// ��������� �����-�������
		$url .= '?screen_name='.$this->_screen_name;
		
		// ��������� ������
		$response = file_get_contents($url);
		
		// ��������� �������������
		if ($this->_text_support)
		{
			echo '<p><strong>User data url:</strong> '.$url.'</p>';
		}
		
		// ���������� ���������
		return $response;
	}
	
	
	/**
	 * ������� ��������� oauth_nonce � oauth_timestamp
	 *
	 */
	private function _init_oauth()
	{
		// ��������� oauth_nonce
		$this->_oauth['nonce'] = md5(uniqid(rand(), true));
		
		// �������� ������� ����� � ��������
		$this->_oauth['timestamp'] = time();
		
		if ($this->_text_support)
		{
			echo '<p><strong>oauth_nonce:</strong> '.$this->_oauth['nonce'].'</p>';
			echo '<p><strong>oauth_timestamp:</strong> '.$this->_oauth['timestamp'].'</p>';
		}
	}
}

?>