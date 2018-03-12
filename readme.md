#Introduction

Greetings, stranger. 
  
Module designed for those who want use BlueSnap and write less as possible code. I hope, you would enjoy it.
Please, don't hesitate talk to me and report issues.
  
Best regards,
Alexander Chertovsky

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
            'antiFraudSalt' => 'some word/phrase/anything that u want to use. it gonna be used to generate anti-fraud token. make sure only u and trusted people know it :)',
            'sellerId' => 'fillme',
        ],
    ],
    'components' => [
        'bluesnap' => [
            'class' => 'achertovsky\bluesnap\Bluesnap',
        ],
    ],
];
```

#Usage examples

Check wiki for usage examples.
