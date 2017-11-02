<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <h3>
                    {{ resource.subject }}
                    <a class="btn" v-bind:href="resource.url" target="_blank" title="Open" data-toggle="tooltip"><i class="fa fa-external-link"></i></a>
                    <a class="btn" v-bind:href="resource.pdf" target="_blank" title="Generate PDF" data-toggle="tooltip"><i class="fa fa-file-pdf-o"></i></a>
                </h3>
                <div class="row">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Created:</div>
                    <div class="col-sm-9">{{ resource.created_at | dateToTheMinute }}</div>
                </div>
                <div class="row" v-if="resource.sent_at">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Sent:</div>
                    <div class="col-sm-9">{{ resource.sent_at | dateToTheMinute }}</div>
                </div>
                <div class="row" v-else="">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Last Update:</div>
                    <div class="col-sm-9">{{ resource.updated_at | dateToTheMinute }}</div>
                </div>
                <div class="row">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Status:</div>
                    <div class="col-sm-9">
                        <i v-if="resource.status === -2" class="fa fa-spinner"></i>
                        <i v-if="resource.status === -1" class="fa fa-clock-o"></i>
                        <i v-if="resource.status === 0" class="fa fa-exclamation-triangle text-danger"></i>
                        <i v-if="resource.status === 1" class="fa fa-check"></i>
                        {{ resource.friendly_status }}
                    </div>
                </div>
                <div class="row" v-if="resource.sender">
                    <div class="col-sm-3 text-sm-right font-weight-bold">From:</div>
                    <div class="col-sm-9">{{ resource.sender }}</div>
                </div>
                <div class="row" v-if="resource.reply_to_email">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Reply To:</div>
                    <div class="col-sm-9">{{ resource.reply_to_email }}</div>
                </div>
                <div class="row">
                    <div class="col-sm-3 text-sm-right font-weight-bold">User:</div>
                    <div class="col-sm-9">{{ resource.user.name }}</div>
                </div>
                <div class="row">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Campaign:</div>
                    <div class="col-sm-9">{{ resource.campaign.name }}</div>
                </div>
                <div v-if="resource.status === -1" class="row"> <!-- Just for a Scheduled Email -->
                    <div class="col-sm-3 text-sm-right font-weight-bold">Recipients:</div>
                    <div class="col-sm-9">{{ resource.recipients_num }}</div>
                </div>
                <div class="row" v-if="resource.injections_count">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Recipients ({{ resource.injections_count }}):</div>
                    <div class="col-sm-9">
                        <label for="viewRecipients"></label><select id="viewRecipients" v-model="recipientsViewOption">
                        <option v-for="option in recipientsViewOptions" v-bind:value="option.value">
                                {{ option.text }}
                        </option></select>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-12">
                        <div v-if="! viewingRecipients">
                            <iframe v-bind:src="resource.url" style="width: 100%; border:none;"  v-on:load="resizeIframe($event)"></iframe>
                        </div>
                        <div v-else="">
                            <div v-if="fetchingRecipients">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div v-else="">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <form class="form-inline" v-on:submit.prevent="doRecipientsSearch">
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
                                                <th v-on:click.prevent="changeSort('first_name')">Recipient <span v-html="getSortMarkup('first_name')"></span></th>
                                                <th style="cursor: default">Status</th>
                                                <template v-if="recipientsViewOption === 'injections'">
                                                    <th v-on:click.prevent="changeSort('injected_at')">Dispatched <span v-html="getSortMarkup('injected_at')"></span></th>
                                                </template>
                                                <template v-if="recipientsViewOption === 'deliveries'">
                                                    <th v-on:click.prevent="changeSort('delivered_at')">Delivered <span v-html="getSortMarkup('delivered_at')"></span></th>
                                                </template>
                                                <template v-if="recipientsViewOption === 'opens'">
                                                    <th v-on:click.prevent="changeSort('ip_address')">IP <span v-html="getSortMarkup('ip_address')"></span></th>
                                                    <th v-on:click.prevent="changeSort('country')">Country <span v-html="getSortMarkup('country')"></span></th>
                                                    <th v-on:click.prevent="changeSort('device')">Device <span v-html="getSortMarkup('device')"></span></th>
                                                    <th v-on:click.prevent="changeSort('os')">OS <span v-html="getSortMarkup('os')"></span></th>
                                                    <th v-on:click.prevent="changeSort('browser')">Browser <span v-html="getSortMarkup('browser')"></span></th>
                                                    <th v-on:click.prevent="changeSort('opens')">Opens <span v-html="getSortMarkup('opens')"></span></th>
                                                    <th v-on:click.prevent="changeSort('first_opened_at')">First Opened <span v-html="getSortMarkup('first_opened_at')"></span></th>
                                                    <th v-on:click.prevent="changeSort('last_opened_at')">Last Opened <span v-html="getSortMarkup('last_opened_at')"></span></th>
                                                </template>
                                                <template v-if="recipientsViewOption === 'clicks'">
                                                    <th v-on:click.prevent="changeSort('link')">Link <span v-html="getSortMarkup('link')"></span></th>
                                                    <th v-on:click.prevent="changeSort('clicks')">Clicks <span v-html="getSortMarkup('clicks')"></span></th>
                                                    <th v-on:click.prevent="changeSort('first_clicked_at')">First Clicked <span v-html="getSortMarkup('first_clicked_at')"></span></th>
                                                    <th v-on:click.prevent="changeSort('last_clicked_at')">Last Clicked <span v-html="getSortMarkup('last_clicked_at')"></span></th>
                                                </template>
                                                <template v-if="recipientsViewOption === 'failures'">
                                                    <th v-on:click.prevent="changeSort('type')">Type <span v-html="getSortMarkup('type')"></span></th>
                                                    <th v-on:click.prevent="changeSort('reason')">Reason <span v-html="getSortMarkup('reason')"></span></th>
                                                    <th v-on:click.prevent="changeSort('fails')">Fails <span v-html="getSortMarkup('fails')"></span></th>
                                                    <th v-on:click.prevent="changeSort('first_failed_at')">First Failed <span v-html="getSortMarkup('first_failed_at')"></span></th>
                                                    <th v-on:click.prevent="changeSort('last_failed_at')">Last Failed <span v-html="getSortMarkup('last_failed_at')"></span></th>
                                                </template>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="recipient in orderedRecipients">
                                                <td>{{ recipient.first_name }} {{ recipient.last_name }} <{{ recipient.email }}></td>
                                                <td>
                                                    <small v-if="recipient.failed" v-html="getStatusHtml('failed')"></small>
                                                    <small v-else-if="recipient.clicked" v-html="getStatusHtml('clicked')"></small>
                                                    <small v-else-if="recipient.opened" v-html="getStatusHtml('opened')"></small>
                                                    <small v-else-if="recipient.delivered" v-html="getStatusHtml('delivered')"></small>
                                                    <small v-else="" v-html="getStatusHtml('unknown')"></small>
                                                </td>
                                                <template v-if="recipientsViewOption === 'injections'">
                                                    <td><span v-bind:title="recipient.injected_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.injected_at | dateToTheMinute }}</span></td>
                                                </template>
                                                <template v-if="recipientsViewOption === 'deliveries'">
                                                    <td><span v-bind:title="recipient.delivered_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.delivered_at | dateToTheMinute }}</span></td>
                                                </template>
                                                <template v-if="recipientsViewOption === 'opens'">
                                                    <td>{{ recipient.ip_address }}</td>
                                                    <td>{{ recipient.country }}</td>
                                                    <td>{{ recipient.device }}</td>
                                                    <td>{{ recipient.os }}</td>
                                                    <td>{{ recipient.browser }}</td>
                                                    <td>{{ recipient.opens }}</td>
                                                    <td><span v-bind:title="recipient.first_opened_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.first_opened_at | dateToTheMinute }}</span></td>
                                                    <td><span v-bind:title="recipient.last_opened_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.last_opened_at | dateToTheMinute }}</span></td>
                                                </template>
                                                <template v-if="recipientsViewOption === 'clicks'">
                                                    <td>{{ recipient.link }}</td>
                                                    <td>{{ recipient.clicks }}</td>
                                                    <td><span v-bind:title="recipient.first_clicked_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.first_clicked_at | dateToTheMinute }}</span></td>
                                                    <td><span v-bind:title="recipient.last_clicked_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.last_clicked_at | dateToTheMinute }}</span></td>
                                                </template>
                                                <template v-if="recipientsViewOption === 'failures'">
                                                    <td>{{ recipient.type }}</td>
                                                    <td>{{ recipient.reason ? recipient.reason : '-' }}</td>
                                                    <td>{{ recipient.fails }}</td>
                                                    <td><span v-bind:title="recipient.first_failed_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.first_failed_at | dateToTheMinute }}</span></td>
                                                    <td><span v-bind:title="recipient.last_failed_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ recipient.last_failed_at | dateToTheMinute }}</span></td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <pagination :pagination="pagination" :callback="fetchRecipients" :options="paginationOptions" class="mt-5 mb-3"></pagination>
                            </div>
                        </div>
                    </div>
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
                this.showResource();
                this.appInitialiseTooltip();
                this.applyListeners();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0,
                    status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '', user: {},
                    campaign: '', url: '', pdf: '', injections_count: 0, deliveries_count: 0, opens_count: 0,
                    clicks_count: 0, failures_count: 0},
                recipientsViewOptions: [
                    { text: 'Select Recipients to View', value: '' },
                    { text: 'All', value: 'injections' },
                    { text: 'Delivered', value: 'deliveries' },
                    { text: 'Confirmed Open', value: 'opens' },
                    { text: 'Clicked', value: 'clicks' },
                    { text: 'Failed', value: 'failures' },
                ],
                recipientsViewOption: '',
                viewingRecipients: false,
                fetchingRecipients: true,
                recipients: [],
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
                orderAttr: 'first_name',
                order: 'asc',
                orderToggle: 1,
                perPage: 25,
                searchText: '',
                searching: false,
            }
        },
        computed: {
            orderedRecipients() {
                return _.orderBy(this.recipients, [this.orderAttr, 'first_name'], [( this.orderToggle === 1 ) ? 'asc' : 'desc', 'asc']);
            }
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
            applyListeners() {
                let vm = this;

                vm.$on('successfulfetch', function () {
                    vm.rootEventsHub.$emit('show-edit-tab', { resource: vm.resource });

                    vm.recipientsViewOptions = [
                        { text: 'Select Recipients to View', value: '' },
                        { text: 'All (' + vm.resource.injections_count + ')', value: 'injections' },
                        { text: 'Delivered (' + vm.resource.deliveries_count + ')', value: 'deliveries' },
                        { text: 'Confirmed Open (' + vm.resource.opens_count + ')', value: 'opens' },
                        { text: 'Clicked (' + vm.resource.clicks_count + ')', value: 'clicks' },
                        { text: 'Failed (' + ( vm.resource.failures_count ) + ')', value: 'failures' }
                    ];
                });
            },
            resizeIframe(event) {
                let iframe = event.target;
                if ( iframe )
                    iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
            },
            fetchRecipients() {
                let vm = this;
                let progress = vm.$Progress;
                let orderBy = vm.orderAttr;
                let lastPage = _.ceil(vm.pagination.total / vm.pagination.per_page);

                let params = {
                    type: vm.recipientsViewOption,
                    perPage: vm.pagination.per_page,
                    page: ( lastPage < vm.pagination.last_page ) ? 1 : vm.pagination.current_page,
                    orderBy: orderBy,
                    order: ( vm.orderToggle === 1 ) ? 'asc' : 'desc',
                };

                if ( vm.searchText.length )
                    params.search = vm.searchText;

                progress.start();
                vm.viewingRecipients = true;
                vm.fetchingRecipients = true;

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/recipients', {params : params}).then(function(response) {
                    let recipients = response.data.recipients;

                    if ( recipients && recipients.data && recipients.data.length ) {
                        vm.recipients = recipients.data;
                        vm.orderAttr = orderBy;
                        vm.order = vm.orderToggle === 1 ? 'asc' : 'desc';

                        vm.$set(vm, 'pagination', {
                            total: recipients.total,
                            per_page: recipients.per_page,
                            current_page: recipients.current_page,
                            last_page: recipients.last_page,
                            from: recipients.from,
                            to: recipients.to
                        });

                        vm.appInitialiseTooltip();
                        progress.finish();
                        vm.fetchingRecipients = false;
                    }
                    else {
                        let message = vm.searching ? 'Your search returned no results. Please try again with different keywords' : 'No records found';

                        if ( vm.searching )
                            vm.appCustomErrorAlertConfirmed(message);

                        vm.searchText = '';
                        vm.recipients = [];
                        vm.fetchingRecipients = false;
                        progress.fail();
                    }

                }, function(error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else if ( error.status && error.status === 404 && error.data ){
                        let message = vm.searching ? 'Your search returned no results. Please try again with different keywords' : error.data.error;

                        if ( vm.searching )
                            vm.appCustomErrorAlertConfirmed(message);
                    }
                    else
                        vm.appGeneralErrorAlert();

                    vm.searchText = '';
                    vm.recipients = [];
                    vm.fetchingRecipients = false;
                    progress.fail();
                });
            },
            doRecipientsSearch() {
                let vm = this;

                if ( vm.searchText.length ) {
                    vm.recipients = [];
                    vm.pagination = vm.getInitialPagination();
                    vm.searching = true;

                    vm.fetchRecipients();
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

                vm.fetchRecipients();
            },
            getSortMarkup(attr) {
                let vm = this;
                let html = '';

                if ( _.toLower(vm.orderAttr) === _.toLower(attr) )
                    html = ( vm.orderToggle === 1 ) ? '&darr;' : '&uarr;';
                return html;
            },
            getStatusHtml(status) {
                switch( status ) {
                    case 'failed':
                        return "<i title='Failed' data-toggle='tooltip' class='fa fa-times'></i>";
                        break;
                    case 'clicked':
                        return "<i title='Clicked' data-toggle='tooltip' class='fa fa-mouse-pointer'></i>";
                        break;
                    case 'opened':
                        return "<i title='Opened' data-toggle='tooltip' class='fa fa-envelope-open-o'></i>";
                        break;
                    case 'delivered':
                        return "<i title='Delivered' data-toggle='tooltip' class='fa fa-envelope'></i>";
                        break;
                    case 'unknown':
                        return "<i title='Dispatched' data-toggle='tooltip' class='fa fa-paper-plane-o'></i>";
                        break;
                }
            }
        },
        watch: {
            recipientsViewOption(newVal) {
                let vm = this;
                if ( newVal.length ) {
                    vm.pagination = vm.getInitialPagination();
                    vm.fetchRecipients();
                }
                else
                    vm.viewingRecipients = false;

            },
            perPage(newVal, oldVal) {
                let vm = this;

                if ( newVal !== oldVal )
                    vm.pagination.per_page = newVal;
            },
            searchText(newVal, oldVal) {
                let vm = this;

                if ( oldVal.length && ! newVal.length ) {
                    vm.searching = false;
                    vm.fetchRecipients();
                }
            },
        },
    }
</script>
