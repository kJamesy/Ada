<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <div class="mb-5 text-right font-italic">
                    <router-link v-bind:to="{ name: 'admin_subscribers.import'}" class="btn btn-link">
                        <i class="icon ion-ios-cloud-upload-outline"></i> Import from Excel/CSV Instead?
                    </router-link>
                </div>
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="first_name">First Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="first_name" v-model.trim="resource.first_name" v-bind:class="validationErrors.first_name ? 'is-invalid' : ''" placeholder="First Name">
                            <small class="invalid-feedback">
                                {{ validationErrors.first_name }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="last_name">Last Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="last_name" v-model.trim="resource.last_name" v-bind:class="validationErrors.last_name ? 'is-invalid' : ''" placeholder="Last Name">
                            <small class="invalid-feedback">
                                {{ validationErrors.last_name }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="email">Email</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="email" v-model.trim="resource.email" v-bind:class="validationErrors.email ? 'is-invalid' : ''" placeholder="Email">
                            <small class="invalid-feedback">
                                {{ validationErrors.email }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row checkbox">
                        <div class="col-md-8 ml-md-auto">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="active" v-model="resource.active">
                                <label class="custom-control-label" for="active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" v-if="mailing_lists.length">
                        <label class="col-md-4 form-control-label">Mailing Lists</label>
                        <div class="col-md-8">
                            <v-select :options="sortedMailingLists" label="name" placeholder="Select Mailing Lists" v-model="selected_mailing_lists" multiple></v-select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 ml-md-auto">
                            <button type="submit" class="btn btn-info btn-lg">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="! appUserHasPermission('create')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.goTime();
            });

            this.$on('successfulcreate', function () {
               this.clearDefaults();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {first_name: '', last_name: '', email: '', active: 1, mailing_lists: []},
                validationErrors: {first_name: '', last_name: '', email: ''},
                mailing_lists: [],
                selected_mailing_lists: []
            }
        },
        computed: {
            sortedMailingLists() {
                return _.sortBy(this.mailing_lists, ['name']);
            },
            flattenedMLists() {
                return _.flatMapDeep(this.selected_mailing_lists, function(mList) {
                    return mList.id;
                });
            }
        },
        methods: {
            goTime() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();
                vm.appClearValidationErrors();

                vm.$http.get(vm.appResourceUrl + '/create').then(function(response) {
                    if ( response.data && response.data.mailing_lists && response.data.mailing_lists.length )
                        vm.mailing_lists = response.data.mailing_lists;

                    progress.finish();
                    vm.fetchingData = false;

                }, function(error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();

                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            createResource() {
                this.appCreateResource();
            },
            clearDefaults() {
                this.selected_mailing_lists = [];
            }
        },
        watch: {
            'selected_mailing_lists': function(newVal) {
                this.resource.mailing_lists = this.flattenedMLists;
            },

        }
    }
</script>
