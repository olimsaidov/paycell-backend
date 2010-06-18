<?php

class UserDetails extends CWidget
{
    /**
     * @var User
     */
    public $model;

    public function run()
    {
        if (Yii::app()->user->role == User::ROLE_ADMINISTRATOR) {
            if ($this->model->role == User::ROLE_ADMINISTRATOR) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'administrator_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_DISTRIBUTOR) {
                echo CHtml::link(
                    $this->model->username,
                    Yii::app()->controller->createUrl('admin/showdistributor', array('id' => $this->model->id)),
                    array(
                        'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                        'class' => 'distributor_link'
                    )
                );
            } else if ($this->model->role == User::ROLE_DEALER) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'dealer_link',
                    'onclick' => 'return false'
                ));
            } else if ($this->model->role == User::ROLE_TERMINAL) {
                echo CHtml::link($this->model->username . ' (Термнал ' . $this->model->id . ')', '#', array(
                    'title' => 'Логин: ' . $this->model->username,
                    'class' => 'terminal_link',
                    'onclick' => 'return false'
                ));
            }
        } else if (Yii::app()->user->role == User::ROLE_DISTRIBUTOR) {
            if ($this->model->role == User::ROLE_ADMINISTRATOR) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'administrator_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_DISTRIBUTOR) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'distributor_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_DEALER) {
                echo CHtml::link(
                    $this->model->username,
                    Yii::app()->controller->createUrl('showdealer', array('id' => $this->model->id)),
                    array(
                        'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                        'class' => 'dealer_link'
                    )
                );
            } else if ($this->model->role == User::ROLE_TERMINAL) {
                echo CHtml::link($this->model->username . ' (Термнал ' . $this->model->id . ')', Yii::app()->controller->createUrl('showterminal', array('id' => $this->model->id)), array(
                    'title' => 'Логин: ' . $this->model->username,
                    'class' => 'terminal_link'
                ));
            }
        } else if (Yii::app()->user->role == User::ROLE_DEALER) {
            if ($this->model->role == User::ROLE_ADMINISTRATOR) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'administrator_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_DISTRIBUTOR) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'distributor_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_DEALER) {
                echo CHtml::link($this->model->username, '#', array(
                    'title' => $this->model->organization ? 'Организация: ' . $this->model->organization : '',
                    'class' => 'dealer_link',
                    'onclick' => 'return false;'
                ));
            } else if ($this->model->role == User::ROLE_TERMINAL) {
                echo CHtml::link(
                    $this->model->username . ' (Термнал ' . $this->model->id . ')',
                    Yii::app()->controller->createUrl('showterminal', array('id' => $this->model->id)),
                    array(
                        'title' => 'Логин: ' . $this->model->username . ($this->model->parent->id != Yii::app()->user->id ? ' Диллер: ' . $this->model->parent->username : ''),
                        'class' => 'terminal_link', 'style' => ($this->model->parent->id != Yii::app()->user->id ? 'color: #AAA' : '')
                    )
                );
            }
        }

    }
}
