<!DOCTYPE html>
<html data-ng-app="app" data-ng-controller="SupportCtrl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>{{ config('app.name') }}</title>

	<link rel="stylesheet" type="text/css" href="/css/libs/bootstrap.min.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/libs/font-awesome.min.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/main.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/app.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/{{ config('app.name') }}.css" media="screen" />
</head>

<body id="app" class="app">
	<div class="page-signin">
		<div class="signin-header">
			<div class="container">
				<div class="wrap-logo text-center">
					<a href="/">
						<img src="/img/logo.jpg" alt="" />
					</a>
				</div>
			</div>
		</div>

		<div class="signin-body">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="form-container">
							<form name="form" method="post" novalidate="novalidate">
								<fieldset>
									<div class="form-group">
										<div class="support input-group">
											<span class="input-group-addon">
												<span class="fa fa-user"></span>
											</span>
											<input type="text" name="name" class="form-control input-lg" ng-model="support.name" required="required" placeholder="{{ __('Your Name') }}" ng-model="supportArr.name"/>
										</div>
									</div>

									<div class="form-group">
										<div class="support input-group">
											<span class="input-group-addon">
												<span class="fa fa-envelope-o"></span>
											</span>
											<input type="email" name="email" class="form-control input-lg" ng-model="support.email" required="required" 
											placeholder="{{ __('Your Email') }}"/>
										</div>
									</div>
									<div class="textarea">
										<div class="form-group">
											<span class="input-group-addon pull-left">
												<span class=" fa fa-comments"></span>
											</span>
											<textarea name="message" class="form-control area-field input-lg" ng-model="support.message" required="required" placeholder="{{ __('Message') }}"></textarea>
										</div>
									</div>
									<div class="btn-log-in">
										<div class="form-group">
											<button type="submit" class="btn btn-primary btn-lg btn-block text-center" ng-class="{'btn-load': request_sent}" ng-click="send()">
											<span class="loading-text">{{ __('Send Message') }}</span>
											<i class="fa fa-spinner fa-pulse fa-3x fa-fw loading-icon"></i>
											</button>
										</div>
									</div>
									<section class="additional-info text-center">
										<a href="/">
											<i class="fa fa-home" aria-hidden="true"></i>
											{{ __('Back To Home Page') }}
										</a>
									</section>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="/js/libs/angular.js"></script>
	<script src="/js/libs/jquery.js"></script>
	<script src="/js/libs/ui.js"></script>
	<script src="/js/support.js"></script>
	<script src="/js/factories.js"></script>
</body>
</html>