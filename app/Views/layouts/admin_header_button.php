<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
		<a class="pure-menu-heading"
		href="<?= site_url('spgsadmin/menu') ?>">
			<img src="<?= base_url('images/spgs/' . $header_img01) ?>" alt="メニュー">
		</a>
        <ul>
			<li>
				<a href="<?= site_url('spgsadmin/menu') ?>">
					<img src="<?= base_url('images/spgs/' . $header_img02) ?>">
				</a>
			</li>
			<li>
				<button type="button"
					class="pure-button header-button"
					onclick="location.href='<?= site_url('spgsadmin/menu') ?>'">
					メニューへ
				</button>
			</li>
			<li>
				<button type="button"
					class="pure-button header-button"
					onclick="location.href='<?= site_url('spgsadmin/logout') ?>'">
					ログアウト
				</button>
			</li>
        </ul>
    </div>
</div>
