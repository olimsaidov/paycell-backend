<?php
return array(
    User::ROLE_GUEST => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Гость',
        'bizRule' => null,
        'data' => null
    ),
    User::ROLE_TERMINAL => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Терминал',
        'bizRule' => null,
        'data' => null
    ),
    User::ROLE_DEALER => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Диллер',
        'bizRule' => null,
        'data' => null
    ),
    User::ROLE_DISTRIBUTOR => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Дистрибютор',
        'bizRule' => null,
        'data' => null
    ),
    User::ROLE_ADMINISTRATOR => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
        'bizRule' => null,
        'data' => null
    ),
);
?>
