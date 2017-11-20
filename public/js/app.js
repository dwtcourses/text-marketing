(function () {
    'use strict';

    angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngFileUpload']);
})();

;

(function () {
    'use strict';

    angular.module('app').config(['$routeProvider', '$locationProvider', '$compileProvider', function($routeProvider, $locationProvider, $compileProvider) {
        var routes, setRoutes;
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
        });

        $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|javascript):/);

        var routes = [
            ':folder/:file/:param',
            ':folder/:file/',
            ':file'
        ];

        setRoutes = function (route) {
            var url = '/' + route;
            var config = {
                templateUrl: function (params) {
                    if (params.folder && params.file && params.param) {
                        return '/view/' + params.folder + '/' + params.file + '/' + params.param;
                    }
                    else if (params.folder && params.file) {
                        return '/view/' + params.folder + '/' + params.file;
                    }
                    else if(params.file) {
                        return '/view/' + params.file;
                    }
                }
            };
            
            $routeProvider.when(url, config);
            return $routeProvider;
        };

        routes.forEach(function (route) {
            return setRoutes(route);
        });

        $routeProvider.when('/', {templateUrl: '/view/'});
    }]); 
})(); 

;

angular.module('app').run(['$route', '$rootScope', '$location', 'logger', function ($route, $rootScope, $location, logger) {
    var original = $location.path;
    var check = 0;
    $location.path = function (path, reload) {
        if ($rootScope.new_surveys_id)
        {
            reload = false;
            check++;
            if (check == 1)
            {
                logger.logError('Survey not saved');
            }
        }

        if (reload === false) {
            var lastRoute = $route.current;
            var un = $rootScope.$on('$locationChangeSuccess', function () {
                $route.current = lastRoute;
                un();
                check = 0;
            });
        }
        return original.apply($location, [path]);
    };
}]);

;

angular.module('app').filter('capitalize', function() {
    return function(input) {
        return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

;

(function () {
    'use strict';

    angular.module('app').controller('AppCtrl', [ '$scope', '$rootScope', '$window', '$timeout', '$location', '$uibModal', 'request', '$document', AppCtrl]);

    function AppCtrl($scope, $rootScope, $window, $timeout, $location, $uibModal, request, $document) {
        $rootScope.own_loader = true;
        $rootScope.show_aside = 0;
        $scope.user = {};
        $scope.open = {};

        $scope.signout = function () {
            request.send('/auth/signout', {}, function (data) {
                if (data) {
                    $timeout(function() {
                        $window.location.href = "/";
                    }, 2000);
                }
            });
        };

        $scope.init = function() {
            $scope.menu();
            $scope.get();
            /*request.send('/users/info/', {}, function(data) {
                if (data)
                {
                   $rootScope.user = data.data;
                   if ($rootScope.user.users_first_time == '0' && $rootScope.user.plans_id == 'text-contractorreviewer')
                   {
                        $scope.modal_first_time();
                   }
                }
            });

			request.send('/teams/info/', {}, function(data) {
                if (data.data)
                {
                   $rootScope.team = data.data;
                   $rootScope.max_lms_chars = 500 - ($rootScope.team.teams_name.length + 1) - 18;
                }
            });

            request.send('/pages/get_aside_menu/', {}, function(data) {
                $scope.pages = data.data;
                for (var k in $scope.pages)
                {
                    $scope.open[$scope.pages[k].pages_code] = $scope.checkMenuOpen($scope.pages[k]);
                }
            });

            request.send('/pub/get_constants/', {}, function(data) {
                $rootScope.constants = data.data;

                if ($rootScope.constants.project == 'ContractorReviewer' || $rootScope.constants.project == 'ContractorTexter')
                {
                    $rootScope.survey_word = '';
                }

                if ($rootScope.constants.project != 'ContractorReviewer') 
                {
                    setInterval(function() {
                        request.send('/dialogs/get_new_message/', $rootScope.user.users_id, function(data) {
                            $scope.new_inbox = data.data;
                        });
                    }, 5000);
                }
            });*/
        };

        $scope.get = function() {
           request.send('/auth/authInfo/', false, function(data) {
                $scope.user = data;
            }, 'get'); 
        };

        $scope.menu = function () {
            request.send('/pages/menu/', {}, function(data) {
                $scope.pages = data;
                for (var k in $scope.pages) {
                    $scope.open[$scope.pages[k].code] = $scope.menuOpen($scope.pages[k]);
                }
            });
        };

        $scope.menuOpen = function (page) {
            for (var k in $scope.pages) {
                if ($scope.menuActive($scope.pages[k]) && $scope.pages[k].code == page.code) {
                    return true;
                }

                for (var i in $scope.pages[k].pages) {
                    if ($scope.menuActive($scope.pages[k].pages[i]) && $scope.pages[k].pages[i].parents_code == page.code) {
                        return true;
                    }
                }
            }

            return false;
        };

        $scope.menuActive = function (page) {
            if ($scope.segment(1) == '') {
                return page.main == '1';
            } else {
                if ($scope.segment(1) == page.folder && $scope.segment(2) == page.file) {
                    return true;
                }
            }

            return false;
        };

        $scope.segment = function (number) {
            var part = $window.location.pathname.split('/');
            return part[number] ? part[number] : ''; 
        };

        $scope.changePage = function (page) {
            console.log();
            $scope.open[page.code] = 1 - $scope.open[page.code];

            if ($scope.open[page.code]) {
                for (var k in $scope.open)
                {
                    if (k != page.code) {
                        $scope.open[k] = 0;
                    }
                }
            }
        };

        $scope.loaded = '';
        $scope.$on('$viewContentLoaded', function() {
            $scope.loaded = 'loaded';
            $rootScope.show_aside = 0;
        });

        $scope.toggleAside = function() {
            $rootScope.show_aside = (1 - $rootScope.show_aside);
        };

        $scope.modal_first_time = function() {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'ModalFirstTime.html',
                controller: 'ModalFirstTimeCtrl'
            });
        };

        $scope.modal_email = function() {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'ModalEmail.html',
                controller: 'ModalEmailCtrl',
                resolve: {
                    items: function () {
                        return {'user': $scope.user};
                    }
                }
            });
        };
    };
})();

;

(function () {
    'use strict';

    angular.module('app').controller('ModalEmailCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'validate', 'logger', 'langs', 'items', ModalEmailCtrl]);

    function ModalEmailCtrl($rootScope, $scope, $uibModalInstance, request, validate, logger, langs, items) {
        $scope.user = angular.copy(items.user);
        $scope.support = {
            'name': $scope.user.firstname,
            'email': $scope.user.email
        };

        $scope.send = function() {
            var error = 1;
            error *= validate.check($scope.form.subject_email, 'Subject email');
            error *= validate.check($scope.form.text_email, 'Text email');

            if (error)
            {
                request.send('/auth/support', $scope.support, function(data) {

                    $uibModalInstance.close();
                });
            }
        };

        $scope.cancel = function() {
            $uibModalInstance.close();
        };
    };
})();

;

(function () {
    'use strict';

    angular.module('app').controller('ModalFirstTimeCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'validate', 'logger', 'langs', ModalFirstTimeCtrl]);

    function ModalFirstTimeCtrl($rootScope, $scope, $uibModalInstance, request, validate, logger, langs) {

        $scope.close = function() {
            $uibModalInstance.close();
        };
    };
})();

;