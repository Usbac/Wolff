<?php

namespace System;

Route::add('main_page', function() {
    $this->load->controller('home');
});