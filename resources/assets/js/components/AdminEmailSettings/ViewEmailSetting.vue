<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                <th scope="row">Setting Value</th>
                                <td>{{ resource.setting_value }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Created</th>
                                <td>{{ resource.created_at | dateToTheMinute }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Last Update</th>
                                <td>{{ resource.updated_at | dateToTheMinute }}</td>
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', name: '', description: '', setting_value: '', created_at: '', updated_at: ''}
            }
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
        }
    }
</script>
