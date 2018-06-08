<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermissionOnUser('read', resource)" class="table-responsive">
                <table class="table table-bordered table-hover table-info">
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
                            <th scope="row">Username</th>
                            <td>{{ resource.username }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Active (Can Login)</th>
                            <td>{{ resource.active ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Role</th>
                            <td>
                                {{ resource.is_super_admin ? 'Super Admin' : 'User' }}
                                <span v-if="resource.is_super_admin" class="text-warning"> <i class="icon ion-android-star"></i> </span>
                            </td>
                        </tr>
                        <tr v-if="permissions">
                            <th scope="row">Permissions</th>
                            <td>{{ permissions }}</td>
                        </tr>
                        <tr>
                            <th scope="row">User Since</th>
                            <td>{{ resource.created_at | dateToTheMinute }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Last Login</th>
                            <td v-html="getLastLoginDateHtml(resource.last_login)"></td>
                        </tr>
                        <tr>
                            <th scope="row">Last Profile Update</th>
                            <td>{{ resource.updated_at | dateToTheMinute }}</td>
                        </tr>
                    </tbody>
                </table>
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', first_name: '', last_name: '', phone: '', email: '', username: '', active: null, created_at: '', updated_at: '',
                    is_super_admin: null, meta: {}, last_login: '', penultimate_login: ''},
                listRoute: 'admin_users.index',
            }
        },
        computed: {
            permissions() {
                let vm = this;
                let permissionsMarkup = '';

                if ( vm.resource.meta ) {
                    let meta = JSON.parse(vm.resource.meta);

                    _.forEach(meta, function(value, key) {
                        if ( value === true )
                            permissionsMarkup += _.upperFirst(_.replace(key, /_/g, ' ')) + '; ';
                    });
                }

                return permissionsMarkup;
            }
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
            getLastLoginDateHtml(last_login) {
                return last_login ? this.$options.filters.dateToTheMinute(last_login.attempted_at) : '-';
            },
            getLastLoginLongDateHtml(last_login) {
                return last_login ? this.$options.filters.dateToTheMinWithDayOfWeek(last_login.attempted_at) : '-';
            },
        },
    }
</script>
