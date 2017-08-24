<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th scope="row">First Name</th>
                            <td>{{ resource.first_name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Last Name</th>
                            <td>{{ resource.last_name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ resource.email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Active</th>
                            <td>{{ resource.active ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Mailing Lists</th>
                            <td>{{ flattenedMLists }}</td>
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
                resource: {id: '', first_name: '', last_name: '', email: '', active: null, created_at: '', updated_at: '', mailing_lists: []}
            }
        },
        computed: {
            flattenedMLists() {
                return this.resource.mailing_lists.length
                    ? _.join(_.flatMapDeep(this.resource.mailing_lists, function(mList) {
                        return mList.name;
                    }), ', ')
                    : '-';
            }
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
        }
    }
</script>
