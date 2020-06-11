<?php
/**
 * Класс для эмулирования работы браузера
 *
 * @version 2.4.6
 */
class Curl {

	// настройки
	public $sleep = 0; // пауза между обращениями к страницам (если указаны прокси - между очередным проходом по их списку), число или массив из 2 чисел - минимальная и максимальная продолжительность паузы
	private $use_cookies = true; // надо ли использовать cookies
	private $store_cookies = false; // надо ли сохранять куки между работой разных экземпляров класса
	private $keep_alive = true; // использовать заголовок Keep-Alive при отправлении запросов или нет

	// данные о прокси
	private $proxy_list = array(); // список прокси-адресов, под которыми curl будет обращаться к страницам (прокси берутся по очереди при каждом обращении)
	private $socks = false; // подключаться к прокси через socks5
	private $auth = ''; // данные для авторизации на прокси-сервере в формате "login:password"
	private $channel = ''; // получать страницы только через указанный прокси
	private $channel_auth = ''; // авторизация для прокси

	// служебные переменные
	private $curl_handle; // указатель на сеанс curl
	private $current_proxy = 0; // индекс текущего прокси-сервера из списка
	private $cookie_file; // файл, в котором хранятся куки
	private $last_exec = 0; // время последнего обращения к какой-либо странице
	private $last_url = ''; // последняя страница, к которой обращались


	/**
	 * Конструктор
	 *
	 * Запоминаем параметры работы curl. Если нужно - инициализируем поток.
	 *
	 * @param  bool  $keep_alive     использовать один и тот же поток при работе скрипта или под каждое обращение открывать новый
	 * @param  bool  $use_cookies    использовать куки при работе curl
	 * @param  bool  $store_cookies  хранить куки между сеансами работы скрипта или удалять
	 * @param  string  $cookie_file  имя файла для хранения кук (полностью путь)
	 */
	public function __construct($keep_alive=true, $use_cookies=true, $store_cookies=false, $cookie_file='') {
		$this->use_cookies   = $use_cookies;
		$this->store_cookies = $store_cookies;
		$this->keep_alive    = $keep_alive;
		$this->cookie_file   = ( $cookie_file ) ? $cookie_file : dirname(__FILE__).'/'.uniqid().'.txt';

		if ($this->keep_alive) $this->curl_handle = $this->init();
	}

	/**
	 * Деструктор
	 *
	 * Удаляем созданный под куки файл, если не нужно сохранить его для следующего запуска скрипта.
	 * Закрываем поток curl, если он открыт.
	 */
	public function __destruct() {
		if ( $this->keep_alive && $this->curl_handle ) curl_close($this->curl_handle);
		if ( $this->store_cookies==false && $this->cookie_file && file_exists($this->cookie_file) ) unlink($this->cookie_file);
	}

