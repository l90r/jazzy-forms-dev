<?php

require_once( JZZF_CORE . '/model.php');

function jzzf_ctrl_forms() {
    $forms = jzzf_list_form_recursive();
    include('tpl-forms.php');
}