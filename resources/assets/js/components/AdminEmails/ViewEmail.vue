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
                <div class="row" v-if="resource.recipients_num">
                    <div class="col-sm-3 text-sm-right font-weight-bold">Recipients ({{ resource.recipients_num }}):</div>
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
                                <div class="row">
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

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="pointer-cursor">

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="recipient in orderedRecipients">

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
                    status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '', user: {}, campaign: '', url: '', pdf: ''},
                recipientsViewOptions: [
                    { text: 'Select Recipients To View', value: '' },
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
                orderAttr: 'updated_at',
                order: 'desc',
                orderToggle: -1,
                perPage: 25,
                searchText: '',
                searching: false,
            }
        },
        computed: {
            orderedRecipients() {
                return _.orderBy(this.recipients, ['first_name', 'last_name'], ['asc']);
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
                });
            },
            resizeIframe(event) {
                let iframe = event.target;
                if ( iframe )
                    iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
            },
            fetchRecipients() {
                let vm = this;
                vm.fetchingRecipients = true;

                vm.viewingRecipients = true;
                vm.fetchingRecipients = false;
            },
            doRecipientsSearch() {
                let vm = this;

                if ( vm.searchText.length ) {
//                    vm.appResources = [];
//                    vm.appPagination = vm.appGetInitialPagination();
//                    vm.appSearching = true;
//                    if ( typeof vm.fetchResources === 'function' )
//                        vm.fetchResources();
                }
            },
            getInitialPagination() {
                return {
                    total: 0,
                    per_page: 100,
                    current_page: 1,
                    last_page: 0,
                    from: 1,
                    to: 100
                };
            },
        },
        watch: {
            'recipientsViewOption'(newVal) {
                let vm = this;
                if ( newVal.length ) {
                    vm.pagination = vm.getInitialPagination();
                    vm.fetchRecipients();
                }
                else
                    vm.viewingRecipients = false;

            }
        },
    }
</script>
