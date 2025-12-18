<?php

declare(strict_types=1);

namespace Engine\Auth\Account;

use Engine\Utilities\StringFormatter;

class AccountFactory
{
    private static function formatField(string $field){
        $formattedField =  preg_replace('/user_accounts_/', '', $field);
        $formattedField = StringFormatter::camelize($formattedField);
        return $formattedField;
    }

    public static function create(array $accountData) : Account
    {
        $account = new Account();
        if ($accountData) {
            foreach ($accountData as $name => $value){
                $field = self::formatField($name);
                $account->$field = $value;
            }
        }
        return $account;
    }

}