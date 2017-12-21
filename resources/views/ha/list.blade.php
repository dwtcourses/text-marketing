<div class="page page-table" data-ng-controller="LinksCtrl" data-ng-init="init()">
	<h2>
		<div class="pull-right">
			<button type="button" class="btn btn-primary" ng-click="create()"><i class="fa fa-plus-circle"></i><span class="hidden-xs"> {{ __('Create New Link') }}</span></button>
		</div>
		{{ __('HomeAdvisor Links') }}
	</h2>

	<div class="content-loader" ng-show=" ! request_finish">
		<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
	</div>

	<div ng-show="request_finish">
		<div uib-alert class="alert-info" ng-show=" ! list.length">
			{{ __("You haven't any links yet.") }}
			<a href="javascript:;" ng-click="create()">{{ __("Create New Link") }}</a> {{ __("now") }}
		</div>

		<section class="panel panel-default table-dynamic table-responsive " ng-show="list.length">
			<table class="table table-bordered table-striped table-middle">
				<thead>
					<tr>
						<th>
							<div class="th">
								{{ __('Required Team') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('First Name') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Last Name') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Phone') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Link for HA') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Success String') }}
							</div>
						</th>

						<th class="th-button">
						</th>

						<th class="th-button">
						</th>

						<th class="th-button">
						</th>
					</tr>
				</thead>

				<tbody>
					<tr ng-repeat="link in list">
						<td>
							@{{ link.teams.name }}
						</td>

						<td>
							@{{ link.firstname }}
						</td>
						
						<td>
							@{{ link.lastname }}
						</td>
						
						<td>
							@{{ link.phone }}
						</td>

						<td>
							@{{ link.url }}
						</td>

						<td>
							@{{ link.success }}
						</td>

						<td class="td-button text-center">
							<a href="javascript:;" class="a-icon text-success" ng-click="create(link.id)">
								<i class="fa fa-pencil-square-o"></i>
							</a>
						</td>

						<td class="td-button text-center">
							<a href="javascript:;" class="a-icon text-danger" ng-click="remove(link.id)">
								<i class="fa fa-trash"></i>
							</a>
						</td>

						<td class="td-button text-center">
							<button ng-show="! link.user_signup.links_code" class="btn btn-default" uib-tooltip="Not signup yet">{{ __("Send") }}</button>
							<button ng-show="link.user_signup.links_code" class="btn btn-primary" ng-click="send_modal(link.user_signup.links_code)">{{ __("Send") }}</button>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	</div>
</div>

<script type="text/ng-template" id="ModalLinksCreate.html">
		<form name="form" method="post" novalidate="novalidate">
			<div class="modal-header">
				<h4 class="modal-title" ng-show=" ! link.id">{{ __("Create New Link") }}</h4>
				<h4 class="modal-title" ng-show="link.id">{{ __("Edit Link") }}</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label>{{ __("Required Team") }}</label>
							<select class="form-control" name="teams_id" ng-model="link.teams_id" required="required" ng-options="team.id as team.name for team in teams">
							</select>
						</div>

						<div class="form-group">
							<label>{{ __("First Name") }}</label>
							<input type="text" name="firstname" class="form-control" ng-model="link.firstname" required="required" />
						</div>

						<div class="form-group">
							<label>{{ __("Last Name") }}</label>
							<input type="text" name="lastname" class="form-control" ng-model="link.lastname" required="required" />
						</div>

						<div class="form-group">
							<label>{{ __("Phone") }}</label>
							<input type="text" name="phone" class="form-control" ng-model="link.phone" required="required" />
						</div>	
					</div>

					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label>{{ __("Code") }}</label>
							<input type="text" class="form-control" ng-model="link.links_code" disabled="disabled" />
						</div>

						<div class="form-group">
							<label>{{ __("Link for HA") }}</label>
							<input type="text" class="form-control" ng-model="link.link_for_ha" disabled="disabled" />
						</div>

						<div class="form-group">
							<label>{{ __("Success String") }}</label>
							<input type="text" class="form-control" ng-model="link.success_string" disabled="disabled" />
						</div>	
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" ng-click="save()">@{{ button }}</button>
				<button type="button" class="btn btn-default" ng-click="cancel()">{{ __('Close') }}</button>
			</div>
		</form>
</script> 