	//***********************//
	// ОБРАЩЕНИЕ К СТРАНИЦАМ //
	//***********************//
	/**
	 * Получить указанную страницу
	 *
	 * @param  string  $url          адрес страницы
	 * @param  mixed   $post         post-параметры запроса: сформированная строка запроса или массив передаваемых переменных в формате name => value
	 * @param  bool    $get_headers  включить заголовки ответа сервера в результат
	 * @param  bool    $emul         если на хосте отключён редирект для curl надо будет осуществлять его вручную
	 * @param  bool    $fast         надо ли делать паузу перед обращением к странице
	 * @param  bool    $ssl          обращение по защищённому соединению
	 * @param  bool    $ajax         надо ли эмулировать ajax-запрос
	 *
	 * @return string $html
	 */
	public function get_page($url, $post='', $get_headers=false, $emul=false, $fast=false, $ssl=false, $ajax=false) {

		// если нужно - инициализируем сеанс curl
		if ( !$this->keep_alive ) $this->curl_handle = $this->init();

		// если указано, что редирект надо реализовывать программно, то автоматический отключим$ заодно проверим, будет ли автоматический работать
		if ( $emul ) {
			curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, 0);
		} else {
			@$res = curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, 1);
			if ( !$res ) $emul = true;
		}

		// указываем, получать или нет заголовки в ответе от сервера (если редирект будет осуществляться вручную, то заголовки ответа надо получать обязательно)
		curl_setopt($this->curl_handle, CURLOPT_HEADER, ($get_headers || $emul) ? true : false);

		// указываем реферер
		if ( $this->last_url ) curl_setopt($this->curl_handle, CURLOPT_REFERER, $this->last_url);
		if ( !$fast && !$ajax ) $this->last_url = $url;

		// прокси или пауза
		if ( !$fast ) {
			if ( $this->proxy_list ) $this->set_proxy(); // если указаны прокси - устанавливаем очередной (там умная обработка, с паузами разберутся без нас)
			elseif ( $this->sleep )  $this->sleep();     // прокси нет, но нужна пауза - берём паузу
		}

		// подключаем ssl
		if ( $ssl || stripos($url, 'https')!==false ) {
			curl_setopt ($this->curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($this->curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
		}

		// указываем адрес страницы
		curl_setopt($this->curl_handle, CURLOPT_URL, $url);

		// если нужно отправить post-данные - укажем их
		if ($post) {
			curl_setopt($this->curl_handle, CURLOPT_POST, 1);
			curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $post);
		}
		// иначе укажем, что post-данные не отправляются
		else {
			curl_setopt($this->curl_handle, CURLOPT_POST, 0);
		}

		// если надо эмулировать ajax - укажем специальные заголовки
		if ( $ajax ) $this->set_headers( array('X-Requested-With: XMLHttpRequest') );

		// загружаем страницу
		$result = ($emul) ? $this->curl_redir_exec() : curl_exec($this->curl_handle);
		$this->last_exec = time();

		// вместо ответа получили ошибку - получим описание
		if ( curl_errno($this->curl_handle)!==0 ) {
			$error = date('Y-m-d H:i:s') . ": Error load page = '$url' (".curl_error($this->curl_handle).")";
			if ( $this->channel ) $error .= " ({$this->channel})";

			$result = false;
		}

		// если эмулировался ajax - восстановим заголовки
		if ( $ajax ) $this->set_headers( false );

		// убираем заголовки из текста ответа, если нужно
		if ( !$get_headers && stripos($result, 'HTTP/1.1')!==false ) {
			$cnt = substr_count($result, 'HTTP/1.1');
			$parts = explode("\r\n\r\n", $result, $cnt + 1);

			$result = end($parts);
		}

		// проверка ip
		/*
		curl_setopt($this->curl_handle, CURLOPT_POST, 0);
		curl_setopt($this->curl_handle, CURLOPT_URL, 'http://internet.yandex.ru/');
		$html = curl_exec($this->curl_handle);
		write_to_file(LOG.'/'.get_uniq_id().'.html', $url."\r\n".serialize($post)."\r\n".$html);
		*/

		// если поток открывается под каждую страницу отдельно - закрываем его
		if ( !$this->keep_alive ) curl_close($this->curl_handle);

		return $result;
	}

	/**
	 * Осуществляем редирект между страницами вручную
	 * если на сервере эта опция отключена
	 *
	 * @access  private
	 * @param  string  $location  текущая страница (на всякий случай)
	 * @return  string
	 */
	private function curl_redir_exec($location='') {

		// защита от бесконечного редиректа
		static $curl_loops = 0;
		static $curl_max_loops = 20;
		if ( $curl_loops>=$curl_max_loops ) {
			$curl_loops = 0;
			return false;
		}

		// получаем данные с сервера
		$data = curl_exec($this->curl_handle);

		// определяем, есть ли на странице редирект
		$http_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
		if ($http_code == 301 || $http_code == 302) {

			$matches = array();
			preg_match('/Location:(.*?)\n/', $data, $matches);
			$url = @parse_url(trim(array_pop($matches)));

			if (!$url) {
				$curl_loops = 0;
				return $data;
			}

			$last_url = parse_url( curl_getinfo($this->curl_handle, CURLINFO_EFFECTIVE_URL) );

			if ( !isset($url['scheme']) || !$url['scheme'] ) $url['scheme'] = $last_url['scheme'];
			if ( !isset($url['host']) || !$url['host'] ) $url['host'] = $last_url['host'];
			if ( !isset($url['path']) || !$url['path'] ) $url['path'] = $last_url['path'];
			if ( !isset($url['query']) || !$url['query'] ) $url['query'] = '';

			$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . '?' . $url['query'];
			curl_setopt($this->curl_handle, CURLOPT_URL, $new_url);
			curl_setopt($this->curl_handle, CURLOPT_POST, 0);

			++$curl_loops;

			return $this->curl_redir_exec($new_url);

		} else {

			if ( stripos($data, 'Location:')===false && $location ) $data = "Location: $location\n" . $data;

			$curl_loops = 0;
			return $data;

		}

		return '';
	}

	/**
	 * Инициализировать поток curl с указанными параметрами
	 */
	private function init() {

		$ch = curl_init();

		// если нужно - включаем куки
		if ( $this->use_cookies ) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
			curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // получать ответ от сервера

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // максимальное время ожидания подключения
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);        // максимальное время работы с соединением

		// тип браузера
		$agents = array(
			"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.162 Safari/535.19",
			"Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0",
			"Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.1)",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4",
			"Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; ru-RU)"
		);
		$agent = $agents[ array_rand($agents) ];
		curl_setopt ($ch, CURLOPT_USERAGENT, $agent);

		// отправляемые заголовки
		$headers = array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3',
			'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
			'Keep-Alive: 115',
			'Connection: keep-alive'
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		return $ch;
	}

	/**
	 * Указать новые заголовки для запросов
	 *
	 * если в качестве $headers передать false - будут восстановлены заголовки по умолчанию
	 *
	 * @param  array|string  $headers
	 */
	public function set_headers($headers=false) {

		if ( $headers===false ) {
			curl_setopt($this->curl_handle, CURLOPT_REFERER, '');
			$this->last_url = '';

			$headers = array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3',
				'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
				'Keep-Alive: 115',
				'Connection: keep-alive'
			);
		} elseif ( is_string($headers) ) {
			$headers = array( $headers );
		}

		curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $headers);
	}

	public function set_cookie($cookies) {

		if ( !$this->curl_handle ) return false;

		$cookie_str = '';
		if ( is_array($cookies) ) {
			foreach ($cookies as $k => $v) {
				$cookie_str .= "$k=$v;";
			}
		} else {
			$cookie_str = $cookies;
		}

		curl_setopt($this->curl_handle, CURLOPT_COOKIE, $cookie_str);

		return true;
	}

	public function set_timeout($time) {

		if ( !$this->curl_handle ) return false;

		curl_setopt($this->curl_handle, CURLOPT_CONNECTTIMEOUT, $time); // максимальное время ожидания подключения
		curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, $time);        // максимальное время работы с соединением

		return true;
	}

	/**
	 * Получить указанную страницу, используя новый сеанс curl
	 *
	 * @param  string  $url
	 * @param  string  $params
	 *
	 * @return  string
	 */
	public function get_html($url, $params=false) {

		// инициализируем сеанс
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);

		// указываем post-данные
		if ( $params ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}

		// подключаем прокси, если нужно
		if ( $this->proxy_list ) {

			curl_setopt( $ch, CURLOPT_PROXY, $this->proxy_list[$this->current_proxy] );

			// если в качестве прокси используется socks - укажем это
			if ( $this->socks ) {
				curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5 );
				curl_setopt( $ch, CURLOPT_HTTPPROXYTUNNEL, 1 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			}

			// если нужна авторизация для прокси - укажем в параметрах
			if ( $this->auth ) {
				curl_setopt( $ch, CURLOPT_PROXYUSERPWD, $this->auth );
				curl_setopt( $ch, CURLOPT_PROXYAUTH, 1 );
			}

		} elseif ( $this->channel ) {

			curl_setopt( $ch, CURLOPT_PROXY, $this->channel );
			if ( $this->channel_auth ) {
				curl_setopt( $ch, CURLOPT_PROXYUSERPWD, $this->channel_auth );
				curl_setopt( $ch, CURLOPT_PROXYAUTH, 1 );
			}

		}

		// получаем страницу
		$result = curl_exec($ch);
		$error = curl_errno($ch);
		curl_close($ch);

		return ( $error===0 ) ? $result : false;
	}


	//*****************//
	// РАБОТА С ПРОКСИ //
	//*****************//
	/**
	 * Указываем браузеру список прокси для соединения
	 *
	 * @access public
	 *
	 * @param array   $list   массив проксей
	 * @param bool    $socks  к указанным прокси нужно подключаться по socks5
	 * @param string  $auth   для подключения к прокси нужно авторизоваться - строка вида "login:password"
	 *
	 * @return bool
	 */
	public function use_proxy($list, $socks=false, $auth='') {
		$proxy = array();
		$list = array_unique($list);
		for ($i = 0; $i < count($list); $i++) {
			$list[$i] = trim($list[$i]);
			$list[$i] = str_replace(array("\r\n","\r","\n","\t",' '), '', $list[$i]);
			if ( $list[$i] ) $proxy[] = $list[$i];
		}

		$this->proxy_list = $proxy;
		$this->current_proxy = 0;

		$this->socks = $socks;
		$this->auth = $auth;

		return true;
	}

	/**
	 * Подключаться всегда через один прокси
	 *
	 * @param  string  $proxy
	 */
	public function set_channel($proxy, $auth='') {

		$this->channel = $proxy;
		$this->channel_auth = $auth;

		curl_setopt($this->curl_handle, CURLOPT_PROXY, $proxy);
		//curl_setopt($this->curl_handle, CURLOPT_PROXYTYPE, 4);

		if ( $auth ) {
			curl_setopt( $this->curl_handle, CURLOPT_PROXYUSERPWD, $auth );
			curl_setopt( $this->curl_handle, CURLOPT_PROXYAUTH, 1 );
		}

	}

	/**
	 * Добавить новый прокси в список используемых
	 *
	 * @param string $proxy  ip-адрес прокси
	 * @return bool
	 */
	public function add_proxy($proxy) {
		$proxy = trim($proxy);
		$this->proxy_list[] = $proxy;
	}

	/**
	 * Удалить прокси из списка
	 *
	 * если передано false - будет удалён текущий прокси
	 *
	 * @param  int  $id
	 */
	public function delete_proxy($id=false) {
		if ( $id===false ) $id = $this->current_proxy;
		unset($this->proxy_list[$id]);

		$new_list = array();
		foreach ($this->proxy_list as $item) $new_list[] = $item;
		$this->proxy_list = $new_list;

		return true;
	}

	/**
	* Переключаемся на очередной прокси из списка
	*/
	private function set_proxy() {

		if ( $this->current_proxy >= count($this->proxy_list)-1 ) { // если сейчас установлен последний прокси - берём паузу и возвращаемся к началу списка

			$this->sleep();
			$this->current_proxy = 0;

		} else { // иначе просто берём следующий прокси

			++$this->current_proxy;

		}

		curl_setopt( $this->curl_handle, CURLOPT_PROXY, $this->proxy_list[$this->current_proxy] );
		curl_setopt($this->curl_handle, CURLOPT_REFERER, '');

		// если в качестве прокси используется socks - укажем это
		if ( $this->socks ) {
			curl_setopt( $this->curl_handle, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5 );
			curl_setopt( $this->curl_handle, CURLOPT_HTTPPROXYTUNNEL, 1 );
			curl_setopt( $this->curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
		}

		// если нужна авторизация для прокси - укажем в параметрах
		if ( $this->auth ) {
			curl_setopt( $this->curl_handle, CURLOPT_PROXYUSERPWD, $this->auth );
			curl_setopt( $this->curl_handle, CURLOPT_PROXYAUTH, 1 );
		}

		return true;
	}

	/**
	 * Использовать дополнительный ip сервера
	 *
	 * @param  string  $server_ip
	 * @return  false
	 */
	public function set_interface($server_ip) {
		curl_setopt($this->curl_handle, CURLOPT_INTERFACE, $server_ip);
	}


	//*************************//
	// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ //
	//*************************//
	/**
	 * Пауза между обращениями к страницам
	 */
	private function sleep() {

		if ( is_array($this->sleep) )
			$pause = rand( $this->sleep[0], $this->sleep[1] );
		elseif ( is_numeric($this->sleep) )
			$pause = $this->sleep;
		else
			$pause = 0;

		$time = time() - $this->last_exec; // сколько прошло времени с последнего запуска
		if ( $time<$pause ) {
			$pause -= $time;
			sleep( $pause );
		}

	}

}

$curl = new Curl();
$post_fields = '{
  "jsonrpc":"2.0",
  "method": "LMT_handle_jobs",
  "params":{
    "jobs":[
      {
        "kind":"default",
        "raw_en_sentence":"e",
        "raw_en_context_before":[],
        "raw_en_context_after":[],
        "preferred_num_beams":4,
        "quality":"fast"
      }
    ],
    "lang":{
      "user_preferred_langs":["DE","RU","EN"],
      "source_lang_user_selected":"auto",
      "target_lang":"RU"
    },
    "priority":-1,
    "timestamp":1579166650093
  },
  "id":69400002
}';
var_dump($curl->get_page('https://www2.deepl.com/jsonrpc', $post_fields));