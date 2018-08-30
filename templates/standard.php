<?php

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Browser\Url;

$config = Config::load();
?>
<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
		  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="stylesheet" href="/github-gist.css">

	<style>
		pre {
			background-color: #f6f8fa;
			padding: 16px;
		}
	</style>

	<title><?= $config->getName() ?></title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="<?= Url::home() ?>"><?= $config->getName() ?></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
			aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/">Packages</a>
			</li>
            <li class="nav-item">
                <a class="nav-link" href="/?page=location">Location</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" target="_blank" href="<?= Url::packagist() ?>">Packagist</a>
            </li>
		</ul>
	</div>

	<span id="build-status" class="badge badge-success" style="padding: 5px 5px 5px 5px;"></span>
	&nbsp;&nbsp;
	<button id="build-order" class="btn btn-primary btn-sm" type="button">Build</button>
</nav>

<div style="padding: 10px 10px 10px 10px;">{content}</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
		integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
		crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
		integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
		crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {

        let orderUrl = '<?= Url::build(['page' => 'services', 'service' => 'order']) ?>';
        let getOrderStatusUrl = '<?= Url::build(['page' => 'services', 'service' => 'getOrderStatus']) ?>';

        $('#build-order').click(function () {
            $.ajax({
                url: orderUrl,
                type: "GET",
                dataType: "json",
                success: function (data) {
                }
            })
        });

        function doPoll() {
            $.ajax({
                url: getOrderStatusUrl,
                type: "GET",
                dataType: "json",
                success: function (data) {

                    // Get data properties.
                    let orderCount = 0;
                    let isRunning = false;
                    let runningTime = '';
                    if (data !== undefined) {
                        orderCount = data.count;
                        isRunning = data.isRunning;
                        runningTime = data.runningTime;
                    }

                    let buildText = '(' + orderCount + ') Building idle';
                    let badgeClass = 'badge-success';
                    if (isRunning) {
                        buildText = '(' + orderCount + ') Build started at ' + runningTime + '.';
                        badgeClass = 'badge-warning';
                    } else {
                        if (orderCount > 0) {
                            buildText = '(' + orderCount + ') Build starting soon.';
                            badgeClass = 'badge-info';
                        }
                    }

                    // Update badge.
                    let $buildStatus = $('#build-status');
                    $buildStatus.removeClass('badge-success');
                    $buildStatus.removeClass('badge-info');
                    $buildStatus.removeClass('badge-warning');
                    $buildStatus.addClass(badgeClass);
                    $buildStatus.text(buildText);

                    setTimeout(doPoll, 1000);
                }
            });

        }

        doPoll();
    });
</script>


</body>
</html>