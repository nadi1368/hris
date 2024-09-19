<?php

use common\models\Settings;
use yii\helpers\Url;

/* @var $resellerOrderCount integer */
/* @var $orderCount integer */
/* @var $cancelRequestCount integer */


$css = <<< CSS
header ul.navbar-nav.mr-auto i {
font-size: large;
}
CSS;
$this->registerCss($css);

?>
<header class="topbar">
	<nav class="navbar top-navbar navbar-expand-md navbar-dark">
		<div class="navbar-header">
			<!-- This is for the sidebar toggle which is visible on mobile only -->
			<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
				<i class="fal fa-bars"></i></a>
			<a class="navbar-brand" href="<?= Url::to(['/site/index']) ?>">
				<!-- Logo icon -->
				<b class="logo-icon">
					<?= Settings::get('web_site_name') ?> <span class="hris-tag">Pulse</span>
				</b>
				<!--End Logo icon -->
			</a>
			<!-- ============================================================== -->
			<!-- End Logo -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Toggle which is visible on mobile only -->
			<!-- ============================================================== -->
			<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
			   data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
			   aria-expanded="false" aria-label="Toggle navigation"><i class="fal fa-ellipsis-h"></i></a>
		</div>
		<!-- ============================================================== -->
		<!-- End Logo -->
		<!-- ============================================================== -->
		<div class="navbar-collapse collapse" id="navbarSupportedContent">
			<!-- ============================================================== -->
			<!-- toggle and nav items -->
			<!-- ============================================================== -->
			<ul class="navbar-nav float-left mr-auto">
				<!-- ============================================================== -->
				<!-- Logo -->
				<!-- ============================================================== -->
				<li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light"
														  href="javascript:void(0)" data-sidebartype="mini-sidebar"><i
								class="fal fa-bars font-18"></i></a>
				</li>

				<li class="nav-item">
				</li>


			</ul>

			<?= $this->render('@backend/views/layouts/_navbar_profile') ?>
		</div>
	</nav>
</header>
