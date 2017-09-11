<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <a class="pull-right btn btn-link btn-sm" v-show="showToggleBtn" v-on:click.prevent="toggleSenderDetails">Toggle Sender Details</a>
                    <div class="clearfix"></div>
                    <div v-show="showSenderDetails">
                        <div class="form-group ">
                            <label class="form-control-label" for="sender_name">Sender Name <small class="text-danger">{{ validationErrors.sender_name }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="sender_name" v-model.trim="resource.sender_name">
                            </div>
                        </div>
                        <div class="form-group ">
                            <label class="form-control-label" for="sender_email">Sender Email <small class="text-danger">{{ validationErrors.sender_email }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="sender_email" v-model.trim="resource.sender_email">
                            </div>
                        </div>
                        <div class="form-group ">
                            <label class="form-control-label" for="reply_to_email">Reply-To Email <small class="text-danger">{{ validationErrors.reply_to_email }}</small></label>
                            <div class="">
                                <input type="text" class="form-control" id="reply_to_email" v-model.trim="resource.reply_to_email">
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
                    <div class="form-group ">
                        <label class="form-control-label" for="subject">Subject <small class="text-danger">{{ validationErrors.subject }}</small></label>
                        <div class="">
                            <input type="text" class="form-control" id="subject" v-model.trim="resource.subject">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Campaign <small class="text-danger">{{ validationErrors.campaign }}</small></label>
                        <div class="">
                            <select class="custom-select form-control" v-model="resource.campaign">
                                <option v-for="option in sortedCampaigns" v-bind:value="option.id">
                                    {{ option.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="content">Content <small class="text-danger">{{ validationErrors.content }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="content" v-model.trim="resource.content" rows="4" v-on:click="checkEditor">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="ml-md-auto">
                            <button type="submit" class="btn btn-primary btn-outline-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="! appUserHasPermission('create')">
                <i class="fa fa-warning"></i> {{ appUnauthorisedErrorMessage }}
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {sender_name: '', sender_email: '', reply_to_email: '', subscribers: [], mailing_lists: [], subject: '', campaign: '', content: ''},
                validationErrors: {sender_name: '', sender_email: '', reply_to_email: '', subscribers: '', mailing_lists: '', subject: '', campaign: '', content: ''},
                subscribers: [],
                selected_subscribers: [],
                mailing_lists: [],
                selected_mailing_lists: [],
                campaigns: [],
                templates: [],
                editorReady: false,
                showSenderDetails: true,
                showToggleBtn: true
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
                this.appCreateResource();
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
                    vm.showToggleBtn = ( vm.resource.sender_name && vm.resource.sender_email && vm.resource.reply_to_email );
                },
                deep: true
            }
        }
    }
</script>
