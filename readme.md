#Instalation manual

Inside config make

```php
return [
    'modules' => [
        'bluesnap' => [
            'class' => 'achertovsky\bluesnap\Module',
            'defaultStoreId' => 'fillme',
            'clientSideEncryptionKey' => 'fillme',
            'username' => 'fillme',
            'password' => 'fillme',
            // OR
            // code below is instruction how to fill 'authToken' field. Refer https://developers.bluesnap.com/docs/authentication
            // (module gonna do same if u provide username and pwd instead)
            // $encodedString = 'Basic '.base64_encode('username:password');
            // 'authToken' => $encodedString;
        ],
    ],
    'components' => [
        'bluesnap' => [
            'class' => 'achertovsky\bluesnap\Bluesnap',
        ],
    ],
];
```
