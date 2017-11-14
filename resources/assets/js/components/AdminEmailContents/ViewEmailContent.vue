<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th scope="row">Title</th>
                            <td>{{ resource.title }}</td>
                        </tr>
                        <tr>
                            <th scope="row">URL Slug</th>
                            <td>{{ resource.slug }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Last Modified By</th>
                            <td>{{ resource.user.name }}</td>
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
                                <a class="btn btn-md" v-bind:href="resource.url" target="_blank" title="Open" data-toggle="tooltip"><i class="fa fa-external-link"></i></a>
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
                resource: {id: '', title: '', slug: '', content: '', created_at: '', updated_at: '', url: '', user: {}}
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
