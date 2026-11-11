<?php
// Админ папкасы (тіл, кіру формасы, дизайн файлдары сонікі). config/db.php анықтамаса, көршілес папкалардан ізделеді:
// live-та devb/config/db.php-ге админнің db.php-сы тікелей көшірілсе де жұмыс істейді.
if (!defined('DEV_ADM')) {
	$__root = dirname(__DIR__, 2);
	$__list = ['adm.berik.c', 'admin.pf.c', 'admb.gprog.kz', 'admin.gprog.kz', 'adm.gprog.kz', 'admb'];
	foreach ((array)glob($__root . '/*/block/sign_form.php') as $__f) $__list[] = basename(dirname($__f, 2));
	foreach ($__list as $__d) if (is_file($__root . '/' . $__d . '/config/t.php') && is_file($__root . '/' . $__d . '/block/sign_form.php')) { define('DEV_ADM', realpath($__root . '/' . $__d)); break; }
	if (!defined('DEV_ADM')) exit('Админ сайтының папкасы табылмады: devb папкасымен қатар тұруы керек (' . htmlspecialchars(implode(', ', array_slice($__list, 0, 6))) . ')');
}
