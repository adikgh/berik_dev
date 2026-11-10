<?php include "../config/core.php";
	// Граммовка шаблондарын сақтау/өшіру (JSON). Логика adm.berik.c/products/tpl/get.php-дан көшірілді.
	header('Content-Type: application/json; charset=utf-8');
	$out = function ($a) { exit(json_encode($a, JSON_UNESCAPED_UNICODE)); };
	if (!$user_id || !dev::can('tpl')) $out(['error' => 'right']);
	if (!dev_post_ok()) $out(['error' => t::w('Refresh the page', 'Бетті жаңартыңыз')]);

	if (isset($_GET['tpl_save'])) {
		$id = (int)@$_POST['id'];
		$e = function ($s) { return mysqli_real_escape_string(db::$connect, trim(strip_tags((string)$s))); };
		$name = $e(@$_POST['name']); $cat = $e(@$_POST['cat']); $keys = $e(@$_POST['keys']);
		if ($name === '') $out(['error' => 'name']);
		$items = json_decode((string)@$_POST['items'], true);
		if (!is_array($items)) $out(['error' => 'items']);
		if ($id) db::query("update grammovka_tpl set name_ru = '$name', cat = '$cat', keys_ru = '$keys', upd_dt = NOW() where id = '$id'");
		else {
			$n = (int)@mysqli_fetch_row(db::query("select max(number) from grammovka_tpl"))[0] + 1;
			db::query("insert into grammovka_tpl (name_ru, cat, keys_ru, number) values ('$name', '$cat', '$keys', '$n')");
			$id = (int)mysqli_insert_id(db::$connect);
		}
		if (!$id) $out(['error' => 'error']);
		db::query("delete from grammovka_tpl_item where tpl_id = '$id'");
		$k = 0;
		foreach ($items as $it) {
			$nm = $e(@$it['name']); $u = in_array(@$it['unit'], ['кг', 'л', 'шт']) ? $it['unit'] : 'кг';
			if ($nm === '') continue;
			$k++;
			foreach ([1, 2, 3] as $v) {
				$qn = round((float)str_replace(',', '.', (string)@$it['q'][$v - 1]), 3);
				if ($qn > 0) db::query("insert into grammovka_tpl_item (tpl_id, variant, name_ru, unit, quantity, number) values ('$id', '$v', '$nm', '$u', '$qn', '$k')");
			}
		}
		$out(['result' => 'yes', 'id' => $id]);
	}
	if (isset($_GET['tpl_del'])) {
		$id = (int)@$_POST['id'];
		$out(['result' => db::query("update grammovka_tpl set arh = 1, upd_dt = NOW() where id = '$id'") ? 'yes' : 'error']);
	}
	$out(['error' => 'unknown']);
