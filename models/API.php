<?php
/**
 * @package vkbotphp
 * @author Dmitriy Kuts <me@exileed.com>
 * @date 3/20/2015
 * @time 2:29 PM
 * @link http://exileed.com
 */

namespace models;

use VK\VK;

class API
{

    private $vk;

    public function __construct($config)
    {

        $this->vk = new VK($config['app_id'], $config['api_secret'], $config['access_token']);


    }

    /**
     * @return array
     */
    public function getUserInfo($user_id = null)
    {
        $params = [
            'name_case' => 'nom',
            'fields' => 'photo_max,online,counters,sex',
        ];
        if ($user_id != null)
            $params['user_ids'] = $user_id;

        $result = $this->vk->api('users.get', $params);

        return $result['response'][0];


    }

    /**
     * @return array
     */
    public function getFriendsRequest()
    {

        $result = $this->vk->api('friends.getRequests');

        return $result['response'];
    }

    /**
     * @return bool
     */
    public function setOnline()
    {

        $result = $this->vk->api('stats.trackVisitor');

        return $result;
    }


    /**
     * @return array
     */
    public function getMessage($count = 20)
    {

        $result = $this->vk->api('messages.getDialogs', array(
            'count' => $count,
            'out' => '0',
        ));

        return $result['response'];

    }

    public function addFriend($user_id)
    {

        $result = $this->vk->api('friends.add', [
            'user_id' => $user_id,
        ]);

        return $result;


    }

    public function markAsRead($user_id)
    {

        $result = $this->vk->api('messages.markAsRead', array(
            'peer_id' => $user_id,
        ));

        return $result;

    }

    public function setActivity($user_id, $type = 'typing')
    {

        $request = $this->vk->api('messages.setActivity', [
            'type' => $type,
            'user_id' => $user_id,
        ]);

        return $request;

    }

    public function sendMessages($user_id, $message, $params = null)
    {

        $options = [
            'message' => $message,
            'uid' => $user_id
        ];

        if ($params != null)
            $options .= $params;

        $request = $this->vk->api('messages.send', $options);

        return $request;


    }


}