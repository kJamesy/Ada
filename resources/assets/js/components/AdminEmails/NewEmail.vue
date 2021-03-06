<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <a class="pull-right btn btn-link btn-sm" v-bind:class="! showToggleBtn ? 'invisible' : ''" v-on:click.prevent="toggleSenderDetails">Toggle Sender Details</a>
                    <div class="clearfix"></div>
                    <div class="row" v-show="showSenderDetails">
                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="sender_name">Sender Name <small class="text-danger">{{ validationErrors.sender_name }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="sender_name" v-model.trim="resource.sender_name" placeholder="A N Other">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="sender_email">Sender Email <small class="text-danger">{{ validationErrors.sender_email }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="sender_email" v-model.trim="resource.sender_email" placeholder="a.n.other@example.com">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="reply_to_email">Reply-To Email <small class="text-danger">{{ validationErrors.reply_to_email }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="reply_to_email" v-model.trim="resource.reply_to_email" placeholder="other@example.com">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" v-if="subscribers.length">
                        <label class="form-control-label">Recipient Subscribers <small class="text-danger">{{ validationErrors.subscribers }}</small></label>
                        <div class="">
                            <v-select :options="sortedSubscribers"  placeholder="Select Subscribers" v-model="selected_subscribers" multiple></v-select>
                        </div>
                    </div>
                    <div class="form-group" v-if="mailing_lists.length">
                        <label class="form-control-label">Recipient Mailing Lists <small class="text-danger">{{ validationErrors.mailing_lists }}</small></label>
                        <div class="">
                            <v-select :options="sortedMailingLists" placeholder="Select Mailing Lists" v-model="selected_mailing_lists" multiple></v-select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="subject">Subject <small class="text-danger">{{ validationErrors.subject }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="subject" v-model.trim="resource.subject" placeholder="Subject">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="email_campaign">Campaign <small class="text-danger">{{ validationErrors.campaign }}</small></label>
                            <div class="">
                                <select class="custom-select form-control" v-model="resource.campaign" id="email_campaign">
                                    <option v-for="option in sortedCampaigns" v-bind:value="option.id">
                                        {{ option.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="send_options">Option</label>
                            <select class="custom-select form-control" v-model="sendOption" id="send_options">
                                <option v-for="option in sendOptions" v-bind:value="option.value">
                                    {{ option.text }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6" v-show="sendOption === '1'">
                            <label class="form-control-label" for="schedule">Schedule <small><em>({{ datePickerOptions.format }})</em></small></label>
                            <datepicker :date="selectedDate" :option="datePickerOptions" id="schedule" class="form-control"></datepicker>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="content">Content <small class="text-danger">{{ validationErrors.content }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="content" v-model.trim="resource.content" rows="4" v-on:click="checkEditor" placeholder="Content"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="ml-md-auto">
                            <button type="submit" class="btn btn-info btn-lg">{{ submitBtnText }}</button>
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
                this.listenEvents();
                this.incrementTime();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: { sender_name: '', sender_email: '', reply_to_email: '', subscribers: [], mailing_lists: [], subject: '', campaign: '', content: '', is_draft: true, send_at: '' },
                validationErrors: {sender_name: '', sender_email: '', reply_to_email: '', subscribers: '', mailing_lists: '', subject: '', campaign: '', content: ''},
                subscribers: [],
                selected_subscribers: [],
                mailing_lists: [],
                selected_mailing_lists: [],
                campaigns: [],
                templates: [],
                editorReady: false,
                showSenderDetails: true,
                showToggleBtn: true,
                sendOptions: [
                    { text: 'Save As Draft', value: '0' },
                    { text: 'Send', value: '1' }
                ],
                sendOption: '0',
                datePickerOptions: {
                    type: 'min',
                    week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                    month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    format: 'YYYY-MM-DD HH:mm',
                    inputStyle: {
                        'display': 'block',
                        'padding': '0',
                        'line-height': 'inherit',
                        'font-size': 'inherit',
                        'border': '0',
                        'box-shadow': 'none',
                        'border-radius': '0',
                        'color': 'inherit',
                        'width': '100%'
                    },
                    buttons: {
                        ok: 'Insert',
                    },
                },
                selectedDate: { time: moment().format('YYYY-MM-DD HH:mm') },
                submitBtnText: 'Save as Draft'
            }
        },
        computed: {
            sortedSubscribers() {
                return _.sortBy(this.subscribers, ['name']);
            },
            flattenedSubscribers() {
                return _.flatMapDeep(this.selected_subscribers, function(subscriber) {
                    return subscriber.id;
                });
            },
            sortedMailingLists() {
                return _.sortBy(this.mailing_lists, ['name']);
            },
            flattenedMLists() {
                return _.flatMapDeep(this.selected_mailing_lists, function(mList) {
                    return mList.id;
                });
            },
            sortedCampaigns() {
                return _.sortBy(this.campaigns, ['name']);
            },
        },
        methods: {
            goTime() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/create').then(function(response) {
                    if ( response.data && response.data.subscribers && response.data.subscribers.length )
                        vm.subscribers = response.data.subscribers;

                    if ( response.data && response.data.mailing_lists && response.data.mailing_lists.length )
                        vm.mailing_lists = response.data.mailing_lists;

                    if ( response.data && response.data.campaigns && response.data.campaigns.length )
                        vm.campaigns = response.data.campaigns;

                    if ( response.data && response.data.templates && response.data.templates.length )
                        vm.templates = response.data.templates;

                    if ( response.data && response.data.sender_name )
                        vm.resource.sender_name = response.data.sender_name;

                    if ( response.data && response.data.sender_email )
                        vm.resource.sender_email = response.data.sender_email;

                    if ( response.data && response.data.reply_to_email )
                        vm.resource.reply_to_email = response.data.reply_to_email;

                    if ( vm.resource.sender_name && vm.resource.sender_email && vm.resource.reply_to_email )
                        vm.showSenderDetails = false;
                    else
                        vm.showToggleBtn = false;

                    vm.resource.send_at = moment(vm.selectedDate.time, vm.datePickerOptions.format).utc().format(vm.datePickerOptions.format);

                    vm.initTinyMce(10);
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
                let vm = this;

                if ( vm.hasBasicValidation() && ! vm.resource.is_draft ) {
                    swal({
                        title: 'Are you sure?',
                        text: 'You are about to send a mass email. Have you confirmed everything is all right?',
                        type: 'warning',
                        animation: 'slide-from-top',
                        showCancelButton: true,
                        confirmButtonText: 'Let\'s do it!'
                    }, function(){
                        vm.appCreateResource();
                    });
                }
                else
                    vm.appCreateResource();
            },
            initTinyMce(wait) {
                let vm = this;
                let defaultConfig = tinyMceConfig;

                defaultConfig.plugins[0] += ' template'; //Extra plugin

                let newCOnfig = {
                    selector: '#content',
                    setup: function(editor) {
                        editor.on('init', function() {
                            editor.setContent(vm.resource.content);
                            vm.editorReady = true;
                        });

                        editor.on('change keyup blur', function() {
                            vm.resource.content = editor.getContent();
                        });
                    },
                    templates: vm.templates
                };

                _.delay(function() {
                    tinymce.remove();
                    tinymce.init(_.assign(defaultConfig, newCOnfig));
                }, parseInt(wait));
            },
            checkEditor() {
                let vm = this;
                if ( ! vm.editorReady )
                    vm.initTinyMce(50);
            },
            listenEvents() {
                let vm = this;

                vm.$on('unsuccessfulcreate', function() {
                    vm.initTinyMce(50);
                });

                vm.$on('successfulcreate', function() {
                    this.clearDefaults();
                    vm.initTinyMce(50);
                });
            },
            clearDefaults() {
                this.selected_subscribers = [];
                this.selected_mailing_lists = [];
            },
            toggleSenderDetails() {
                let vm = this;
                vm.showSenderDetails = ! vm.showSenderDetails;
            },
            incrementTime() {
                let vm = this;

                setInterval(function() {
                    if ( moment(vm.selectedDate.time).isBefore(moment().format(vm.datePickerOptions.format)) )
                        vm.selectedDate = {time: moment().format(vm.datePickerOptions.format)};
                }, 1000);
            },
            updateSubmitBtnText() {
                let vm = this;
                let now = moment().format(vm.datePickerOptions.format);
                let schedule = moment(vm.selectedDate.time);
                let whenText = 'now';
                let output = {
                    nextWeek: '[on] dddd [at] h:mm A',
                    sameElse: '[on the] Do [of] MMM YYYY[,] [at] h:mm A'
                };

                if ( schedule.isAfter(now) && vm.sendOption === '1' )
                    whenText = schedule.calendar(null, output);

                if ( vm.sendOption === '1' )
                    vm.submitBtnText = 'Send ' + whenText;
            },
            hasBasicValidation() {
                let r = this.resource;
                return ( r.sender_name && r.sender_email && r.reply_to_email && (r.subscribers || r.mailing_lists) && r.subject && r.campaign && r.content );
            }
        },
        watch: {
            'selected_subscribers': function(newVal) {
                this.resource.subscribers = this.flattenedSubscribers;
            },
            'selected_mailing_lists': function(newVal) {
                this.resource.mailing_lists = this.flattenedMLists;
            },
            'resource': {
                handler: function(newVal) {
                    let vm = this;
                    vm.showToggleBtn = ( newVal.sender_name && newVal.sender_email && newVal.reply_to_email );
                },
                deep: true
            },
            'validationErrors': {
                handler: function(newVal) {
                    let vm = this;
                    vm.showToggleBtn = ! ( newVal.sender_name || newVal.sender_email || newVal.reply_to_email );
                    vm.showSenderDetails = ( newVal.sender_name || newVal.sender_email || newVal.reply_to_email );
                },
                deep: true
            },
            'sendOption': function(newVal) {
                let vm = this;
                if ( newVal === '0' ) {
                    vm.submitBtnText = 'Save as Draft';
                    vm.resource.is_draft = true;
                }
                else {
                    vm.updateSubmitBtnText();
                    vm.resource.is_draft = false;
                }
            },
            'selectedDate': {
                handler: function(newVal) {
                    let vm = this;
                    vm.resource.send_at = moment(vm.selectedDate.time, vm.datePickerOptions.format).utc().format(vm.datePickerOptions.format);
                    vm.updateSubmitBtnText();
                },
                deep: true
            },
        }
    }
</script>
