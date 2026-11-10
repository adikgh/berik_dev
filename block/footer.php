<? if ($user_id): ?>
		</div>
	</div>
<? endif ?>
<? foreach ($page_pop ?? [] as $pp) include __DIR__ . "/$pp.php"; // терезелер (pop_bl) мәзірден жоғары тұруы үшін бет қабатынан тыс ?>
	<? $A = '/asset.php?f='; foreach (['norm', 'main', 'berik_admin', 'berik_mobile'] as $j): ?>
	<script src="<?=$A?>js/<?=$j?>.js&v=<?=@filemtime(DEV_ADM . "/assets/js/$j.js")?>"></script>
	<? endforeach ?>
	<? foreach ($page_js ?? [] as $j): ?><script src="<?=$A?>js/<?=$j?>.js&v=<?=@filemtime(DEV_ADM . "/assets/js/$j.js")?>"></script><? endforeach ?>
	<script src="/assets/js/dev.js?v=<?=@filemtime(__DIR__ . '/../assets/js/dev.js')?>"></script>
</body>
</html>
