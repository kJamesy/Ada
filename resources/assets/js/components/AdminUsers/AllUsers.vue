<template>
    <div class="mt-3">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>
        <div v-if="! fetchingData && appResourceCount">
            <div v-if="appUserHasPermission('read')">
                <a href="#" v-on:click.prevent="exportAll" class="btn btn-link pull-right"><i class="icon ion-android-download"></i></a>
                <div class="clearfix mb-2"></div>
                <form v-on:submit.prevent="appDoSearch">
                    <div class="form-group">
                        <input type="text" v-model.trim="appSearchText" placeholder="Search" class="form-control" />
                    </div>
                </form>
                <div class="mt-md-4 mb-md-4">
                    <form class="form-inline pull-left" v-if="appSelectedResources.length">
                        <label class=" mr-sm-2" for="quick-edit">Options</label>
                        <select class="custom-select form-control mb-2 mb-sm-0 mr-sm-5" v-model="appQuickEditOption" id="quick-edit">
                            <option v-for="option in quickEditOptions" v-bind:value="option.value" v-if="appUserHasPermission(option.value)">
                                {{ option.text }}
                            </option>
                        </select>
                    </form>
                    <form class="form-inline pull-right">
                        <span class="mr-3">Page {{ appPagination.current_page }} of {{ appPagination.last_page }} [<b>{{ appPagination.total }} items</b>]</span>
                        <label class=" mr-sm-2" for="records_per_page">Per Page</label>
                        <select class="custom-select form-control mb-2 mb-sm-0" v-model="appPerPage" id="records_per_page">
                            <option v-for="option in appPerPageOptions" v-bind:value="option.value">
                                {{ option.text }}
                            </option>
                        </select>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-info">
                        <thead>
                            <tr class="pointer-cursor">
                                <th class="normal-cursor checkbox-th" v-if="appUserHasPermission('update')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" v-model="selectAll">
                                        <label class="custom-control-label" for="selectAllCheckbox"></label>
                                    </div>
                                </th>
                                <th v-on:click.prevent="appChangeSort('first_name')">Name <span v-html="appGetSortMarkup('first_name')"></span></th>
                                <th v-on:click.prevent="appChangeSort('email')">Email <span v-html="appGetSortMarkup('email')"></span></th>
                                <th v-on:click.prevent="appChangeSort('username')">Username <span v-html="appGetSortMarkup('username')"></span></th>
                                <th class="normal-cursor">Last Login</th>
                                <th v-on:click.prevent="appChangeSort('active')" >Active <span v-html="appGetSortMarkup('active')"></span></th>
                                <th v-on:click.prevent="appChangeSort('updated_at')" >Updated <span v-html="appGetSortMarkup('updated_at')"></span></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in orderedAppResources">
                                <td v-if="appUserHasPermission('update')">
                                    <template v-if="appUserHasPermissionOnUser('update', user)">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" v-bind:id="'select_' + user.id" v-model="appSelectedResources" v-bind:value="user.id">
                                            <label class="custom-control-label" v-bind:for="'select_' + user.id"></label>
                                        </div>
                                    </template>
                                </td>
                                <td>{{ user.name }} <span v-if="user.is_super_admin" class="text-dark" title="Super Admin" data-toggle="tooltip"> <i class="icon ion-android-star"></i> </span></td>
                                <td>{{ user.email }}</td>
                                <td> {{ user.username }}</td>
                                <td v-bind:title="getLastLoginLongDateHtml(user.last_login)" data-toggle="tooltip" v-html="getLastLoginHtml(user.last_login)"></td>
                                <td v-html="appActiveMarkup(user.active)"></td>
                                <td v-bind:title="user.updated_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ user.updated_at | dateToTheDay }}</td>
                                <td>
                                    <template v-if="appUserHasPermissionOnUser('read', user)">
                                        <router-link v-bind:to="{ name: 'admin_users.view', params: { id: user.id }}" class="btn btn-sm rounded-circle btn-pink"><i class="ion-ios-body-outline"></i></router-link>
                                    </template>
                                    <template v-if="appUserIsCurrentUser(user)">
                                        <a v-bind:href="appUserHome" class="btn btn-sm rounded-circle btn-dark" data-toggle="tooltip" title="Yours truly :)"><i class="ion-ios-body-outline"></i></a>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <pagination :pagination="appPagination" :callback="fetchResources" :options="appPaginationOptions" class="mt-5 mb-3"></pagination>

            </div>
            <div v-if="! appUserHasPermission('read')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </div>
        <div v-if="! fetchingData && ! appResourceCount" class="mt-5">
            No records found
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.appInitialiseSettings();
                this.fetchResources();
                this.applyListeners();
            });
        },
        data() {
            return {
                fetchingData: true,
                quickEditOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Export', value: 'export' },
                    { text: 'Activate', value: 'activate' },
                    { text: 'Deactivate', value: 'deactivate' },
                    { text: 'Delete', value: 'delete' }
                ]
            }
        },
        computed: {
            selectAll: {
                get() {
                    return this.appResourcesIds ? this.appSelectedResources.length === this.appResourcesIds.length : false;
                },
                set(value) {
                    let vm = this;
                    let resourcesIds = _.cloneDeep(vm.appResourcesIds);
                    let selected = [];

                    if ( value ) {
                        _.forEach(resourcesIds, function(id) {
                            if ( id !== vm.appUser.id )
                                selected.push(id);
                        });
                    }

                    this.appSelectedResources = selected;
                }
            }
        },
        methods: {
            fetchResources(orderAttr, orderToggle) {
                this.appFetchResources(this, orderAttr, orderToggle);
                this.appInitialiseTooltip();
            },
            quickEditResources() {
                this.appQuickEditResources();
            },
            exportAll() {
                this.appExportAll();
            },
            applyListeners() {
                let vm = this;

                vm.$on('successfulfetch', function () {
                    vm.appInitialiseTooltip();
                });
            },
            getLastLoginHtml(last_login) {
                return last_login ? this.$options.filters.dateToTheDay(last_login.attempted_at) : '&mdash;';
            },
            getLastLoginLongDateHtml(last_login) {
                return last_login ? this.$options.filters.dateToTheMinWithDayOfWeek(last_login.attempted_at) : '-';
            }
        },
    }
</script>
