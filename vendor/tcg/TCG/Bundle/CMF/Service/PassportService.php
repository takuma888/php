<?php

namespace TCG\Bundle\CMF\Service;


use Symfony\Component\HttpFoundation\Request;
use TCG\Bundle\CMF\CMFException;
use TCG\Bundle\CMF\PrivateTrait;

class PassportService
{
    use PrivateTrait;

    /**
     * @param $username
     * @param $password
     * @return bool|mixed|null|string|\TCG\Bundle\CMF\Database\MySQL\Model\User
     * @throws CMFException
     */
    public function loginByUsername($username, $password)
    {
        $username = trim($username);
        if (!$username) {
            throw new CMFException("用户名不能为空");
        }
        $password = trim($password);
        if (!$password) {
            throw new CMFException("密码不能为空");
        }
        $user = $this->providerUser()
            ->oneBy('username', $username);
        if (!$user) {
            throw new CMFException("用户不存在");
        }
        if (!$this->serviceUser()->checkPassword($user, $password)) {
            throw new CMFException("密码错误");
        }
        // 成功
        $session = $this->getRequest()->getSession();
        $session->set('uid', $user->id);
        return $user;
    }

    /**
     * logout
     */
    public function logout()
    {
        $session = $this->getRequest()->getSession();
        $session->remove('uid');
    }


    /**
     * @return bool|mixed|null|string|\TCG\Bundle\CMF\Database\MySQL\Model\User
     */
    public function login()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $userId = $session->get('uid');
        // 生成account
        $user = $this
            ->providerUser()
            ->oneById($userId);
        return $user;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return getContainer()->tag('request');
    }
}