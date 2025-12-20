<?php

declare(strict_types=1);

namespace Engine\Auth\Identification;

use Engine\Auth\Account\Account;
use Engine\Auth\Account\AccountFactory;
use Engine\Database\IConnector;

class Identification
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

    public function identify(string $accountName) : Account|false {
        $query = ("SELECT * FROM user_accounts WHERE user_accounts_user_name = :accountName");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['accountName' => $accountName]);
        if ($stmt->rowCount() > 0) {
            return AccountFactory::create($stmt->fetch());
        }
        return false;
    }
}