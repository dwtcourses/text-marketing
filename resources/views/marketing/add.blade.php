<div class="page page-table" data-ng-controller="MarketingSendCtrl" ng-init="init()">
	<h2>
		New Message		
		<i class="fa fa-question-circle-o help-icon" uib-tooltip="Here is where you type the texts you want to send out to your lists. You can insert the names of your contacts, a link, picture, or video. You can send the texts immediately or schedule them for the future. The texts are sent individually, not as a group text." tooltip-placement="right" aria-hidden="true">
		</i>
	</h2>

	<div class="row">
		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-primary" ng-class="{'btn-primary': step == 1, 'btn-default': step != 1}" ng-click="step = 1">
				1. Write a Message
			</a>
		</div>

		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-disabled" ng-class="{'btn-primary': step == 2, 'btn-disabled': step == 1, 'btn-default': step == 3}" ng-click="step = (step > 2) ? 2 : step">
				2. Select Contact List
			</a>
		</div>

		<div class="col-sm-4 hidden-xs form-group">
			<a href="javascript:;" class="btn btn-block align-left btn-disabled" ng-class="{'btn-primary': step == 3, 'btn-disabled': step != 3}">
				3. Confirm
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<section class="panel panel-default table-dynamic" ng-show="step == 1">
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6 col-xs-12 form-group">
							<div class="chars-area">
								<label>Message Text</label>
								<textarea id="messagesText" class="form-control" placeholder="Message Text" ng-model="message.messagesText">
								</textarea>
								<span>
									<span>@{{ message.messagesText.length }}</span> /
									<span>130</span>
									<span class="fa fa-question-circle-o" uib-tooltip="You can go over 130 characters and have 460. This will cost 3 text credits." tooltip-placement="left"></span>
								</span>
							</div>

							<div class="btn-group btn-group-justified move-top-pixel">
								<div class="btn-group">
									<button ng-click="show_message_text_url = true" type="button" class="btn btn-sm btn-default">
										<i class="fa fa-link"></i> 	Short Link
									</button>
								</div>

								<div class="btn-group">
									<button type="button" ng-click="insertMask('messagesText', '[$FirstName]')" class="btn btn-sm btn-default" uib-tooltip="Be aware adding this takes 30 characters off your limit">
										<i class="fa fa-user"></i> First Name
									</button>
								</div>

								<div class="btn-group">
									<button type="button" ng-click="insertMask('messagesText', '[$LastName]')" class="btn btn-sm btn-default" uib-tooltip="Be aware adding this takes 30 characters off your limit">
										<i class="fa fa-user-o"></i> Last Name
									</button>
								</div>
							</div>

							<div ng-show="show_message_text_url" class="input-group short-url-box">
								<input class="form-control" type="text" placeholder="Add your link here" ng-model="short_link_message_text">
								<div class="input-group-btn">
									<button ng-click="insertShortLink('messagesText', short_link_message_text)" type="button" class="btn btn-sm btn-primary"><i class="fa fa-refresh"></i>
									</button>
									<button ng-click="show_message_text_url = false" type="button" class="btn btn-sm btn-default"><i class="fa fa-times"></i></button>
								</div>
							</div>

							<div class="vertical-magin-container">
								<div class="form-group">
									<span class="upload-button-box">
										<button type="button" class="btn btn-sm btn-default"><i class="fa fa-picture-o"></i> Choose File</button>
										<input custom-on-change="upload" type="file" accept="image/jpeg,image/png,image/gif,image/bmp,video/avi,video/mp4,video/quicktime,video/x-ms-wmv">
									</span>

									<span class="upload-tooltip" uib-tooltip="Image size limit is 500 KB; supported image file types include .JPG, .PNG, .GIF (non-animated), .BMP Video size limit is 3 MB; supported video file types include .AVI, .MP4, .WMV, and .MOV"><i class="fa fa-question-circle"></i> Upload details</span>
								</div>

								<div ng-show="false" class="upload-image-container">
									<i ng-show="false" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
									<div class="upload-name-box">
										<div class="upload-file-name"></div>
										<div class="progress upload-progress">
											<div ng-class="{'active progress-bar-warning': upload_percent != 100, 'progress-bar-success': upload_percent == 100}" class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
												<span class="sr-only">100% Complete</span>
											</div>
										</div>
									</div>
									<a href="javascript:;" class="text-danger upload-detele" ng-click="message.messages_file = ''"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12 form-group">
							<div ng-show="message.messagesFollowupEnable == '0'" class="disable-follow-up"></div>
							<div class="chars-area" ng-class="{'danger': chars_count(message.messagesFollowupText) > user.max_chars}">
								<div>
									<label class="ui-switch ui-switch-success ui-switch-sm pull-right follow-up-check">
										<input id="enable_followup" type="checkbox" ng-model="message.messagesFollowupEnable" ng-true-value="'1'" ng-false-value="'0'">
										<i></i>
									</label>
									<label for="enable_followup">Use Follow Up Text</label>
								</div>

								<textarea id="messagesFollowupText" class="form-control" placeholder="Follow Up Text" ng-model="message.messagesFollowupText"></textarea>
								<span>
									<span ng-bind="message.messagesFollowupText.length">0</span> / <span>130</span>
								</span>
							</div>
							<div ng-show="!show_followup_text_url" class="btn-group btn-group-justified move-top-pixel">
								<div class="btn-group">
									<button type="button" ng-click="show_followup_text_url = true" class="btn btn-sm btn-default"><i class="fa fa-link"></i> Short Link</button>
								</div>

								<div class="btn-group">
									<button type="button" ng-click="insertMask('messagesFollowupText', 'user.firstname')" class="btn btn-sm btn-default"><i class="fa fa-user"></i> First Name</button>
								</div>

								<div class="btn-group">
									<button type="button" ng-click="insertMask('messagesFollowupText', 'user.lastname')" class="btn btn-sm btn-default"><i class="fa fa-user-o"></i> Last Name</button>
								</div>
							</div>

							<div ng-show="show_followup_text_url" class="input-group short-url-box">
								<input class="form-control" type="text" placeholder="Add your link here" ng-model="short_link_followup_text">

								<div class="input-group-btn">
									<button ng-click="insertShortLink('messagesFollowupText', short_link_followup_text)" type="button" class="btn btn-sm btn-primary">
										<i class="fa fa-refresh"></i>
									</button>
									<button ng-click="show_followup_text_url = false" type="button" class="btn btn-sm btn-default"><i class="fa fa-times"></i></button>
								</div>
							</div>

							<div class="vertical-magin-container">
								<div class="form-group">
									<span class="upload-button-box">
										<button type="button" class="btn btn-sm btn-default"><i class="fa fa-picture-o"></i> Choose File</button>
										<input custom-on-change="upload_followup" type="file" accept="image/jpeg,image/png,image/gif,image/bmp,video/avi,video/mp4,video/quicktime,video/x-ms-wmv">
									</span>

									<span class="upload-tooltip" uib-tooltip="Image size limit is 500 KB; supported image file types include .JPG, .PNG, .GIF (non-animated), .BMP Video size limit is 3 MB; supported video file types include .AVI, .MP4, .WMV, and .MOV"><i class="fa fa-question-circle"></i> Upload details</span>
								</div>

								<div ng-show="message.messages_followup_file != '' || upload_followup_progress" class="upload-image-container">
									<i ng-show="false" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>

									<div class="upload-name-box">
										<div class="upload-file-name ng-binding"></div>

										<div class="progress upload-progress">
											<div ng-class="{'active progress-bar-warning': upload_followup_percent != 100, 'progress-bar-success': upload_followup_percent == 100}" class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
												<span class="sr-only">100% Complete</span>
											</div>
										</div>
									</div>

									<a href="javascript:;" class="text-danger upload-detele" ng-click="message.messages_followup_file = ''"><i class="fa fa-trash"></i></a>
								</div>
							</div>

							<div class="vertical-magin-container">
								<label class="ui-radio"><input name="messages_followup_setting" ng-model="message.messages_followup_setting" type="radio" value="10"><span>After 10m</span></label>
								<label class="ui-radio"><input name="messages_followup_setting" ng-model="message.messages_followup_setting" type="radio" value="20" checked="" ><span>After 20m</span></label>
								<label class="ui-radio"><input name="messages_followup_setting" ng-model="message.messages_followup_setting" type="radio" value="30" ><span>After 30m</span></label>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="vertical-magin-container">
								<div class="row">
									<div class="col-sm-6">
										<label class="ui-radio"><input name="messagesSchedule" type="radio" ng-model="message.messagesSchedule" value="0" >
											<span>Send Now</span>
										</label>
										<label class="ui-radio"><input name="messagesSchedule" type="radio" ng-model="message.messagesSchedule" value="1" >
											<span>Schedule</span>
										</label>
									</div>
								</div>
							</div>

							<div ng-show="message.messagesSchedule == '1'">
								<div class="calendar-container">
									<div uib-datepicker="" ng-model="message.sendDate" class="well well-sm" datepicker-options="date_options" role="application"  min-date="minDate">
									<div ng-switch="datepickerMode">
										<div uib-daypicker="" ng-switch-when="day" tabindex="0" class="uib-daypicker"></div>
									</div>
								</div>
							</div>

							<div ng-show="message.sendDate" class="vertical-magin-container">
								<div class="interval-container">
									<label>Send</label><br>
									<label class="ui-radio"><input name="messagesSwitch" ng-model="message.messagesSwitch" type="radio" value="1" >
										<span>on @{{ message.sendDate }}</span></label><br>
									<label class="ui-radio"><input name="messagesSwitch" ng-model="message.messagesSwitch" type="radio" value="2" >
										<span>every Day</span></label><br>
									<label class="ui-radio"><input name="messagesSwitch" ng-model="message.messagesSwitch" type="radio" value="3" >
										<span>every Thursday</span></label><br>
									<label class="ui-radio"><input name="messagesSwitch" ng-model="message.messagesSwitch" type="radio" value="4" >
										<span>every 1st</span></label><br>
									<label class="ui-radio"><input name="messagesSwitch" ng-model="message.messagesSwitch" type="radio" value="5" >
										<span>every</span>
										<select ng-model="x_day" >
											<option value="2" selected="selected">2nd</option>
											<option value="3">3rd</option>
											<option value="4">4th</option>
											<option value="5">5th</option>
											<option value="6">6th</option>
										</select>
										<span class="x-day">day</span>
									</label>
									<div class="time-container">
										<span>at</span>
										<div uib-timepicker="" ng-model="message.send_time" hour-step="1" minute-step="1" show-meridian="true">
											<table class="uib-timepicker">
												<tbody>
													<tr class="text-center" ng-show="::showSpinners">
														<td class="uib-increment hours"><a ng-click="incrementHours()" ng-class="{disabled: noIncrementHours()}" class="btn btn-link" ng-disabled="noIncrementHours()" tabindex="-1"><span class="glyphicon glyphicon-chevron-up"></span></a></td>
														<td>&nbsp;</td>
														<td class="uib-increment minutes"><a ng-click="incrementMinutes()" ng-class="{disabled: noIncrementMinutes()}" class="btn btn-link" ng-disabled="noIncrementMinutes()" tabindex="-1"><span class="glyphicon glyphicon-chevron-up"></span></a></td>
														<td ng-show="showSeconds">&nbsp;</td>
														<td ng-show="showSeconds" class="uib-increment seconds"><a ng-click="incrementSeconds()" ng-class="{disabled: noIncrementSeconds()}" class="btn btn-link" ng-disabled="noIncrementSeconds()" tabindex="-1"><span class="glyphicon glyphicon-chevron-up"></span></a></td>
														<td ng-show="showMeridian" class=""></td>
													</tr>
													<tr>
														<td class="form-group uib-time hours" ng-class="{'has-error': invalidHours}">
															<input type="text" placeholder="HH" ng-model="hours" ng-change="updateHours()" class="form-control text-center" ng-readonly="::readonlyInput" maxlength="2" tabindex="0" ng-disabled="noIncrementHours()" ng-blur="blur()">
														</td>
														<td class="uib-separator">:</td>
														<td class="form-group uib-time minutes" ng-class="{'has-error': invalidMinutes}">
															<input type="text" placeholder="MM" ng-model="minutes" ng-change="updateMinutes()" class="form-control text-center" ng-readonly="::readonlyInput" maxlength="2" tabindex="0" ng-disabled="noIncrementMinutes()" ng-blur="blur()">
														</td>
														<td ng-show="showSeconds" class="uib-separator">:</td>
														<td class="form-group uib-time seconds" ng-class="{'has-error': invalidSeconds}" ng-show="showSeconds">
															<input type="text" placeholder="SS" ng-model="seconds" ng-change="updateSeconds()" class="form-control text-center" ng-readonly="readonlyInput" maxlength="2" tabindex="0" ng-disabled="noIncrementSeconds()" ng-blur="blur()">
														</td>
														<td ng-show="showMeridian" class="uib-time am-pm"><button type="button" ng-class="{disabled: noToggleMeridian()}" class="btn btn-default text-center" ng-click="toggleMeridian()" ng-disabled="noToggleMeridian()" tabindex="0">PM</button></td>
													</tr>
													<tr class="text-center" ng-show="::showSpinners">
														<td class="uib-decrement hours"><a ng-click="decrementHours()" ng-class="{disabled: noDecrementHours()}" class="btn btn-link" ng-disabled="noDecrementHours()" tabindex="-1"><span class="glyphicon glyphicon-chevron-down"></span></a></td>
														<td>&nbsp;</td>
														<td class="uib-decrement minutes"><a ng-click="decrementMinutes()" ng-class="{disabled: noDecrementMinutes()}" class="btn btn-link" ng-disabled="noDecrementMinutes()" tabindex="-1"><span class="glyphicon glyphicon-chevron-down"></span></a></td>
														<td ng-show="showSeconds">&nbsp;</td>
														<td ng-show="showSeconds" class="uib-decrement seconds"><a ng-click="decrementSeconds()" ng-class="{disabled: noDecrementSeconds()}" class="btn btn-link" ng-disabled="noDecrementSeconds()" tabindex="-1"><span class="glyphicon glyphicon-chevron-down"></span></a></td>
														<td ng-show="showMeridian" class=""></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class="stop-container" ng-show="message.messagesSwitch == 2 || message.messagesSwitch == 3 || message.messagesSwitch == 4 || message.messagesSwitch == 5">
										<div class="row">
											<div class="col-sm-6">
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label>
																Stop at:
															</label>
															<div class="input-group">
																<input type="text" class="form-control" uib-datepicker-popup="" ng-model="message.messages_finish" is-open="popup.opened_finish" datepicker-options="date_finish_options" close-text="Close">
																<div uib-datepicker-popup-wrap="" ng-model="date" ng-change="dateSelection(date)" 
																template-url="/uib/template/datepickerPopup/popup.html">
															</div>
															<span class="input-group-btn">
																<button type="button" class="btn btn-default" ng-click="open_finish()"><i class="glyphicon glyphicon-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="alert alert-info">
												Message will be send from January 1st every 
												<span ng-show="message.messagesSwitch == 2">day</span>
												<span ng-show="message.messagesSwitch == 3" >Thursday</span>
												<span ng-show="message.messagesSwitch == 4" >1st date</span>
												<span ng-show="message.messagesSwitch == 5" >2nd day</span>
												<span ng-show="message.messages_finish" >and stop at  NaNth</span>
												<span ng-show="message.messages_finish" >(message will be send  times)</span>
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
				<button ng-click="step = step + 1" type="button" class="btn btn-sm btn-primary">Next</button>
			</div>
		</div>
	</section>
	<section class="panel panel-default table-dynamic" ng-show="step == 2">
		<div class="panel-body">
			<h3 class="inside-panel">
				Lists
				<div class="pull-right">
					<button type="button" class="btn btn-default" ng-click="create()"><i class="fa fa-plus-circle"></i> Create New List</button>
				</div>
			</h3>
			<div class="divider divider-dashed divider-sm pull-in"></div>
			<div class="content-loader" ng-show=" false">
				<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
			</div>
			<section class="panel panel-default table-dynamic">
					<div class="panel-body">
						<div class="content-loader" ng-show="false">
							<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
						</div>
						<div class="alert-info ng-isolate-scope alert" ng-show=" ! contactList.length && requestFinish" role="alert">
							<div >
								You don't have any list yet. <a href="javascript:;" ng-click="create()" >Create your first list</a> right now.
							</div>
						</div>

						<div ng-show="contactList.length">
							<div ng-repeat="item in contactList" ng-init="itemIndex = $index">

								<i class="choose-list fa fa-check-circle-o" ng-class="item.choosed ? 'fa-check-circle-o selected' : 'fa-circle-o'" ng-click="item.choosed = !item.choosed" style=""></i>
								<div class="item-panel" ng-class="{'active': selected == $index}">
									<div class="action-div list-actions" ng-click="choose($index)">
									</div>
									
									<div class="row-name">
										<span ng-show="!item.editable" >@{{ item.listName }}</span>
										<div class="row edit-main-container" ng-show="item.editable">
											<div class="col-sm-12 col-md-8 col-lg-9">
												<input class="form-control" type="text" placeholder="List Name" ng-model="item.listName" autofocus="autofocus" required="required">
											</div>
											<div class="col-sm-12 col-md-4 col-lg-3">
												<div class="btn-group btn-group-justified">
													<div class="btn-group">
														<button type="button" class="btn btn-default" ng-click="cancel(itemIndex)">Cancel</button>
													</div>
													<div class="btn-group">
														<button type="button" class="btn btn-primary" ng-click="save($index)">Save</button>
													</div>
												</div>
											</div>
										</div>
										<a href="javascript:;" ng-show="!item.editable" class="a-icon text-success" ng-click="edit($index)"><i class="fa fa-pencil"></i></a>
										<a href="javascript:;" ng-show="!item.editable" class="a-icon text-danger" ng-click="remove($index)"><i class="fa fa-trash"></i></a>
										<span ng-show="!item.editable" class="small-italic a-icon" style="">Click to see numbers</span>
									</div>

									<div ng-show="selected == $index">
										<button type="button" class="btn btn-default" ng-click="createPhone(itemIndex)"><i class="fa fa-plus-circle"></i> Add Number Manually</button>
										<span class="dropable-phones-outer">
											<button type="button" class="btn btn-default" ng-click="open_numbers_box();"><i class="fa fa-list-ul"></i> Choose from Saved Numbers</button>
										</span>
										<button type="button" class="btn btn-default" ng-click="openImport()"><i class="fa fa-upload"></i> Import from CSV file</button>
										<div ng-show="item.phones.length" >
											<div ng-repeat="phone in item.phones" >
												<div class="item-panel panel-child" ng-class="{'active': phone.editable}">
													<div class="row-name">
														<span ng-show="!phone.editable"  style=""><i class="phone-icon fa fa-phone"></i> 
															@{{ phone.number + ' ' + phone.firstName + ' ' + phone.lastName }}
														</span>
														<div class="row edit-child-container" ng-show="phone.editable">
															<div class="col-sm-12 col-md-8">
																<div class="row">
																	<div class="col-sm-12 col-md-6">
																		<div class="form-group search-group">
																			<i class="fa fa-phone search-icon" aria-hidden="true"></i>
																			<input class="form-control" type="text" placeholder="Phone Number" ng-model="phone.number" focus-me="phone.editable">
																		</div>
																	</div>
																	<div class="col-sm-12 col-md-6">
																		<div class="form-group search-group">
																			<i class="fa fa-birthday-cake search-icon" aria-hidden="true"></i>
																			<div class="input-group">
																				<input type="text" class="form-control" uib-datepicker-popup="yyyy-MM-dd" ng-model="phone.birthDay" is-open="opened" datepicker-append-to-body="true" close-text="Close">
																				<span class="input-group-btn">
																					<button type="button" class="btn btn-default" ng-click="opened = true"><i class="glyphicon glyphicon-calendar"></i></button>
																				</span>
																			</div>
																		</div>
																	</div>
																	<div class="col-sm-12 col-md-6">
																		<div class="form-group search-group">
																			<i class="fa fa-user search-icon" aria-hidden="true"></i>
																			<input ng-model="phone.firstName" class="form-control" type="text" placeholder="First Name">
																		</div>
																	</div>
																	<div class="col-sm-12 col-md-6">
																		<div class="form-group search-group">
																			<i class="fa fa-user-o search-icon" aria-hidden="true"></i>
																			<input class="form-control" type="text" placeholder="Last Name" ng-model="phone.lastName">
																		</div>
																	</div>
																</div>  <!-- phone-info -->
															</div>
															<div class="col-sm-12 col-md-4">
																<div class="form-group">
																	<div class="btn-group btn-group-justified">
																		<div class="btn-group">
																			<button type="button" class="btn btn-default" ng-click="cancel(itemIndex)">Cancel</button>
																		</div>
																		<div class="btn-group">
																			<button type="button" class="btn btn-primary" ng-click="savePhone(itemIndex, $index)">Save</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<a href="javascript:;" ng-show="! phone.editable" class="a-icon text-success" ng-click="phone.editable = true"><i class="fa fa-pencil"></i></a>
														<a href="javascript:;" ng-show="! phone.editable" class="a-icon text-danger" ng-click="remove_phone(phone.phones_id, phone.lists_id)"><i class="fa fa-trash"></i></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			
			<div class="pull-right next-step-button">
				<div class="btn-group">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-default" ng-click="step = step - 1">Back</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-primary" ng-click="step = step + 1">Next</button>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="panel panel-default table-dynamic" ng-show="step == 3">
		<div class="panel-body">
			<h3 class="inside-panel">
			Confirm					</h3>
			<p>
				You want to send a message with text <b >@{{ message.messagesText }}</b> 
				<span ng-show="!message.sendDate || !message.messagesSchedule" class="">right now</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 1" >on January 1st</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 2" >every Day starting from January 1st</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 3" >every Thursday starting from January 1st</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule &amp;&amp; message.messagesSwitch == 4" >every 1st starting from January 1st</span>
				<span ng-show="message.sendDate &amp;&amp; message.messagesSchedule" >at 19:29.</span> 
				<span ng-show="message.messagesFollowupEnable == '1'" >and followup with text <b >""</b> after 10m.</span>
			</p>
			<p>
				This message will be send to:
			</p>
			<ul>
				<li ng-repeat="item in list" ng-show="item.choosed &amp;&amp; item.count_numbers > 0"  style="">
					asdasd with 1 contacts
				</li><!-- end ngRepeat: item in list -->
			</ul>
			Total: 1 contacts.
			<p></p>

			<div class="pull-right next-step-button">
				<div class="btn-group">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-default" ng-click="step = step - 1">Back</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-primary" ng-click="confirm_send()">Send</button>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
</div>

</div>

