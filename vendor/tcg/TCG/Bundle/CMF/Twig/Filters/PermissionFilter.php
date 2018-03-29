<?php

namespace TCG\Bundle\CMF\Twig\Filters;

use TCG\Bundle\CMF\Service\Account;

class PermissionFilter implements TwigFilterInterface
{
    public static function getTwigFilter()
    {
        return new \Twig_SimpleFilter('permission', function (Account $account, $permissionId) {
            return $account->hasPermission($permissionId);
        });
    }
}