<div class="page page-table page-fixed" ng-controller="GeneralMessagesCtrl" data-ng-init="init()">
	<h2>
		{{ __('Global Inbox') }}
	</h2>

	<div class="dialogs panel panel-default">
		<div class="phones-body hidden-xs">
			<div class="btn-body text-center">
				<div>
					<div class="search-group">
						<i class="fa fa-search search-icon" aria-hidden="true"></i>
						<input ng-model="search.$" class="form-control" type="text" placeholder="{{ __('Search from list...') }}" />
					</div>
				</div>
			</div>
			<div ng-repeat="(key, dialog) in dialogs | filter: search">
				<div class="divider divider-dashed"></div>
				<div class="phones" ng-click="setClient(dialog)" ng-class="{'active': dialog.id == activeClient.id}">
					<div class="row">
						<div class="col-sm-12">
							<div>
								<strong>@{{ dialog.phone }}</strong>
								<!-- <span ng-if="dialog.type == 'thankyou'" class="small-italic">{{ __('Thank You Sign Up Text') }}</span>
								<span ng-if="dialog.type == 'twodays'" class="small-italic">{{ __('2 Days After Sign Up') }}</span>
								<span ng-if="dialog.type == 'fourdays'" class="small-italic">{{ __('4 Days After Sign Up') }}</span> -->
							</div>
							<div class="phone-name">
								@{{ dialog.firstname }}
								@{{ dialog.lastname }}
								<span class="small-italic">(@{{ dialog.created_at_string }})</span>
							</div>
						</div>
						<div class="col-sm-2 text-right">
							<span class="badge badge-primary">@{{ dialog.count }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mobile-phones-body visible-xs">
			<span class="dropable-phones-outer">
				<button type="button" class="btn btn-default" ng-click="showPhonesBox = ! showPhonesBox">
					@{{ activeClient.firstname + ' ' +  activeClient.lastname }} <span class="caret pull-right"></span>
				</button>
				<div ng-show="showPhonesBox" class="dropable-phones">
					<div ng-show="true">
						<div class="search-group">
							<i class="fa fa-search search-icon" aria-hidden="true"></i>
							<input ng-model="search.$" class="form-control" type="text" placeholder="Search from list..." />
						</div>
						<div class="dropable-phones-inner">
							<div ng-repeat="dialog in dialogs | filter: search">
								<div class="divider divider-xs divider-dashed">
								</div>
								<a href="javascript:;" class="selecting-phones" ng-click="setClient(dialog)" ng-class="{'active': dialog.id == activeClient.id}">
									<strong class="ng-binding">@{{ dialog.phone}}</strong> @{{ dialog.firstname + ' ' +  dialog.lastname }}
								</a>
							</div>
						</div>
					</div>
				</div>
			</span>
		</div>

		<div class="dialogs-body">
			<div class="chat-body" scroll-bottom="messages">
				<div class="chat-wrap">
					<div ng-repeat="message in messages">
						<div class="message-row">
							<div class="message-avatar text-center">
								<i ng-show="message.my" class="fa fa-user-circle fa-2x" aria-hidden="true"></i>
								<i ng-show="! message.my" class="fa fa-commenting-o fa-2x" aria-hidden="true"></i>
							</div>
							<div class="message-body">
								@{{ message.text }}
								<div class="text-right">
									<i ng-show="message.status == 1 && message.my" class="fa fa-check text-danger" aria-hidden="true"></i>
									<i ng-show="message.status == 0 && message.my" class="fa fa-times text-danger" aria-hidden="true"></i>
									<span class="small-italic">@{{ message.created_at_string }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="send-group">
				<form name="form_chat">
					<div class="chat-text chars-area">
						<textarea name="messages_text" class="form-control area-resize" ng-model="messages_text" placeholder="{{ __("Enter your text here...") }}" required="required"></textarea>
						<span>
							<span ng-show="charsCount(messages_text) > maxOneText()">{{ __('3 messages') }} </span>
							<span ng-bind="charsCount(messages_text)">0</span> / 
							<span ng-bind="maxChars()">140</span>
						</span>
					</div>
					<div class="chat-button">
						<button type="button" class="btn btn-block send-btn" ng-class="{'btn-default': sent, 'btn-primary': ! sent}" ng-click="send();">
							<i class="fa fa-envelope-o" aria-hidden="true"></i>
							<span class="hidden-xs">
								<span ng-show="! sent">{{ __("Send") }}</span>
								<span ng-show="sent">{{ __("Sent!") }}</span>
							</span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>