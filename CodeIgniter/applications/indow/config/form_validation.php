<?php

    $config = array(
        'permissions/edit' => array(
            array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|callback_permissionEdit_check'
            )
        )
    );