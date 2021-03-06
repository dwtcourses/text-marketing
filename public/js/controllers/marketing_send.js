
(function () {
	'use strict';

	angular.module('app').controller('MarketingSendCtrl', ['$rootScope', '$scope', '$uibModal', 'request', 'langs', '$location', 'logger', 'getShortUrl', '$http', MarketingSendCtrl]);

	function MarketingSendCtrl($rootScope, $scope, $uibModal, request, langs, $location, logger, getShortUrl, $http) {
		$scope.step = 1;
		$scope.open = false;
		$scope.longLink =  {
			'input': '',
			'show': false
		};
        $scope.popup = {};

        $scope.csvClients = [];

		$scope.companyChanged = false;
		$scope.oldCompany = angular.copy($scope.user.company_name);

        $scope.file = {};

		var date = new Date();
        $scope.seanceDate = date;

        var date = new Date();
        date.setHours(9, 0);
        $scope.seanceTime = date;
        $scope.timeMin = date;

        var date = new Date();
        date.setHours(21, 0);
        $scope.timeMax = date;

        var date = new Date();
        $scope.seanceFinish = date;
		
		$scope.list = [];
        $scope.listsList = [];
        $scope.selectedList = {};
        $scope.originList = {};
        $scope.originClient = {};

		$scope.message = {
			'text': '',
			'schedule': '0',
			'switch': '1',
			'time': [],
            'finish_date': [],
			'x_day': '2',
			'lists_id': [],
            'company': $scope.user.company_name,
            'file': ''
		};

        $scope.dateOptions = {
            minDate: new Date()
        };

		$scope.companyChange = function () {
            $scope.companyChanged = false;
            if ($scope.oldCompany != $scope.user.company_name) {
                $scope.companyChanged = true;
            }
        };

        $scope.companySave = function () {
            request.send('/users/company', {'company': $scope.user.company_name}, function (data) {
                if (data) {
                    $scope.user.company_status = data.status;
                    $scope.companyChanged = false;
                    $scope.checkCompany();
                }
            }, 'put');
        };

        $scope.checkCompany = function () {
            var user = $scope.partner.id ? $scope.partner : $scope.user;
            $timeout.cancel($scope.timer);
            if ($scope.user.company_status == 'pending') {
                $scope.timer = $timeout(function () {
                    request.send('/users/status' + ($scope.partner.id ? '/' + $scope.partner.id : ''), {}, function (data) {
                        if (data) {
                            user.company_status = data.status;
                        }
                        $scope.checkCompany();
                    }, 'get');
                }, 5000);
            }
        };

		$scope.saveMessage = function() {
			var error = 1;
			if ( ! $scope.message.text || $scope.message.text == '') {
				logger.logError('Message text is required.');
				error = 0;
			}

			if (error) {
				for (var k in $scope.listsList) {
					if ($scope.message.lists_id.indexOf($scope.listsList[k].id) >= 0) {
						
						$scope.listsList[k].choosed = true;
					}
				}

                var date = new Date()

                var time = {
                    'year': $scope.seanceDate.getFullYear(),
                    'month': $scope.seanceDate.getMonth() + 1,
                    'date': $scope.seanceDate.getDate(),
                    'hours': $scope.message.schedule == '1' ? $scope.seanceTime.getHours() : date.getHours(),
                    'minutes': $scope.seanceTime.getMinutes()
                };

                $scope.message.time = time;

                request.send('/messages/textValidate', $scope.message, function (data) {
                    if (data) {
                        $scope.step++;
                    }
                });
			}
		};

		$scope.getContacts = function() {
            request.send('/clients', {}, function (data) {
                $scope.list = data;
            }, 'get');
        };

		$scope.getList = function() {
            request.send('/lists', {}, function (data) {
                $scope.listsList = data;
            }, 'get');
        };

        $scope.goToConfirm = function() {
        	if ( ! $scope.checkLists()) {
        		return;
        	}

            var post_mas = {
                'company': $scope.user.company_name,
                'text': $scope.message.text,
                'clients': $scope.getClientsInLists(),
                'time': $scope.message.time,
                'schedule': $scope.message.schedule
            };

            request.send('/messages/textValidate', post_mas, function (data) {
                if (data) {
                    $scope.step++;
                }
            });
        };

        $scope.openDate = function() {
            $scope.popup.popup_date = ! $scope.popup.popup_date;
        };

        $scope.backToMessage = function() {
            $scope.getContacts();
            $scope.step--;
        };

        $scope.getClientsInLists = function() {
            var clients = [];
            for (var k in $scope.listsList) {
                if ($scope.listsList[k].choosed && $scope.listsList[k].clients.length) {
                    for (var j in $scope.listsList[k].clients) {
                        function findClient(client) {
                            return client.id == $scope.listsList[k].clients[j].id;
                        };
                        if ( ! clients.find(findClient)) {
                           clients.push($scope.listsList[k].clients[j]); 
                       }
                    }
                }
            }
            return clients;
        };

        $scope.confirm = function() {
        	var time = {
                'year': $scope.seanceDate.getFullYear(),
                'month': $scope.seanceDate.getMonth() + 1,
                'date': $scope.seanceDate.getDate(),
                'hours': $scope.seanceTime.getHours(),
                'minutes': $scope.seanceTime.getMinutes()
            };

            var finish_time = {
                'year': $scope.seanceFinish.getFullYear(),
                'month': $scope.seanceFinish.getMonth() + 1,
                'date': $scope.seanceFinish.getDate(),
                'hours': $scope.seanceTime.getHours(),
                'minutes': $scope.seanceTime.getMinutes()
            };

            $scope.message.time = time;
            $scope.message.finish_date = finish_time;

        	for (var k in $scope.listsList) {
        		if ($scope.listsList[k].choosed && $scope.listsList[k].clients.length) {
        			$scope.message.lists_id.push($scope.listsList[k].id);
        		}
        	}
        	request.send('/messages/' + ( ! $scope.message.id ? 'create' : $scope.message.id), $scope.message, function (data) {
        		$scope.message.lists_id = [];
				$location.path('/marketing/outbox');
        	}, ( ! $scope.message.id ? 'put' : 'post'));
        }

        $scope.checkLists = function() {
        	for (var k in $scope.listsList) {
        		if ($scope.listsList[k].choosed && $scope.listsList[k].clients.length) {
        			return true;
        		}
        	}
        	logger.logError('Choose list first.');
        	return false;
        }

		/*$scope.countTimes = function() {
			var from = $scope.message.date.getTime();
			var to = $scope.message.finish.getTime();
			switch ($scope.message.switch) {
				case '2': return (to - from) / 60 / 60 / 24 / 1000 + 1;
				case '3': return (to - from) / 60 / 60 / 24 / 1000 / 7 + 1;
				case '4': return ($scope.message.finish.getMonth() + (($scope.message.finish.getFullYear() - $scope.message.date.getFullYear()) * 12) - $scope.message.date.getMonth()) + 1;
				case '5': return Math.floor(((to - from) / 60 / 60 / 24 / 1000 + 1) / $scope.message.day + 1);
			}
		};*/

		$scope.totalCount = function() {
			var count = 0;
			for (var i in $scope.listsList) {
				if ($scope.listsList[i].choosed && $scope.listsList[i].clients.length) {
					count += $scope.listsList[i].clients.length;
				}
			}
			return count;
		};
		
		$scope.getSuffix = function(day) {
			switch (day) {
				case '1': return 'st';
				case '2': return 'nd';
				case '3': return 'rd';
				default: return  'th';
			}
		};

		$scope.init = function() {
			$scope.get();
			$scope.getContacts();
			$scope.getList();
		};

		$scope.get = function() {
			var url = $location.path();
			var temp = url.split('/');
			if (temp[3]) {
				request.send('/messages/' + temp[3], {}, function (data) {
					$scope.message = data;
					$scope.message.date = new Date($scope.message.date * 1000);
					$scope.message.finish = new Date($scope.message.finish * 1000);
					$scope.message.schedule = $scope.message.schedule + '';
					$scope.message.switch = $scope.message.switch + '';
					var temp = data.lists_id.split(',');
					$scope.message.lists_id = [];
					for (var k in temp) {
						$scope.message.lists_id.push(temp[k] * 1);
					}
                    if ($scope.message.file) {
                        $scope.file.url = $location.protocol() + '://' + $location.host() + '/' + $scope.message.file;
                    }
            	}, 'get');
			}
		};

		$scope.cancel = function(index, list) {
            if (! list.id) {
                $scope.listsList.splice(index, 1);
            } else {
                $scope.listsList[index] = $scope.originList;
            }
        };

        $scope.saveList = function(index) {
            if ( ! $scope.listsList[index].name) {
                logger.logError('List name is required');
                return;
            }

            $scope.listsList[index].editable = false;
            request.send('/lists/' + ( ! $scope.listsList[index].id ? 'save' : $scope.listsList[index].id), $scope.listsList[index], function (data) {
                $scope.listsList[index].id = data;
            }, (! $scope.listsList[index].id ? 'put' : 'post'));
        };

		$scope.create = function() {
            $scope.listsList.unshift({
                'editable': true,
                'clients': []
            });
        };

        $scope.createClient = function(index) {
            if ( ! $scope.activeEditable) {
                $scope.listsList[index].clients.unshift({
                    'editable': true,
                    'phone': '',
                    'view_phone': '',
                    'email': '',
                    'firstname': '',
                    'lastname': '',
                    'source': 'Manually'
                });
                $scope.activeEditable = true;
            }
        };

		$scope.choose = function(index) {
            if ($scope.selectedList.id != $scope.listsList[index].id) {
                $scope.selectedList = $scope.listsList[index];
                return;
            }
            $scope.selectedList = {};
        };

		$scope.edit = function(index) {
            $scope.originList = angular.copy($scope.listsList[index]);
            $scope.listsList[index].editable = true;
        };

        $scope.remove = function(id, index) {
            if (confirm(langs.get('Do you realy want to remove this list?'))) {
                request.send('/lists/' + id, {}, function (data) {
                    $scope.listsList.splice(index, 1);   
                }, 'delete');
            }
        };

		$scope.saveSelectedPhones = function(index) {
            for (var k in $scope.list) {
                if ($scope.list[k].selected) {
                    var check = false;
                    for (var j in $scope.listsList[index].clients) {
                        if ($scope.listsList[index].clients[j].id == $scope.list[k].id) {
                            check = true;
                        }
                    }
                    if (! check) {
                        $scope.listsList[index].clients.push($scope.list[k]);
                    }
                    
                    $scope.list[k].selected = false;
                }
            }
            
            request.send('/clients/addToList/' + $scope.listsList[index].id, $scope.listsList[index].clients, function (data) {

            });
        };

        $scope.saveFromCsv = function(data) {
            console.log(data);
            var mas = angular.copy(data);
            console.log(mas.clients);
            /*for (var k in $scope.csv_mas.clients) {
                console.log($scope.csv_mas.clients[k]);
                /*var check = false;
                for (var j in $scope.listsList[index].clients) {
                    if ($scope.listsList[index].clients[j].id == clients[k].id) {
                        check = true;
                    }
                }
                if (! check) {
                    $scope.listsList[index].clients.push(clients[k]);
                }
            }
            
            /*request.send('/clients/addToList/' + $scope.listsList[index].id, $scope.listsList[index].clients, function (data) {

            });*/
        };

        $scope.cancelClient = function(i, index) {
            if ( ! $scope.originClient.id) {
                $scope.listsList[index].clients.shift();
            } else {
                $scope.listsList[index].clients[i] = angular.copy($scope.originClient);
                $scope.originClient = {};
            }
            $scope.activeEditable = false;
        };

        $scope.editClient = function(client) {
            $scope.activeEditable = true;
            $scope.originClient = angular.copy(client);
            client.editable = true;
        };

        $scope.saveClient = function(clientIndex, index, client) {
            var error = 1;
            if ( ! client.phone) {
                logger.logError('Phone number is required');
                error = 0;
            }

            if ( ! client.firstname) {
                logger.logError('Name is required');
                error = 0;
            }
            
            if (error) {
                $scope.listsList[index].clients[clientIndex].lists_id = $scope.listsList[index].id;
                $scope.listsList[index].clients[clientIndex].view_phone = $scope.listsList[index].clients[clientIndex].phone;

                request.send('/clients/' + ( ! $scope.listsList[index].clients[clientIndex].id ? 'createForList/' + $scope.listsList[index].id : 'updateForList/' + $scope.listsList[index].id + '/' + $scope.listsList[index].clients[clientIndex].id), $scope.listsList[index].clients[clientIndex], function (data) {
                    if (data) {
                        $scope.activeEditable = $scope.listsList[index].clients[clientIndex].editable = false;
                        $scope.listsList[index].clients[clientIndex].id = data;
                    }
                }, ( ! $scope.listsList[index].clients[clientIndex].id ? 'put' : 'post'));
            }
        };

        $scope.removeClient = function(client, index, clientIndex) {
            request.send('/clients/removeFromList/' + $scope.listsList[index].id + '/' + $scope.listsList[index].clients[clientIndex].id, {}, function (data) {
                $scope.listsList[index].clients.splice(clientIndex, 1);
            }, 'delete');
        };

        $scope.insertMask = function(textarea, mask) {
        	$scope.insertAtCaret(textarea, mask);
        };

        $scope.insertUrl = function() {
        	getShortUrl.getLink($scope.longLink.input, function(shortUrl) {
				if (shortUrl) {
					shortUrl = shortUrl.replace('http://', '');
					$scope.insertAtCaret('messageText', shortUrl);
					$scope.longLink.input = '';
					$scope.longLink.show = false;
				} else {
					logger.logError('Inccorect link');
				}
			});
        };

        $scope.insertAtCaret = function(areaId,text) {
            var txtarea = document.getElementById(areaId);
            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
                'ff' : (document.selection ? 'ie' : false ) );
            if (br == 'ie') {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            }
            else if (br == 'ff') strPos = txtarea.selectionStart;

            var front = (txtarea.value).substr(0,strPos);  
            var back = (txtarea.value).substr(strPos,txtarea.value.length);
            if (front.substr(-1) != ' ' && front.substr(-1) != '') {
                text = ' ' + text;
            }

            txtarea.value = front + text + back;

            strPos = strPos + text.length;
            if (br == 'ie') {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                range.moveStart('character', strPos);
                range.moveEnd('character', 0);
                range.select();
            }
            else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }
            txtarea.scrollTop = scrollPos;
            $scope.message.text = txtarea.value;
        };

        $scope.maxChars = function() {
        	return 500 - ' Txt STOP to OptOut'.length - ($scope.user.company_status == 'verified' ? $scope.user.company_name.length : 0);
        };

        $scope.maxOneText = function() {
        	return 140 - ' Txt STOP to OptOut'.length - ($scope.user.company_status == 'verified' ? $scope.user.company_name.length : 0);
        };

        $scope.charsCount = function(text) {
            $scope.check_firstname = false;
            if (text) {
                var firstname = 0;
                var lastname = 0;
                if (text.indexOf('[$FirstName]') + 1) {
                    var count = 0;
                    for (var k in $scope.list) {
                        if ($scope.list[k].firstname.length > count) {
                            count = $scope.list[k].firstname.length;
                        }
                    }
                	firstname = count - '[$FirstName]'.length;
                }

                if (text.indexOf('[$LastName]') + 1) {
                    var count = 0;
                	for (var k in $scope.list) {
                        if ($scope.list[k].lastname.length > count) {
                            count = $scope.list[k].lastname.length;
                        }
                    }
                    lastname = count - '[$LastName]'.length;
                }

                return text.length + firstname + lastname;
            }
            return 0;
        };

        $scope.uploadFile = function(file) {
            var size = file.size / 1024;
            if (size > 500) {
                logger.logError(langs.get('Image size limit is 500 KB'));
                return;
            }

            $scope.file.name = file.name;
            var fd = new FormData();
            fd.append('file', file);

            $http.post('/api/v1/upload/file', fd, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).then(function(response) {
                $scope.message.file = response.data.data;
                $scope.file.url = $location.protocol() + '://' + $location.host() + '/' + response.data.data;
                $scope.request = false;
            });
        };

        $scope.removeMMS = function() {
            $scope.message.file = $scope.file.url = '';
        };

		$scope.openImport = function(index) {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: 'ImportFiles.html',
				controller: 'ImportFileCtrl',
                resolve: {
                    items: function () {
                        return {'index': index};
                    }
                }
			});

			modalInstance.result.then(function(response) {
                console.log(response);
                console.log(response.clients);

                var data = angular.copy(response);
                
                //$scope.saveFromCsv(data);
			}, function () {

			});
		};
	};
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ImportFileCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'langs', 'logger', '$http', 'items', ImportFileCtrl]);

	function ImportFileCtrl($rootScope, $scope, $uibModalInstance, request, langs, logger, $http, items) {
		$scope.csv = {};
        $scope.fd = false;
        $scope.index = angular.copy(items.index);
        
		$scope.uploadCSV = function(file) {
            $scope.csv.name = file.name;
            var fd = new FormData();
            fd.append('file', file);
            $scope.fd = fd;
        };

        $scope.save = function() {
            if ( ! $scope.fd) {
                logger.logError('Please choose file');
                return;
            }
            var index = $scope.index;
            $http.post('/api/v1/upload/csv', $scope.fd, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).then(function(response) {
                var data = logger.check(response.data);
                var mas = {
                    'clients': data,
                    'index': index
                };

                $uibModalInstance.close(mas);
            });
        };

        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
	};
})();

;