<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
		<?php echo form_open('spgsinfo/menu', 'name="formA" style="display:inline-block!important;"'); ?>
		<?=form_hidden('login_id', $login_id);?>
		<?=form_hidden('login_pwd', $login_pwd);?>
		<a class="pure-menu-heading" href="javascript:void(0)" onclick="document.formA.submit();return false;"><img src="<?=base_url()?>images/spgs/<?=$header_img01?>"></a>
		<?=form_close();?>

<!--        <a class="pure-menu-heading" href="<?=$header_url01?>"><img src="<?=base_url()?>images/spgs/<?=$header_img01?>"></a> -->

        <ul>
            <li>
				<?php echo form_open('spgsinfo/menu', 'name="formB" style="display:inline-block!important;"'); ?>
				<?=form_hidden('login_id', $login_id);?>
				<?=form_hidden('login_pwd', $login_pwd);?>
				<a href="javascript:void(0)" onclick="document.formB.submit();return false;"><img src="<?=base_url()?>images/spgs/<?=$header_img02?>"></a>
				<?=form_close();?>
<!-- <a href="<?=$header_url02?>"><img src="<?=base_url()?>images/spgs/<?=$header_img02?>"></a> -->
			</li>
            <li>
				<?php echo form_open('spgsinfo/menu', 'style="display:inline-block!important;"'); ?>
				<?=form_hidden('login_id', $login_id);?>
				<?=form_hidden('login_pwd', $login_pwd);?>
				<input type="submit" class="pure-button header-button" value="メニューへ" >
				<?=form_close();?>
			</li>
            <li>
				<?php echo form_open('spgsinfo/logout/', 'style="display:inline-block!important;"'); ?>
				<input type="submit" class="pure-button header-button" value="ログアウト" >
				<?=form_hidden('errmsg', 'ログアウトしました。');?>
				<?=form_close();?>
			</li>
        </ul>
    </div>
</div>
