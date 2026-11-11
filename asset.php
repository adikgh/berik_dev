<?php
// Админнің дизайн файлдары (CSS, JS, иконкалар, шрифттер, логотип) dev-те көшірмесіз сол күйі ашылады:
// /asset.php?f=css/norm.css → <админ>/assets/css/norm.css. Админде өзгерсе, dev-те де өзгереді.
// ?f= түрі кез келген серверде (Apache, Nginx) жұмыс істейді; /asset.php/css/norm.css түрі де қабылданады.
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/adm_dir.php';
$p = (string)($_GET['f'] ?? '');
if ($p === '') $p = (string)($_SERVER['PATH_INFO'] ?? '');
if ($p === '' && preg_match('~/asset\.php(/[^?#]+)~', (string)($_SERVER['REQUEST_URI'] ?? ''), $m)) $p = rawurldecode($m[1]);
$p = ltrim(str_replace('\\', '/', $p), '/');
$types = ['css' => 'text/css', 'js' => 'application/javascript', 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf', 'eot' => 'application/vnd.ms-fontobject',
	'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'ico' => 'image/x-icon', 'gif' => 'image/gif'];
$root = realpath(DEV_ADM . '/assets');
$f = ($root && $p !== '') ? realpath($root . '/' . $p) : false;
$ext = strtolower(pathinfo((string)$f, PATHINFO_EXTENSION));
if (!$f || strpos($f, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($f) || !isset($types[$ext])) { http_response_code(404); exit; }
$mt = filemtime($f);
header('Content-Type: ' . $types[$ext] . ($ext == 'css' || $ext == 'js' ? '; charset=utf-8' : ''));
header('Cache-Control: public, max-age=604800');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mt) . ' GMT');
if (@strtotime((string)@$_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mt) { http_response_code(304); exit; }
if ($ext != 'css') { header('Content-Length: ' . filesize($f)); readfile($f); exit; }

// CSS ішіндегі url(../webfonts/x.woff2) не url(/assets/img/x.png) сілтемелерін де осы файл арқылы ашамыз.
$dir = dirname(str_replace('\\', '/', substr($f, strlen($root) + 1)));
echo preg_replace_callback('~url\(\s*([\'"]?)([^\'")]+)\1\s*\)~i', function ($m) use ($dir) {
	$u = trim($m[2]);
	if (preg_match('~^(data:|https?:|//|#)~i', $u)) return $m[0];
	$frag = ''; if (($h = strpos($u, '#')) !== false) { $frag = substr($u, $h); $u = substr($u, 0, $h); }
	if (($q = strpos($u, '?')) !== false) $u = substr($u, 0, $q);
	if (strpos($u, '/assets/') === 0) $u = substr($u, 8); elseif ($u[0] == '/') return $m[0]; else $u = ($dir == '.' ? '' : $dir . '/') . $u;
	$out = [];
	foreach (explode('/', $u) as $s) { if ($s === '..') array_pop($out); elseif ($s !== '' && $s !== '.') $out[] = $s; }
	return 'url("/asset.php?f=' . implode('/', $out) . $frag . '")';
}, file_get_contents($f));
