<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-info">
                        <tbody>
                            <tr>
                                <th scope="row">Name</th>
                                <td>{{ resource.name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Description</th>
                                <td>{{ resource.description }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Created</th>
                                <td>{{ resource.created_at | dateToTheMinute }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Last Update</th>
                                <td>{{ resource.updated_at | dateToTheMinute }}</td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <a class="btn btn-md btn-link" v-bind:href="resource.url" target="_blank" title="Open" data-toggle="tooltip"><i class="icon ion-android-open"></i></a>
                                    <a class="btn btn-md btn-link" v-bind:href="resource.pdf" target="_blank" title="Generate PDF" data-toggle="tooltip"><i class="icon ion-ios-cloud-download-outline"></i></a>
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
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
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
                resource: {id: '', name: '', description: '', created_at: '', updated_at: '', url: '', pdf: ''}
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
