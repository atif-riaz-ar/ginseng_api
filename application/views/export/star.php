<form style="display: none" method="post">
	<div class="form-group">
		<input type="text" class="form-control" name="userid" id="userid" value="<?php echo $userid; ?>" placeholder="[[ADM_SEARCH_MEMBER_TEXTFIELD]]">
	</div>
	<div class="form-group">
		<select name="period" id="period" class="form-control">
			<?php
			$the_month = '2019-08';
			$this_month = date('Y-m');
			$next_month = date("Y-m", strtotime("+1 month", strtotime(date("Y-m-d"))));
			while ($the_month <= $this_month) {
				$selected = $the_month == $period ? ' selected="selected"' : '';
				?>
				<option value="<?php echo $the_month ?>" <?php echo $selected; ?>><?php echo date('M Y', strtotime($the_month . "-01")); ?></option>
				<?php $the_month = date("Y-m", strtotime("+1 month", strtotime($the_month)));
			} ?>
		</select>
	</div>
	<div class="form-group">
		<select name="fl_rank" id="fl_rank" class="form-control">
			<option <?php echo $fl_rank == 'any' ? 'selected' : '' ?> value="any">Star Members
				Only
			</option>
			<option <?php echo $fl_rank == 'all' ? 'selected' : '' ?> value="all">All Members
			</option>
		</select>
	</div>
	<div class="mt-2">
		<div class="w-full mt-4">
			<button id="submit" class="w-full btn btn-primary shadow-md mr-2">[[GET_REPORT]]
			</button>
		</div>
	</div>
</form>
