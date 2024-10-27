<?php
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'سیستم مدیریت حقوق';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-2">
		<a href="<?= Url::to(['employee-branch/index']) ?>" title="دپارتمان های آواپرداز">
			<div class="card bg-white card-radius">
				<div class="card-body">
					<div class="align-items-center my-2">
						<div class="row justify-content-md-center mb-4">
							<span class="fal fa-building fa-4x"></span>
						</div>
						<div class="row justify-content-md-center mt-2">
							<p class="text-dark h4">دپارتمان ها</p>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-md-2">
		<a href="<?= Url::to(['employee-branch/users']) ?>" title="کارمندان شعبه">
			<div class="card bg-white card-radius">
				<div class="card-body">
					<div class="align-items-center my-2">
						<div class="row justify-content-md-center mb-4">
							<span class="fal fa-users fa-4x"></span>
						</div>
						<div class="row justify-content-md-center mt-2">
							<p class="text-dark h4">کارمندان دپارتمان</p>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-md-2">
		<a href="<?= Url::to(['salary-period/index']) ?>" title="دوره حقوق">
			<div class="card bg-white card-radius">
				<div class="card-body">
					<div class="align-items-center my-2">
						<div class="row justify-content-md-center mb-4">
							<span class="fal fa-file-contract fa-4x"></span>
						</div>
						<div class="row justify-content-md-center mt-2">
							<p class="text-dark h4">دوره حقوق</p>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

</div>