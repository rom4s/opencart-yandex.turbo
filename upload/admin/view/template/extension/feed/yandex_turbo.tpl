<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-yandex_turbo" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-primary">
					<i class="fa fa-save"></i>
				</button>
				<a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>" class="btn btn-default">
					<i class="fa fa-reply"></i>
				</a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb): ?>
				<li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
	<?php if($error_warning): ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php endif; ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<div id="category"></div>
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-yandex_turbo" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-data-feed"><?php echo $entry_yandex_categories; ?></label>
						<div class="col-sm-10">
							<div class="well well-sm" style="height: 150px; overflow: auto;">
							<?php foreach ($categories as $category): ?>
								<div class="checkbox">
									<label>
										<input 
												type="checkbox"
												name="yandex_categories[]"
												value="<?php echo $category['category_id']; ?>"
												<?php if( in_array($category['category_id'], $yandex_categories) ): ?> checked="checked"<?php endif; ?>
										/>
										<?php echo $category['name']; ?>
									</label>
								</div>
							<?php endforeach; ?>
							</div>
							<button type="button" id="button-categories-save" data-toggle="tooltip" title="<?= $text_save_selected_c; ?>" class="btn btn-primary">
								<i class="fa fa-save"></i>
							</button>
							<a style="cursor:default;" onclick="$(this).parent().find(':checkbox').prop('checked', true);">
								<?= $text_select_all; ?>
							</a> / 
							<a style="cursor:default;" onclick="$(this).parent().find(':checkbox').prop('checked', false);">
								<?= $text_unselect_all; ?>
							</a> /
							<span> Товаров: <span id="product_count"><?=$yandex_turbo_count;?></span> шт.</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_yandex_for_categories; ?></label>
						<div class="col-sm-10">
							<select name="yandex_turbo_for_categories" id="input-status" class="form-control">
							<?php if ($yandex_turbo_for_categories): ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
							<?php else: ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
							<?php endif; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="yandex_turbo_limit"><?= $entry_limit; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="yandex_turbo_limit" value="<?php echo $yandex_turbo_limit; ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="yandex_turbo_phone"><?= $entry_phone_for_call; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="yandex_turbo_phone" value="<?php echo $yandex_turbo_phone; ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="yandex_turbo_code"><?= $entry_code; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="yandex_turbo_code" value="<?php echo $yandex_turbo_code; ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-data-feed"><?php echo $entry_data_feed; ?></label>
						<div class="col-sm-10">
							<textarea rows="5" id="input-data-feed" class="form-control" readonly>
<?php foreach ($data_feed_ar as $df): ?>
<?= $df . "\n"; ?>
<?php endforeach; ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<select name="yandex_turbo_status" id="input-status" class="form-control">
							<?php if ($yandex_turbo_status): ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
							<?php else: ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
							<?php endif; ?>
							</select>
						</div>
					</div>
				</form>
			</div>
		</div>
		<p>Модуль Яндекс.Турбо для товаров. Тестировался на ocStore 2.3.0.2.3. Модуль распространяется "как есть". По вопросам поддержки, установки: mikhail.sn@yandex.ru</p>
		<p>По вопросам поддержки модификации: r.r.r.r.roman+help@x-eu.com</p>
		<style>
		.btn.btn--thanks {
			position: relative;
			background-color: #4A5759;
			border-color: #4A5759;
			color: #fff;
			overflow: hidden;
		}
		.btn.btn--thanks::after {
			position: absolute;
			content: '';
			width: 40px;
			bottom: 0;
			background: linear-gradient(-92deg,rgba(255,255,255,.2),rgba(255,255,255,.2));
			height: 120%;
			left: 0;
			transform: skewX(-42deg) translateX(-54px);
			z-index: 1;
			will-change: transform;
		}
		.btn.btn--thanks:hover:after {
			transform: skewX(-42deg) translateX(15px);
			left: 100%;
			transition: .8s ease;
		}
		</style>
		<p>
			<a class="btn btn-success" href="https://money.yandex.ru/to/410011406184999">Сказать спасибо автору</a>
			<a class="btn btn--thanks" href="https://money.yandex.ru/to/410011458012550">Сказать спасибо автору модификации</a>
		</p>
	</div>
	<script type="text/javascript"><!--
		$('#button-categories-save').on('click', function(e) {
			e.preventDefault();
			$.ajax({
				url: 'index.php?route=extension/feed/yandex_turbo/savecategories&token=<?php echo $token; ?>',
				type: 'post',
				dataType: 'json',
				data: $('[name="yandex_categories[]"]:checked').map(function(){ return 'categories[]=' + this.value; }).get().join('&'),
				beforeSend: function() {
					$('#button-category-save').button('loading');
				},
				complete: function() {
					$('#button-category-save').button('reset');
				},
				success: function(json) {
					$('.alert').remove();

					if (json['error']) {
						$('#category').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					}
					if (json['success']) {
						$.getJSON( 'index.php?route=extension/feed/yandex_turbo/getproductscount&token=<?php echo $token; ?>', function( data ) {
							$('#product_count').text( data.count );
							$('#input-data-feed').text( data.pages.join('\n') );
						});
						$('#category').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	//--></script>
</div>
<?php echo $footer; ?>