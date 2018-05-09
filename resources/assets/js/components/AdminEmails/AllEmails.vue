<template>
    <div class="mt-3">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>
        <div v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <template v-if="appResourceCount">
                    <a href="#" v-on:click.prevent="exportAll" class="btn btn-link pull-right" title="Export All" data-toggle="tooltip"><i class="icon ion-android-download"></i></a>
                    <div class="clearfix mb-2"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <form v-on:submit.prevent="appDoSearch">
                                <div class="form-group">
                                    <label class="form-control-label">&nbsp;</label>
                                    <input type="text" v-model.trim="appSearchText" placeholder="Search" class="form-control" />
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form v-if="isLandingPage()">
                                <label class="form-control-label" for="filter_options">Filter By</label>
                                <select class="custom-select form-control" v-model="filterOption" id="filter_options">
                                    <option value="">Select Filter</option>
                                    <option v-for="option in filterOptions" v-bind:value="option.filter">
                                        {{ option.filter }}
                                    </option>
                                </select>
                            </form>
                            <form v-if="isLandingPage() && filterOption" class="mt-4">
                                <select class="custom-select form-control" v-model="selectedFilter" id="filter_options_group">
                                    <option value="">Select {{ filterOption }}</option>
                                    <option v-for="option in filters" v-bind:value="option.id">
                                        {{ option.name }}
                                    </option>
                                </select>
                            </form>
                            <form v-if="! isLandingPage() && filterOption">
                                <label class="form-control-label" for="filter_options_group_2">Filter By {{ filterOption }}</label>
                                <select class="custom-select form-control" v-model="selectedFilter" id="filter_options_group_2">
                                    <option value="">Select {{ filterOption }}</option>
                                    <option v-for="option in filters" v-bind:value="option.id">
                                        {{ option.name }}
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="mt-4 mb-4">
                        <form class="form-inline pull-left" v-if="appSelectedResources.length">
                            <label class="form-control-label mr-sm-2" for="quick-edit">Options</label>
                            <select class="custom-select form-control mb-2 mb-sm-0 mr-sm-5" v-model="appQuickEditOption" id="quick-edit">
                                <option v-for="option in dynamicQuickEditOptions" v-bind:value="option.value" v-if="appUserHasPermission(option.value)">
                                    {{ option.text }}
                                </option>
                            </select>
                        </form>
                        <form class="form-inline pull-right">
                            <span class="mr-3">Page {{ appPagination.current_page }} of {{ appPagination.last_page }} [<b>{{ appPagination.total }} items</b>]</span>
                            <label class="form-control-label mr-sm-2" for="records_per_page">Per Page</label>
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
                                            <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" v-model="appSelectAll">
                                            <label class="custom-control-label" for="selectAllCheckbox"></label>
                                        </div>
                                    </th>
                                    <th v-on:click.prevent="appChangeSort('subject')">Subject <span v-html="appGetSortMarkup('subject')"></span></th>
                                    <th v-if="! appIsDraftsPage" v-on:click.prevent="appChangeSort('sender')">Sender <span v-html="appGetSortMarkup('sender')"></span></th>
                                    <th v-if="! appIsDraftsPage" v-on:click.prevent="appChangeSort('recipients_num')">Recipients <span v-html="appGetSortMarkup('recipients_num')"></span></th>
                                    <th v-on:click.prevent="appChangeSort('status')">Status <span v-html="appGetSortMarkup('status')"></span></th>
                                    <th v-on:click.prevent="appChangeSort('created_at')" >Created <span v-html="appGetSortMarkup('created_at')"></span></th>
                                    <th v-if="appIsDraftsPage" v-on:click.prevent="appChangeSort('updated_at')" >Updated <span v-html="appGetSortMarkup('updated_at')"></span></th>
                                    <th v-if="! appIsDraftsPage" v-on:click.prevent="appChangeSort('sent_at')" >Sent <span v-html="appGetSortMarkup('sent_at')"></span></th>
                                    <th v-if="appUserHasPermission('read')"></th>
                                    <th v-if="appUserHasPermission('read')"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="resource in orderedAppResources">
                                    <td v-if="appUserHasPermission('update')">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" v-bind:id="'select_' + resource.id" v-model="appSelectedResources" v-bind:value="resource.id">
                                            <label class="custom-control-label" v-bind:for="'select_' + resource.id"></label>
                                        </div>
                                    </td>
                                    <td>{{ resource.subject }}</td>
                                    <td v-if="! appIsDraftsPage">
                                        <span v-if="resource.sender">{{ resource.sender }}</span>
                                        <span v-else=""><em>&mdash;</em></span>
                                    </td>
                                    <td v-if="! appIsDraftsPage">
                                        <span v-if="resource.recipients_num">{{ resource.recipients_num }}</span>
                                        <span v-else=""><em>&mdash;</em></span>
                                    </td>
                                    <td v-bind:title="resource.friendly_status" data-toggle="tooltip">
                                        <i v-if="resource.status === -2" class="icon ion-more"></i>
                                        <i v-if="resource.status === -1" class="icon ion-android-alarm-clock"></i>
                                        <i v-if="resource.status === 0" class="icon ion-alert-circled text-danger"></i>
                                        <i v-if="resource.status === 1" class="icon ion-ios-checkmark-outline"></i>
                                    </td>
                                    <td><span v-bind:title="resource.created_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.created_at | dateToTheDay }}</span></td>
                                    <td v-if="appIsDraftsPage"><span v-bind:title="resource.updated_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.updated_at | dateToTheDay }}</span></td>
                                    <td v-if="! appIsDraftsPage">
                                        <span v-if="resource.sent_at" v-bind:title="resource.sent_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.sent_at | dateToTheDay }}</span>
                                        <span v-else=""><em>&mdash;</em></span>
                                    </td>
                                    <td v-if="appUserHasPermission('read')">
                                        <router-link v-bind:to="{ name: 'admin_emails.view', params: { id: resource.id }}" class="btn btn-sm rounded-circle btn-pink"><i class="icon ion-eye"></i></router-link>
                                    </td>
                                    <td v-if="appUserHasPermission('read')">
                                        <router-link v-bind:to="{ name: 'admin_emails.stats', params: { id: resource.id }}" class="btn btn-sm rounded-circle btn-pink"
                                                     v-bind:class="resource.status < 1 ? 'disabled' : ''">
                                            <i class="ion-stats-bars"></i>
                                        </router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <pagination :pagination="appPagination" :callback="fetchResources" :options="appPaginationOptions" class="mt-5 mb-3"></pagination>

                </template>
                <div v-else="" class="mt-5">
                    No items found
                </div>
            </div>
            <div v-if="! appUserHasPermission('read')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </div>
        <div class="mt-3 mb-3 font-italic text-right" v-if="! fetchingData && appDeletedNum && isLandingPage()">
            <router-link v-bind:to="{ name: 'admin_emails.trash'}" class="btn btn-link"><i class="icon ion-trash-a"></i> Deleted Items ({{ appDeletedNum }})</router-link>
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
                screen: 'emails',
                quickEditOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Export', value: 'export' },
                    { text: 'Delete', value: 'delete' }
                ],
                users: [],
                user: {name: ''},
                campaigns: [],
                campaign: {name: ''},
                trash: 0,
                filterOptions: [
                    { filter: 'Campaign'},
                    { filter: 'User'}
                ],
                filterOption: '',
                filters: [],
                selectedFilter: ''
            }
        },
        computed: {
            dynamicQuickEditOptions() {
                let vm = this;
                if ( vm.appIsDraftsPage ) {
                    return [
                        { text: 'Select Option', value: '' },
                        { text: 'Export', value: 'export' },
                        { text: 'Destroy', value: 'destroy' }
                    ]
                }
                else {
                    return [
                        { text: 'Select Option', value: '' },
                        { text: 'Export', value: 'export' },
                        { text: 'Delete', value: 'delete' }
                    ]
                }
            }
        },
        methods: {
            fetchResources(orderAttr, orderToggle) {
                this.appFetchResources(this, orderAttr, orderToggle);
            },
            quickEditResources() {
                this.appQuickEditResources();
            },
            exportAll() {
                this.appExportAll();
            },
            setOtherData() {
                let vm = this;

                if ( typeof vm.appFetchResponse !== 'undefined' ) {
                    let response = vm.appFetchResponse;

                    if ( response.data.deletedNum ) {
                        vm.rootEventsHub.$emit('show-deleted-tab', { deletedNum: response.data.deletedNum });
                        vm.appDeletedNum = response.data.deletedNum;
                    }

                    if ( response.data.draftsNum )
                        vm.rootEventsHub.$emit('show-drafts-tab', { draftsNum: response.data.draftsNum });

                    if ( response.data.campaigns )
                        vm.campaigns = response.data.campaigns;

                    if ( response.data.campaign )
                        vm.campaign = response.data.campaign;

                    if ( response.data.users )
                        vm.users = response.data.users;

                    if ( response.data.user )
                        vm.user = response.data.user;

                    if ( vm.appBelongingToUser )
                        vm.filterOption = 'User';

                    if ( vm.appBelongingToCampaign )
                        vm.filterOption = 'Campaign';
                }
            },
            applyListeners() {
                let vm = this;

                vm.$on('successfulfetch', function () {
                    vm.setOtherData();
                    vm.appInitialiseTooltip();
                });
            },
            isLandingPage() {
                return this.$route.name === 'admin_emails.index';
            }
        },
        watch: {
            'filterOption': function(newVal) {

                let vm = this;

                switch(newVal) {
                    case 'Campaign':
                        vm.filters = vm.campaigns;
                        vm.selectedFilter = vm.appBelongingToCampaign ? vm.appBelongingToCampaign : '';
                        break;
                    case 'User':
                        vm.filters = vm.users;
                        vm.selectedFilter = vm.appBelongingToUser ? vm.appBelongingToUser : '';
                        break;
                }
            },
            'selectedFilter': function(newVal) {

                let vm = this;

                if ( newVal ) {
                    switch (vm.filterOption) {
                        case 'Campaign':
                            vm.$router.push({name: 'admin_emails.campaign', params: {campaignId: parseInt(newVal)}});
                            break;
                        case 'User':
                            vm.$router.push({name: 'admin_emails.user', params: {userId: parseInt(newVal)}});
                            break;
                    }
                }
                else
                    vm.$router.push({name: 'admin_emails.index'});

            }
        }
    }
</script>
