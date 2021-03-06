<!DOCTYPE html>
<html data-ng-app="app" data-ng-controller="SignInCtrl">
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
                                <form class="form-horizontal" name="form" method="post" novalidate="novalidate">
                                    <fieldset>
                                        <div class="form-group">
                                            <div class="input-group input-group-first">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-envelope-o"></span>
                                                </span>
                                                <input type="email" name="email" class="form-control input-lg" placeholder="{{ __('Email') }}" ng-model="auth.email" required="required" />
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-lock"></span>
                                                </span>
                                                <input type="password" name="password" class="form-control input-lg" placeholder="{{ __('Password') }}" ng-model="auth.password" required="required" />
                                            </div>
                                        </div>

                                        <div class="btn-log-in">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-lg btn-block text-center" ng-class="{'btn-load': request_sent}" ng-click="signin()">
                                                    <span class="loading-text">{{ __('Log in') }}</span>
                                                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw loading-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                
                                <section class="additional-info">
                                    <a href="/recovery">
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        {{ __('Forgot your password?') }}
                                    </a>

                                    <a href="/support" target="_self" class="pull-right">
                                        <i class="fa fa-life-ring" aria-hidden="true"></i>
                                        {{ __('Support') }}
                                    </a>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>         

        <script src="/js/libs/angular.js"></script>
        <script src="/js/libs/jquery.js"></script>
        <script src="/js/libs/ui.js"></script>
        <script src="/js/signin.js"></script>
        <script src="/js/factories.js"></script>
    </body>
</html>