<?php if (!defined('THINK_PATH')) exit(); if(is_array($tree)): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><dl class="cate-item">
		<dt class="cf">
			<form action="<?php echo U('goodsCategoryEdit');?>" method="post">
				<div class="btn-toolbar opt-btn cf">
					<a title="<?php echo (show_status_op($list["status"])); ?>" href="<?php echo U('goodsCategorySetStatus?ids='.$list['id'].'&status='.abs(1-$list['status']));?>" class="ajax-get"><?php echo (show_status_op($list["status"])); ?></a>
					<a title="编辑" href="<?php echo U('goodsCategoryEdit?id='.$list['id'].'&pid='.$list['pid']);?>">编辑</a>
					<a title="删除" href="<?php echo U('goodsCategoryRemove?id='.$list['id']);?>" class="confirm ajax-get">删除</a>
				</div>
				<div class="fold"><i></i></div>
				<div class="order"><input type="text" name="sort" class="text input-mini" value="<?php echo ($list["sort"]); ?>"></div>
				<div class="name">
					<span class="tab-sign"></span>
					<input type="hidden" name="id" value="<?php echo ($list["id"]); ?>">
					<input type="text" name="name" class="text" value="<?php echo ($list["name"]); ?>">
					<a class="add-sub-cate" title="添加子分类" href="<?php echo U('goodsCategoryAdd?pid='.$list['id']);?>">
						<i class="icon-add"></i>
					</a>
					<span class="help-inline msg"></span>
				</div>
			</form>
		</dt>
		<?php if(!empty($list['_'])): ?><dd>
				<?php echo R('Goods/tree', array($list['_']));?>
			</dd><?php endif; ?>
	</dl><?php endforeach; endif; else: echo "" ;endif; ?>