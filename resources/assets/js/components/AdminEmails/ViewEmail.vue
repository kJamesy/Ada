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
                    <div class="col-sm-9">{{ resource.recipients_num }}</div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-12">
                        <iframe v-bind:src="resource.url" style="width: 100%; border:none;"  v-on:load="resizeIframe($event)"></iframe>
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
                    { text: 'Read Confirmed', value: 'opens' },
                    { text: 'Clicked', value: 'clicks' },
                    { text: 'Failed', value: 'failures' },
                ]
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
        }
    }
</script>
