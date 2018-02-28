<?php

namespace achertovsky\bluesnap\controllers;

/**
 * Contains everything of user's UI part
 *
 * @author alexander
 */
class PaymentController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ]
            ],
        ];
    }
}
