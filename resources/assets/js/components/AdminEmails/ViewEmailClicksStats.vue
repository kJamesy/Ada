<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <h3>{{ resource.subject }}</h3>

                <div v-if="fetchingClicks">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
                <div v-else="">
                    <div class="row mt-5 mb-4">
                        <div class="col">
                            <form class="form-inline pull-left">
                                <label class="form-control-label mr-sm-2" for="statType">Viewing</label>
                                <select class="custom-select form-control mb-2 mb-sm-0" v-model="statType" id="statType">
                                    <option v-for="option in statTypes" v-bind:value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <form class="form-inline" v-on:submit.prevent="doClicksSearch">
                                <div class="form-group">
                                    <label class="form-control-label">&nbsp;</label>
                                    <input type="text" v-model.trim="searchText" placeholder="Search" class="form-control" />
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form class="form-inline pull-right">
                                <span class="mr-3">Page {{ pagination.current_page }} of {{ pagination.last_page }} [<b>{{ pagination.total }} items</b>]</span>
                                <label class="form-control-label mr-sm-2" for="records_per_page">Per Page</label>
                                <select class="custom-select form-control mb-2 mb-sm-0" v-model="perPage" id="records_per_page">
                                    <option v-for="option in perPageOptions" v-bind:value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive mt-5">
                        <table class="table table-striped">
                            <thead>
                                <tr class="pointer-cursor">
                                    <th v-on:click.prevent="changeSort('link')">Link <span v-html="getSortMarkup('link')"></span></th>
                                    <th v-on:click.prevent="changeSort('clicks_count')">Unique Clicks <span v-html="getSortMarkup('clicks_count')"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="click in orderedClicks">
                                    <td>{{ click.link }}</td>
                                    <td>{{ click.clicks_count }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <pagination :pagination="pagination" :callback="showResource" :options="paginationOptions" class="mt-5 mb-3"></pagination>
                </div>

            </div>
            <div v-else="">
                <i class="fa fa-warning"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                let vm = this;
                vm.pagination = vm.getInitialPagination();
                vm.showResource();
                vm.appInitialiseTooltip();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: { id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0, status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '',
                    clicks_stats: [] },
                statType: 'clicks',
                fetchingClicks: true,
                pagination: {},
                paginationOptions: {
                    offset: 5,
                    alwaysShowPrevNext: true
                },
                perPageOptions: [
                    { text: '25', value: 25} ,
                    { text: '50', value: 50 },
                    { text: '100', value: 100 },
                    { text: '500', value: 500 }
                ],
                orderAttr: 'clicks_count',
                order: 'desc',
                orderToggle: -1,
                perPage: 25,
                searchText: '',
                searching: false,
            }
        },
        computed: {
            orderedClicks() {
                return _.orderBy(this.resource.clicks_stats.data, [this.orderAttr, 'clicks_count'], [( this.orderToggle === 1 ) ? 'asc' : 'desc', 'asc']);
            }
        },
        methods: {
            showResource() {
                let vm = this;
                let progress = vm.$Progress;
                let orderBy = vm.orderAttr;
                let lastPage = _.ceil(vm.pagination.total / vm.pagination.per_page);

                let params = {
                    perPage: vm.pagination.per_page,
                    page: ( lastPage < vm.pagination.last_page ) ? 1 : vm.pagination.current_page,
                    orderBy: orderBy,
                    order: ( vm.orderToggle === 1 ) ? 'asc' : 'desc',
                };

                if ( vm.searchText.length )
                    params.search = vm.searchText;

                progress.start();
                vm.fetchingClicks = true;

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/' + vm.statType + '-stats', {params : params}).then(function (response) {

                    if ( response.data && response.data.resource && response.data.resource.clicks_stats.data.length ) {
                        _.forEach(vm.resource, function (val, idx) {
                            if ( response.data.resource.hasOwnProperty(idx) )
                                vm.$set(vm.resource, idx, response.data.resource[idx]);
                        });

                        vm.rootEventsHub.$emit('show-edit-tab', { resource: vm.resource });

                        vm.orderAttr = orderBy;
                        vm.order = vm.orderToggle === 1 ? 'asc' : 'desc';

                        let clicks_stats = response.data.resource.clicks_stats;

                        vm.$set(vm, 'pagination', {
                            total: clicks_stats.total,
                            per_page: clicks_stats.per_page,
                            current_page: clicks_stats.current_page,
                            last_page: clicks_stats.last_page,
                            from: clicks_stats.from,
                            to: clicks_stats.to
                        });

                        vm.appInitialiseTooltip();
                        progress.finish();
                    }
                    else {
                        let message = vm.searching ? 'Your search returned no results. Please try again with different keywords' : 'No records found';

                        if ( vm.searching )
                            vm.appCustomErrorAlertConfirmed(message);

                        vm.searchText = '';
                        vm.resource.clicks_stats = [];

                        progress.fail();
                    }

                    vm.fetchingData = false;
                    vm.fetchingClicks = false;
                }, function (error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else if ( error.status && error.status === 404 && error.data ){
                        let message = vm.searching ? 'Your search returned no results. Please try again with different keywords' : error.data.error;

                        if ( vm.searching )
                            vm.appCustomErrorAlertConfirmed(message);
                        else
                            vm.appCustomErrorAlert(error.data.error);
                    }
                    else
                        vm.appGeneralErrorAlert();

                    vm.searchText = '';
                    vm.resource.clicks_stats = [];

                    vm.fetchingData = false;
                    vm.fetchingClicks = false;
                    progress.fail();
                });
            },
            doClicksSearch() {
                let vm = this;

                if ( vm.searchText.length ) {
                    vm.resource.clicks_stats = [];
                    vm.pagination = vm.getInitialPagination();
                    vm.searching = true;

                    vm.showResource();
                }
            },
            getInitialPagination() {
                return {
                    total: 0,
                    per_page: 25,
                    current_page: 1,
                    last_page: 0,
                    from: 1,
                    to: 25
                };
            },
            changeSort(attr) {
                let vm = this;

                vm.orderToggle = ( _.toLower(vm.orderAttr) === _.toLower(attr) ) ? vm.orderToggle * -1 : 1;
                vm.orderAttr = attr;

                vm.showResource();
            },
            getSortMarkup(attr) {
                let vm = this;
                let html = '';

                if ( _.toLower(vm.orderAttr) === _.toLower(attr) )
                    html = ( vm.orderToggle === 1 ) ? '&darr;' : '&uarr;';
                return html;
            },
        },
        watch: {
            perPage(newVal, oldVal) {
                let vm = this;

                if ( newVal !== oldVal )
                    vm.pagination.per_page = newVal;
            },
            searchText(newVal, oldVal) {
                let vm = this;

                if ( oldVal.length && ! newVal.length ) {
                    vm.searching = false;
                    vm.showResource();
                }
            },
        },
    }
</script>