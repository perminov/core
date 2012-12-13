<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
  <title><?=$this->seoTDK('title')?></title>
  <meta name="keywords" content="<?=$this->seoTDK('keyword')?>" />
  <meta name="description" content="<?=$this->seoTDK('description')?>" />
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
  <link rel="icon" type="image/x-icon" href="/i/favicon.ico" />
  <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen, projection" />
  <link rel="stylesheet" href="/css/index.css" type="text/css" media="screen, projection" />
  <script language="javascript" type="text/javascript" src="/js/jquery-1.6.1.min.js" ></script>
  <script language="javascript" type="text/javascript" src="/js/index.js" ></script>
  <style>strong{font-weight: bold;}</style>
</head>
<body>
<?
  echo $this->siteHeader();
  $core = rtrim($_SERVER['DOCUMENT_ROOT'] . '/', '\\/') . '/core/application/views/' . $this->controller . '/' . ($this->controller == 'error' ? 'index' : $this->action->alias) . '.php';
  $www  = preg_replace('/core(\/application)/', 'www$1', $core);
  include(is_file($www) ? $www : $core);
  echo $this->siteFooter();
?>
</body>
</html>
