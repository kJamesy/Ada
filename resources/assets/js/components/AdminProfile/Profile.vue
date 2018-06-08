<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>
        <div class="table-responsive" v-if="! fetchingData">
            <table class="table table-bordered table-hover table-info">
                <tbody>
                <tr>
                    <th scope="row">First Name</th>
                    <td>{{ profile.first_name }}</td>
                </tr>
                <tr>
                    <th scope="row">Last Name</th>
                    <td>{{ profile.last_name }}</td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>{{ profile.email }}</td>
                </tr>
                <tr>
                    <th scope="row">Username</th>
                    <td>{{ profile.username }}</td>
                </tr>
                <tr>
                    <th scope="row">Role</th>
                    <td>
                        {{ profile.is_super_admin ? 'Super Admin' : 'User' }}
                        <span v-if="profile.is_super_admin" class="text-dark"> <i class="icon ion-android-star"></i> </span>
                    </td>
                </tr>
                <tr v-if="permissions">
                    <th scope="row">Permissions</th>
                    <td>{{ permissions }}</td>
                </tr>
                <tr>
                    <th scope="row">User Since</th>
                    <td>{{ profile.created_at | dateToTheMinute }}</td>
                </tr>
                <tr>
                    <th scope="row">Last Login</th>
                    <td v-html="getLastLoginDateHtml(profile.penultimate_login)"></td>
                </tr>
                <tr>
                    <th scope="row">Last Profile Update</th>
                    <td>{{ profile.updated_at | dateToTheMinute }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.$Progress.start();
                this.fetchProfile();
            });
        },
        data() {
            return {
                fetchingData: true,
                profile: {}
            }
        },
        computed: {
            permissions() {
                let vm = this;
                let permissionsMarkup = '';

                if ( vm.profile.meta ) {
                    let meta = JSON.parse(vm.profile.meta);

                    _.forEach(meta, function(value, key) {
                        if ( value === true )
                            permissionsMarkup += _.upperFirst(_.replace(key, /_/g, ' ')) + '; ';
                    });
                }

                return permissionsMarkup;
            }
        },
        methods: {
            fetchProfile() {
                let vm = this;
                let progress = vm.$Progress;

                vm.$http.get(vm.appResourceUrl + '/show').then(function(response) {
                    if ( response.data && response.data.profile )
                        vm.profile = response.data.profile;
                    vm.fetchingData = false;
                    progress.finish();
                }, function(error) {
                    vm.appGeneralErrorAlert();
                    progress.fail();
                });
            },
            getLastLoginDateHtml(last_login) {
                return last_login ? this.$options.filters.dateToTheMinute(last_login.attempted_at) : '-';
            },
        }
    }
</script>
