<?php

namespace TCG\Bundle\CMF;


use Symfony\Component\HttpFoundation\Response;
use TCG\Bundle\CMF\Service\Account;
use TCG\Bundle\Twig\Component\TwigHttpExec;

abstract class CMFExec extends TwigHttpExec
{
    /**
     * @var Account;
     */
    private $_account;

    protected $permission;

    public function __invoke()
    {
        if ($this->needAuthenticate()) {
            $response = $this->authenticate();
            if ($response) {
                return $response;
            }
        }
        try {
            if ($this->_account) {
                $this->permission();
            }
            return parent::__invoke();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function setAccount(Account $account)
    {
        $this->_account = $account;
    }


    public function getAccount()
    {
        return $this->_account;
    }

    protected function needAuthenticate()
    {
        return true;
    }


    protected function permission()
    {
        if ($this->permission && $this->_account) {
            if (!$this->_account->hasPermission($this->permission)) {
                throw new CMFException("用户权限不足", CMFException::CODE_ACCOUNT_PERMISSION_DENY);
            }
        }
    }

    /**
     * 检查account
     * @return null|Response
     */
    abstract protected function authenticate();
}