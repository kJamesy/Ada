<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermissionOnUser('update', resource)">
                <form v-on:submit.prevent='updateResource' v-if="! fetchingData ">

                    <h3 class="mb-3">
                        <i class="icon ion-ribbon-b"></i> {{ resource.first_name }} {{ resource.last_name }}
                        <span v-show="resource.is_super_admin" class="text-warning" title="Super Admin" data-toggle="tooltip"> <i class="icon ion-android-star"></i> </span>
                    </h3>
                    <div class="text-muted mb-5">
                        <i class="icon ion-alert"></i> Please note, you can only assign permissions that you have
                    </div>

                    <div class="row mb-4" v-if="appUser.is_super_admin">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_super_admin" v-model="resource.is_super_admin">
                                <label class="custom-control-label" for="is_super_admin">
                                    Super Admin
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="newPoliciesLength">
                        <div class="col-sm-4 mb-4" v-for="(policy, key, index) in newPolicies">
                            <h5>{{ appStartCase(appReplaceUnderscores(key)) }}</h5>
                            <div class="custom-control custom-checkbox" v-for="(action, key2) in policy">
                                <input type="checkbox" class="custom-control-input" v-bind:id="key2 + '_' + key" v-model="action.resource"
                                       v-bind:disabled="disableCheckbox(action.user)" v-on:change="checkboxChanged()">
                                <label class="custom-control-label" v-bind:for="key2 + '_' + key">{{ appCapitalise(key2) }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info btn-lg">Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <div v-if="! appUserHasPermission('update')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.appInitialiseTooltip();
                this.getResource();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', first_name: '', last_name: '', username: '', email: '', password: '', password_confirmation: '', active: null, is_super_admin: null},
                newPolicies: {},
                cachedNewPolicies: {}
            }
        },
        computed: {
            newPoliciesLength() {
                return _.size(this.newPolicies);
            }
        },
        methods: {
            getResource() {
                let vm = this;
                let progress = vm.$Progress;
                progress.start();

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/edit', { params : {permissions: true} }).then(function(response) {
                    if ( response.data && response.data.resource && response.data.policies ) {
                        _.forEach(vm.resource, function (val, idx) {
                            if ( response.data.resource[idx] )
                                vm.$set(vm.resource, idx, response.data.resource[idx]);
                        });

                        vm.$set(vm, 'newPolicies', response.data.policies);
                        vm.cachedNewPolicies = _.cloneDeep(vm.newPolicies);
                        vm.checkboxChanged(); //Initialise to make sure user can read if they have other permissions. Clever stuff

                        progress.finish();
                    }
                    else {
                        vm.appGeneralErrorAlert();
                        progress.fail();
                    }

                    vm.fetchingData = false;
                }, function(error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlertConfirmed(error.data.error);
                    else if ( error.status && error.status === 404 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();
                    progress.fail();
                    vm.fetchingData = false;
                });

            },
            updateResource() {
                let vm = this;
                let progress = vm.$Progress;
                progress.start();
                vm.fetchingData = true;

                vm.resource.newPolicies = vm.newPolicies;

                vm.$http.put(vm.appResourceUrl + '/' + vm.id, vm.resource, { params : {permissions: true} }).then(function(response) {
                    vm.appCustomSuccessAlert('Permissions updated');
                    progress.finish();
                    vm.fetchingData = false;
                }, function(error) {
                    if ( error.status && error.status === 404 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlertConfirmed(error.data.error);
                    else
                        vm.appGeneralErrorAlert();

                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            disableCheckbox(currentUserHasPermission) {
                return ( this.resource.is_super_admin || ! currentUserHasPermission )
            },
            checkboxChanged() {
                let vm = this;

                if ( _.size(vm.newPolicies) ) {
                    _.forEach(vm.newPolicies, function(policy, key) {
                        _.forEach(policy, function(permission) {
                            if ( permission.resource )
                                vm.newPolicies[key]['read']['resource'] = true;
                        });
                    });
                }
            }
        },
        watch: {
            'resource.is_super_admin'(newVal, oldVal) {
                let vm = this;

                if ( newVal ) {
                    _.forEach(vm.newPolicies, function(policy) {
                        _.forEach(policy, function(permission) {
                            permission.resource = true;
                        });
                    });
                }
                else if ( oldVal && ! newVal )
                    vm.$set(vm, 'newPolicies', _.cloneDeep(vm.cachedNewPolicies));
            },
        }
    }
</script>
