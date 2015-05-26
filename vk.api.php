<?php

class VK{

	/**
	 * Токен
	 * @var string
	 */
	private $token = '';

	/**
	 * @param string $token Токен
	 */
	public function __construct($token){
		$this->token = $token;
	}

	/**
	 * Уведомить об онлайне
	 */
	public function ping(){
		$this->request('account.setOnline');
	}

	/**
	 * Получить список диалогов
	 * @return mixed|null
	 */
	public function dialogs(){
		return $this->request('messages.getDialogs',['count'=>10]);
	}

	/**
	 * Получить список не прочитаных диалогов
	 * @return array
	 */
	public function unread(){
		$response = $this->request('messages.getDialogs',['count'=>10,'unread'=>1]);
		if(!property_exists($response,'response')){
			return array();
		}else{
			return $response->response->items;
		}
	}

	/**
	 * Отметить как прочитаное
	 * @param int $userID Идентификатор пользователя
	 * @return mixed|null
	 */
	public function markAsRead($userID){
		return $this->request('messages.markAsRead',['peer_id'=>$userID]);
	}

	/**
	 * Показать активность
	 * @param int $userID Идентификатор пользователя
	 * @param string $type Тип уведомления
	 * @return mixed|null
	 */
	public function setActivity($userID,$type='typing'){
		return $this->request('messages.setActivity',['user_id'=>$userID,'type'=>$type]);
	}

	/**
	 * Отправить сообщение пользователю
	 * @param int $userID Идентификатор пользователя
	 * @param string $message Сообщение
	 * @return mixed|null
	 */
	public function sendMessage($userID,$message){
		return $this->request('messages.send',['message'=>$message,'user_id'=>$userID]);
	}

	/**
	 * Запрос к VK
	 * @param string $method Метод
	 * @param array $params Параметры
	 * @return mixed|null
	 */
	public function request($method,$params=array()){
		$url = 'https://api.vk.com/method/'.$method;
		$params['access_token']=$this->token;
		//$params['v']='5.14';
		return json_decode(file_get_contents($url.'?'.http_build_query($params)), true);
	}
}

