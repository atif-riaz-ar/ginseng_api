<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Ginseng Report Generating</title>
	<link href="<?php echo API_URL; ?>assets/loader/globe_css/globe.css" rel="stylesheet">
</head>
<body>
<table align="center" style="padding-top: 50px;">
	<tr>
		<td>
			<center>
				<div class="globe__placeholder" style="display: contents;">
					<div class="globe__container">
						<div class="globe" style="visibility: visible; opacity: 1; transform: scale(1);">
							<div class="globe__sphere"></div>
							<div class="globe__outer_shadow"></div>
							<div class="globe__reflections__bottom"></div>

							<div class="globe__worldmap">
								<div class="globe__worldmap__back velocity-animating"
									 style="transform: translateX(-105.361px);"></div>
								<div class="globe__worldmap__front velocity-animating"
									 style="transform: translateX(-367.639px);"></div>
							</div>

							<div class="globe__inner_shadow"></div>
							<div class="globe__reflections__top"></div>
						</div>
					</div>
				</div>
			</center>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 20px !important;">
			<center>
				<h2>We are working on report. <br> Please wait and do not close the window..!!</h2>
			</center>
		</td>
	</tr>
</table>
<?php $this->load->view("export/$report", $form); ?>
<script src="<?php echo API_URL; ?>assets/loader/globe_css/jquery-1.11.1.min.js.download"></script>
<script src="<?php echo API_URL; ?>assets/loader/globe_css/velocity.min.js.download"></script>
<script src="<?php echo API_URL; ?>assets/loader/globe_css/globe_animation.js.download"></script>
<script>
	$(document).ready(function () {
		setTimeout(function () {
			$.ajax({
				url: "<?php //echo API_URL . "admin/export/$report" ?>",
				data: $("form").serialize(),
				method: "post",
				success: function (report_url) {
					var a = document.createElement('a')
					a.href = report_url
					a.download = report_url.split('/').pop()
					document.body.appendChild(a)
					a.click()
					document.body.removeChild(a);
					setTimeout(function () {
						window.close();
					}, 500)
				}
			});
		}, 500);
	});

</script>


</body>
</html>
