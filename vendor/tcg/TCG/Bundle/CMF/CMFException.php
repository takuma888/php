<?php


namespace TCG\Bundle\CMF;

class CMFException extends \Exception
{

    const CODE_CREATE_USER_BUT_USERNAME_EMAIL_MOBILE_ALL_EMPTY = 100001; // 创建用户时 用户名 邮箱 手机号码都为空
    const CODE_CREATE_USER_BUT_USERNAME_EXISTS = 100002; // 创建用户时 用户名已存在
    const CODE_CREATE_USER_BUT_EMAIL_EXISTS = 100003;
    const CODE_CREATE_USER_BUT_MOBILE_EXISTS = 100004;
    const CODE_CREATE_USER_BUT_PASSWORD_EMPTY = 100005; // 创建用户时密码为空

    const CODE_CREATE_ROLE_BUT_ROOT_EXISTS = 100006; // 创建角色时 根已存在
    const CODE_CREATE_ROLE_BUT_KEY_EXISTS = 100007; // 创建角色时 key 已存在
    const CODE_CREATE_ROLE_BUT_KEY_EMPTY = 100008; // 创建角色时 key 为空
}