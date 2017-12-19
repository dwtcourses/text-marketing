<div class="page page-table" data-ng-controller="MarketingSendCtrl" data-ng-init="getPhones()">
	<h2>
		{{ __('New Message') }}
		<i class="fa fa-question-circle-o help-icon" uib-tooltip="Here is where you type the texts you want to send out to your lists. You can insert the names of your contacts, a link, picture, or video. You can send the texts immediately or schedule them for the future. The texts are sent individually, not as a group text." tooltip-placement="right" aria-hidden="true">
		</i>
	</h2>

	<div class="row">
		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-primary" ng-class="{'btn-primary': step == 1, 'btn-default': step != 1}" ng-click="step = 1">
				{{ __('1. Write a Message') }}
			</a>
		</div>

		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-disabled" ng-class="{'btn-primary': step == 2, 'btn-disabled': step == 1, 'btn-default': step == 3}" ng-click="step = (step > 2) ? 2 : step">
				{{ __('2. Select Contact List') }}
			</a>
		</div>

		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-disabled" ng-class="{'btn-primary': step == 3, 'btn-disabled': step != 3}">
				{{ __('3. Confirm') }}
			</a>
		</div>
	</div>

	<section class="panel panel-default table-dynamic" ng-show="step == 1">
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-6 col-xs-12">
					<div class="form-group">
						<div char-set ng-model="message.text" options="TextCharSetOptions"></div>
					</div>
					<!--<div upload></div>-->
						
					<label class="ui-radio"><input name="messagesSchedule" type="radio" ng-model="message.schedule" value="0" >
						<span>{{ __('Send Now') }}</span>
					</label>
					<label class="ui-radio"><input name="messagesSchedule" type="radio" ng-model="message.schedule" value="1" >
						<span>{{ __('Schedule') }}</span>
					</label>
				</div>
			
				<div class="col-lg-6 col-xs-12">
					<div ng-show="message.schedule == '1'">
						<div class="calendar-container">
							<div uib-datepicker ng-model="message.date" datepicker-options="dateOptions" role="application">
							</div>
						</div>
						<div ng-show="message.date" class="vertical-magin-container">
							<div class="interval-container">
								<label>{{ __('Send') }}</label><br />
								<label class="ui-radio">
									<input name="messagesSwitch" ng-model="message.switch" type="radio" value="1" />
									<span>{{ __('on') }} @{{ message.date | date: 'MMMM d' }}@{{getSuffix(message.date | date: 'd')}}</span>
								</label><br />
								<label class="ui-radio">
									<input name="messagesSwitch" ng-model="message.switch" type="radio" value="2" />
								<span>{{ __('every Day') }}</span>
								</label><br />
								<label class="ui-radio">
									<input name="messagesSwitch" ng-model="message.switch" type="radio" value="3" />
									<span>{{ __('every') }} @{{ message.date | date: 'EEEE' }}</span>
								</label><br />
								<label class="ui-radio">
									<input name="messagesSwitch" ng-model="message.switch" type="radio" value="4" />
									<span>{{ __('every') }} @{{ message.date | date: 'd'  }}@{{getSuffix(message.date | date: 'd')}} </span>
								</label><br />
								<label class="ui-radio">
									<input name="messagesSwitch" ng-model="message.switch" type="radio" value="5" />
									<span>{{ __('every') }}</span>
									<select ng-model="message.day" >
										<option value="2">{{ __('2nd') }}</option>
										<option value="3">{{ __('3rd') }}</option>
										<option value="4">{{ __('4th') }}</option>
										<option value="5">{{ __('5th') }}</option>
										<option value="6">{{ __('6th') }}</option>
									</select>
									<span class="x-day">{{ __('day') }}</span>
								</label>
								<div class="time-container">
									<span>{{ __('at') }}</span>
									<div uib-timepicker ng-model="message.time" show-meridian="ismeridian"></div>
								</div>
								<div class="stop-container" ng-show="message.switch > 1">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label>
													{{ __('Stop at:') }}
												</label>
												<div class="input-group">
													<input type="text" class="form-control" uib-datepicker-popup ng-model="message.finish" datepicker-options="finishOptions" close-text="Close" popup-placement="bottom" is-open="open">
													<span class="input-group-btn">
														<button type="button" class="btn btn-default" ng-click="open = ! open"><i class="glyphicon glyphicon-calendar"></i></button>
													</span>
												</div>
											</div>
											<div class="alert alert-info">
												{{ __('Message will be send from') }} @{{ message.date | date: 'MMMM d' }} {{ __('every') }} 
												<span ng-show="message.switch == 2">day</span>
												<span ng-show="message.switch == 3" >@{{ message.date | date: 'EEEE' }}</span>
												<span ng-show="message.switch == 4" >@{{ message.date | date: 'd' }}@{{getSuffix(message.date | date: 'd')}}</span>
												<span ng-show="message.switch == 5" >@{{ message.day }}@{{getSuffix(message.day)}} {{ __('day') }}</span>
												<span ng-show="message.finish" >and stop at @{{ message.finish | date : 'MMMM d' }}@{{ getSuffix(message.finish | date : 'd') }}</span>
												<span ng-show="message.finish" >(message will be send @{{ countTimes() }} times)</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pull-right">
				<button ng-click="saveMessage()" type="button" class="btn btn-sm btn-primary">{{ __('Next') }}</button>
			</div>
		</div>
	</section>

	<section class="panel panel-default table-dynamic" ng-show="step == 2">
		<div class="panel-body">
			<h3 class="inside-panel">
				{{ __('Lists') }}						
				<div class="pull-right">
					<button type="button" class="btn btn-default" ng-click="create()"><i class="fa fa-plus-circle"></i> {{ __('Create New List') }}</button>
				</div>
			</h3>

			<div class="alert-info alert" ng-show=" ! listsList.length" role="alert">
				<div >
					{{ __("You don't have any list yet.") }}<a href="javascript:;" ng-click="create()">{{ __('Create your first list') }}</a> {{ __('right now.') }}
				</div>
			</div>

			<div ng-show="listsList.length">
				<div ng-repeat="item in listsList track by $index">
					<div class="item-panel" ng-class="{'active': selectedList.id == item.id}">
						<div class="action-div list-actions" ng-click="choose($index)">
							<div class="row-name">
								<span ng-show=" ! item.editable" >@{{ item.name }}</span>
								<div class="row edit-main-container" ng-show="item.editable">
									<div class="col-sm-12 col-md-8 col-lg-9">
										<input class="form-control" type="text" placeholder="List Name" ng-model="item.name" required="required" />
									</div>
									<div class="col-sm-12 col-md-4 col-lg-3">
										<div class="btn-group btn-group-justified">
											<div class="btn-group">
												<button type="button" class="btn btn-default" ng-click="cancel($index, item)">{{ __('Cancel') }}</button>
											</div>
											<div class="btn-group">
												<button type="button" class="btn btn-primary" ng-click="saveList($index)">{{ __('Save') }}</button>
											</div>
										</div>
									</div>
								</div>
								<a href="javascript:;" ng-show="!item.editable" class="a-icon text-success" ng-click="edit($index)">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="javascript:;" ng-show="!item.editable" class="a-icon text-danger" ng-click="remove(item.id, $index)">
									<i class="fa fa-trash"></i>
								</a>
								<span ng-show="!item.editable" class="small-italic a-icon">{{ __('Click to see numbers') }}</span>
							</div>

							
						</div>


						

						<button type="button" class="btn btn-default" ng-click="openImport()"><i class="fa fa-upload"></i> Import from CSV file</button>
						
						<div ng-show="item.clients.length" >
							<div ng-repeat="(i, client) in item.clients" >
								<div class="item-panel panel-child" ng-class="{'active': client.editable}">
									<div class="row-name">
										<span ng-show=" ! client.editable">
											<i class="phone-icon fa fa-phone"></i> 
											@{{ client.view_phone + ' ' + client.firstname + ' ' + client.lastname }}
										</span>
										<div class="row edit-child-container" ng-show="client.editable">
											<div class="col-sm-12 col-md-8">
												<div class="row">
													<div class="col-sm-12 col-md-6">
														<div class="form-group search-group">
															<i class="fa fa-phone search-icon" aria-hidden="true"></i>
															<input class="form-control" type="text" placeholder="Phone Number" ng-model="client.phone">
														</div>
													</div>
													<div class="col-sm-12 col-md-6">
														<div class="form-group search-group">
															<i class="fa fa-envelope-o search-icon" aria-hidden="true"></i>
															<input class="form-control" type="text" placeholder="Email" ng-model="client.email">
														</div>
													</div>
													<div class="col-sm-12 col-md-6">
														<div class="form-group search-group">
															<i class="fa fa-user search-icon" aria-hidden="true"></i>
															<input ng-model="client.firstname" class="form-control" type="text" placeholder="First Name">
														</div>
													</div>
													<div class="col-sm-12 col-md-6">
														<div class="form-group search-group">
															<i class="fa fa-user-o search-icon" aria-hidden="true"></i>
															<input class="form-control" type="text" placeholder="Last Name" ng-model="client.lastname">
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-12 col-md-4">
												<div class="form-group">
													<div class="btn-group btn-group-justified">
														<div class="btn-group">
															<button type="button" class="btn btn-default" ng-click="cancelClient(client, $index)">{{ __('Cancel') }}</button>
														</div>
														<div class="btn-group">
															<button type="button" class="btn btn-primary" ng-click="saveClient(i, $index, client)">{{ __('Save') }}</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<a href="javascript:;" ng-show="! client.editable" class="a-icon text-success" ng-click="editClient(client)"><i class="fa fa-pencil"></i></a>
										<a href="javascript:;" ng-show="! client.editable" class="a-icon text-danger" ng-click="remove_phone(phone.phones_id, phone.lists_id)"><i class="fa fa-trash"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div ng-show="$index < (listsList.length - 1)" class="divider divider-dashed divider-sm pull-in">
				</div>
			</div>
		</div>
	</section>

	<section class="panel panel-default table-dynamic" ng-show="step == 3">
		<div class="panel-body">
			<h3 class="inside-panel">
				Confirm
			</h3>
			<p>
				You want to send a message with text <b >@{{ message.messagesText }}</b> 
				<span ng-show="!message.sendDate || !message.messagesSchedule" class="">right now</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 1">@{{ message.sendDate | date: 'MMMM d' }}@{{getSuffix(message.sendDate | date: 'd')}}</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 2">every Day starting from @{{ message.sendDate | date: 'MMMM d' }}@{{getSuffix(message.sendDate | date: 'd')}}</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 3">every @{{ message.sendDate | date: 'EEEE' }}</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 4" >every @{{ message.sendDate | date: 'd' }}@{{getSuffix(message.sendDate | date: 'd')}} starting from @{{ message.sendDate | date: 'MMMM d' }} @{{ getSuffix(message.sendDate | date: 'd') }}</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule" >at @{{ message.sendTime | date: 'HH:mm'}}.</span>
			</p>
			<p>This message will be send to:</p>
			<ul class="total">
				<li ng-repeat="item in contactList " ng-show="item.choosed &amp;&amp; item.phones.length > 0">
					@{{ item.listName + ' with ' + item.phones.length }} contacts
				</li>
			</ul>
			Total: @{{ totalContacts }} contacts.
			<div class="pull-right next-step-button">
				<div class="btn-group">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-default" ng-click="step = step - 1">Back</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-primary" ng-click="sendFunc()">Send</button>
					</div>
				</div>
			</div>
		</div>
	</section>

</div>

<script type="text/ng-template" id="ImportFiles.html">
	<form name="form" method="post" novalidate="novalidate">
		<div class="modal-header">
			<h4 class="modal-title">Import Numbers from CSV file</h4>
		</div>

		<div class="modal-body">
			<div class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 control-label">CSV File</label>
					<div class="col-sm-9">
						<span class="upload-button-box">
							<button type="button" class="btn btn-sm btn-default"><i class="fa fa-picture-o"></i> Choose File</button>
							<input custom-on-change="upload_csv" type="file" accept=".csv">
						</span>
						<div ng-show="csv.upload_csv != '' || upload_progress" class="upload-name-box">
							<div class="upload-file-name"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="submit" class="btn btn-primary " ng-click="save()" ng-if=" ! view">Import</button>
			<button type="button" class="btn btn-default " ng-click="cancel()" ng-if=" ! view">Cancel</button>
		</div>
	</form>
</script>