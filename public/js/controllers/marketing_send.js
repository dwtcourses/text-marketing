
(function () {
	'use strict';

	angular.module('app').controller('MarketingSendCtrl', ['$rootScope', '$scope', '$uibModal', 'request', 'langs', '$location', 'logger', 'getShortUrl', MarketingSendCtrl]);

	function MarketingSendCtrl($rootScope, $scope, $uibModal, request, langs, $location, logger, getShortUrl) {
		$scope.step = 1;
		$scope.open = false;
		$scope.longLink =  {
			'input': '',
			'show': false
		};

		$scope.time = new Date();
		$scope.minTime = $scope.time.setHours(9);
		$scope.maxTime = $scope.time.setHours(20);
		$scope.maxTime = $scope.time.setMinutes(59);
		
		$scope.list = [];
        $scope.listsList = [];
        $scope.selectedList = {};
        $scope.originList = {};
        $scope.originClient = {};

        $scope.max_text_len = 140 - ' Txt STOP to OptOut'.length;
        $scope.max_lms_text_len = 500 - ' Txt STOP to OptOut'.length;

		$scope.message = {
			'text': '',
			'schedule': '0',
			'switch': '1',
			'date': new Date(),
			'time': new Date(),
			'day': '2',
			'finish': new Date(),
			'lists_id': []
		};

		$scope.dateOptions = {
			minDate: new Date()
		};

		$scope.finishOptions = {
			minDate: $scope.message.date,
			dateFormat: 'yyyy-MMMM-dd',
			dateDisabled: disabled
		};

		function disabled(data) {
			var date = data.date;
			switch ($scope.message.switch) {
				case '3': return date.getDay() !== $scope.message.date.getDay();
				case '4': return date.getDate() !== $scope.message.date.getDate();
				case '5': return (date.getDate() + 1) %  $scope.message.day;
				default: return false;
			}
		};

		$scope.saveMessage = function() {
			var error = 1;
			if ( ! $scope.message.text || $scope.message.text == '') {
				logger.logError('Message text is required.');
				error = 0;
			}

			if ($scope.charsCount($scope.message.text) > $scope.max_lms_text_len) {
				logger.logError('The body of your SMS message must not exceed ' + $scope.max_lms_text_len + ' characters.');
				error = 0;
			}

			if (error) {
				console.log($scope.message.lists_id);
				for (var k in $scope.listsList) {
					console.log($scope.message.lists_id.indexOf($scope.listsList[k].id));
					if ($scope.message.lists_id.indexOf($scope.listsList[k].id) >= 0) {
						
						$scope.listsList[k].choosed = true;
					}
				}
				if ($scope.charsCount($scope.message.text) > $scope.max_text_len) {
					logger.log('will be cost 3 messages.');
				}
				$scope.step++;
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
        	$scope.step = 3;
        };

        $scope.confirm = function() {
        	for (var k in $scope.listsList) {
        		if ($scope.listsList[k].choosed && $scope.listsList[k].clients.length) {
        			$scope.message.lists_id.push($scope.listsList[k].id);
        		}
        	}
        	request.send('/messages/' + ( ! $scope.message.id ? 'create' : $scope.message.id), $scope.message, function (data) {
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

		$scope.countTimes = function() {
			var from = $scope.message.date.getTime();
			var to = $scope.message.finish.getTime();
			switch ($scope.message.switch) {
				case '2': return (to - from) / 60 / 60 / 24 / 1000 + 1;
				case '3': return (to - from) / 60 / 60 / 24 / 1000 / 7 + 1;
				case '4': return ($scope.message.finish.getMonth() + (($scope.message.finish.getFullYear() - $scope.message.date.getFullYear()) * 12) - $scope.message.date.getMonth()) + 1;
				case '5': return Math.floor(((to - from) / 60 / 60 / 24 / 1000 + 1) / $scope.message.day + 1);
			}
		};

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
                    $scope.listsList[index].clients.push($scope.list[k]);
                    $scope.list[k].selected = false;
                }
            }
            request.send('/clients/addToList/' + $scope.listsList[index].id, $scope.listsList[index].clients, function (data) {

            });
        };

        $scope.cancelClient = function(client, index) {
            if ( ! $scope.originClient.id) {
                $scope.listsList[index].clients.shift();
            }
            $scope.activeEditable = client.editable = false;
        };

        $scope.editClient = function(client) {
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

            if ( ! client.email) {
                logger.logError('Email is required');
                error = 0;
            }
            
            if (error) {
                $scope.listsList[index].clients[clientIndex].lists_id = $scope.listsList[index].id;
                $scope.activeEditable = $scope.listsList[index].clients[clientIndex].editable = false;

                request.send('/clients/' + ( ! $scope.listsList[index].clients[clientIndex].id ? 'save' : $scope.listsList[index].clients[clientIndex].id), $scope.listsList[index].clients[clientIndex], function (data) {
                    $scope.listsList[index].clients[clientIndex].id = data;
                    $scope.listsList[index].clients[clientIndex].view_phone = $scope.listsList[index].clients[clientIndex].phone;
                }, ( ! $scope.listsList[index].clients[clientIndex].id ? 'put' : 'post'));
            }
        };

        $scope.removeClient = function(client, index, clientIndex) {
        	$scope.listsList[index].clients.splice(clientIndex, 1);
        	request.send('/clients/removeFromList/' + $scope.listsList[index].id, $scope.listsList[index].clients, function (data) {

            });
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

        $scope.charsCount = function(text) {
            $scope.check_firstname = false;
            if (text) {
                var firstname = 0;
                var lastname = 0;
                if (text.indexOf('[$FirstName]') + 1) {
                	firstname = $scope.user.firstname.length - '[$FirstName]'.length;
                }
                if (text.indexOf('[$LastName]') + 1) {
                	firstname = $scope.user.lastname.length - '[$LastName]'.length;
                }

                return ($scope.user.company_name ? $scope.user.company_name.length : 0) + ': '.length + text.length + firstname + lastname;
            }
            return 0;
        };

		$scope.openImport = function() {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: 'ImportFiles.html',
				controller: 'ImportFileCtrl'
			});

			modalInstance.result.then(function(response) {
			}, function () {

			});
		};
	};
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ImportFilesCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'langs', 'logger', ImportFilesCtrl]);

	function ImportFilesCtrl($rootScope, $scope, $uibModalInstance, request, langs, logger) {

		$scope.csv = {'phones_firstname': 1,
		'phones_lastname': 2,
		'phones_number': 3,
		'phones_email': "",
		'starts_from': "0",
		'upload_csv': false};

		$scope.upload_progress = false;
		$scope.upload_percent = 100;

		$scope.save = function() {
			var error = 1;
			if (! $scope.csv.upload_csv)
			{
				logger.logError('Please choose file');
				return;
			}

			if (error)
			{
				request.send('/phones/csv/', $scope.csv, function(data) {
					if (data)
					{
						$uibModalInstance.close(data);
					}
				});
			}
		};

		$scope.cancel = function() {
			$uibModalInstance.dismiss('cancel');
		};

		$scope.upload_csv = function(event) {
			var files = event.target.files;
			if (files.length)
			{
				var xhr = new XMLHttpRequest();
				xhr.open('POST', '/api/pub/upload/', true);
				xhr.onload = function(event)
				{
					if (this.status == 200)
					{
						var response = JSON.parse(this.response);
						if (response.data) {
							var part = response.data.split('/data/');
							var ext = part[1].split('.');
							$timeout(function() { $scope.csv.upload_csv = '/data/' + part[1]; });
						}
						$scope.upload_progress = false;
					}
				};

				xhr.upload.onprogress = function(event)
				{
					if (event.lengthComputable)
					{
						$scope.upload_progress = true;
						$scope.upload_percent = Math.round(event.loaded * 100 / event.total);
					}
				};

				var fd = new FormData();
				fd.append("file", files[0]);

				xhr.send(fd);
				$scope.upload_progress = true;
			}
		};

		$scope.getFileName = function(path) {
			if (!path || path == "") return '';
			return path.replace(/^.*[\\\/]/, '')
		};

	};
})();

;