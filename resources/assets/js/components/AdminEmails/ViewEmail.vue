<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th scope="row">Subject</th>
                            <td>{{ resource.subject }}</td>
                        </tr>
                        <tr v-if="resource.sender">
                            <th scope="row">Sender</th>
                            <td>{{ resource.sender }}</td>
                        </tr>
                        <tr v-if="resource.sender">
                            <th scope="row">Reply-To</th>
                            <td>{{ resource.reply_to_email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">User</th>
                            <td>{{ resource.user.name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Campaign</th>
                            <td>{{ resource.campaign.name }}</td>
                        </tr>
                        <tr v-if="resource.recipients_num">
                            <th scope="row">Recipients</th>
                            <td>{{ resource.recipients_num }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>{{ resource.friendly_status }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Created</th>
                            <td>{{ resource.created_at | dateToTheMinute }}</td>
                        </tr>
                        <tr v-if="resource.sent_at">
                            <th scope="row">Sent</th>
                            <td>{{ resource.sent_at | dateToTheMinute }}</td>
                        </tr>
                        <tr v-else="">
                            <th scope="row">Last Update</th>
                            <td>{{ resource.updated_at | dateToTheMinute }}</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <a class="btn btn-md" v-bind:href="resource.url" target="_blank" title="Open" data-toggle="tooltip"><i class="fa fa-external-link"></i></a>
                                <a class="btn btn-md" v-bind:href="resource.pdf" target="_blank" title="Generate PDF" data-toggle="tooltip"><i class="fa fa-file-pdf-o"></i></a>
                            </th>
                            <td>
                                <iframe v-bind:src="resource.url" style="width: 100%; border:none;"  v-on:load="resizeIframe($event)"></iframe>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0,
                    status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '', user: {}, campaign: '', url: '', pdf: ''}
            }
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
            resizeIframe(event) {
                let iframe = event.target;
                if ( iframe )
                    iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
            },
        }
    }
</script>
