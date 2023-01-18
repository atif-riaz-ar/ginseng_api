<form style="display: none" method="post">
	<div class="input-form mt-5">
		<div class="row">
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[USERID]]</label>
				<input class="form-control" type="text" name="member_id" value="<?php echo $_GET['member_id'] ?>">
			</div>
			<div class="col-6">
				<label class=" control-label" for="type">[[TYPE]]</label>
				<input class="form-control" type="text" name="type" value="<?php echo $_GET['type'] ?>">
			</div>
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[CREDIT_TYPE]]</label>
				<input type="text" name="currency" value="<?php echo $_GET['currency']; ?>">
			</div>
		</div>
	</div>

	<div class="input-form mt-2">
		<div class="row">
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[LABEL_FROM_YEAR]]</label>
				<select name="selyrfrom" id="year_start" class="form-control">
					<?php
					$defYear = "2018";
					for ($year = date('Y'); $year > $defYear - 1; $year--) {
						$sel = $year == $_GET['selyrfrom'] ? ' selected="selected"' : '';
						echo '<option value="' . $year . '"' . $sel . ' >' . $year . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[LABEL_FROM_MONTH]]</label>
				<select name="selmonfrom" id="month_start" class="form-control">
					<?php
					for ($m = 2; $m < 14; $m++) {
						$month = date("m", mktime(0, 0, 0, $m, 0, 0));
						$monthd = date("M", mktime(0, 0, 0, $m, 0, 0));
						$sel = isset($_GET['selmonfrom']) && $month == $_GET['selmonfrom'] ? ' selected="selected"' : '';
						echo '<option value="' . $month . '"' . $sel . '>[[DEF_' . strtoupper($monthd) . ']]</option>';
					}
					?>
				</select>
			</div>
		</div>
	</div>

	<div class="input-form mt-2">
		<div class="row">
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[DEF_UNTIL_YEAR]]</label>
				<select name="selyrto" id="year_end" class="form-control">
					<?php
					$defYear = ((date('Y') - 20) > date('Y', strtotime($join_date)) ? (date('Y') - 20) : date('Y', strtotime($join_date)));
					for ($year = date('Y'); $year > $defYear - 1; $year--) {
						$sel = isset($_GET['selyrto']) && $year == $_GET['selyrto'] ? ' selected="selected"' : '';
						echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-6">
				<label class=" control-label" for="txtGroupName">[[DEF_UNTIL_MONTH]]</label>
				<select name="selmonto" id="month_end" class="form-control">
					<?php
					for ($m = 2; $m < 14; $m++) {
						$month = date("m", mktime(0, 0, 0, $m, 0, 0));
						$monthd = date("M", mktime(0, 0, 0, $m, 0, 0));
						$sel = isset($_GET['selmonto']) && $month == $_GET['selmonto'] ? ' selected="selected"' : '';
						echo '<option value="' . $month . '"' . $sel . '>[[DEF_' . strtoupper($monthd) . ']]</option>';
					}
					?>
				</select>
			</div>
		</div>
	</div>

	<div class="mt-2">
		<div class="w-full mt-4">
			<button id="submit" class="w-full btn btn-primary shadow-md mr-2">[[GET_REPORT]]</button>
		</div>
	</div>
</form>
