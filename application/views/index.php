<?php
echo $this->siteHeader();
$core = rtrim($_SERVER['DOCUMENT_ROOT'] . '/', '\\/') . STD . '/core/application/views/' . $this->controller . '/' . ($this->controller == 'error' ? 'index' : $this->action->alias) . '.php';
$www  = preg_replace('/core(\/application)/', 'www$1', $core);
include(is_file($www) ? $www : $core);
echo $this->siteFooter();
