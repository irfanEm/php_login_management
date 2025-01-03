<?php 

function getDatabaseConfig(): array
{
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management_test",
                "username" => "irfanm",
                "password" => "irfan2711"
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management",
                "username" => "irfanm",
                "password" => "irfan2711"
            ]
        ]
    ];
}