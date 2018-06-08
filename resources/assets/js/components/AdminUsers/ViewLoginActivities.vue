<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermissionOnUser('read', resource)">
                <div v-if="resource.login_activities.length" class="table-responsive">
                    <table class="table table-bordered table-hover table-info">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Successful</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr v-for="activity in orderedActivities">
                            <td>{{ activity.attempted_at | dateToTheMinWithDayOfWeek }}</td>
                            <td v-html="appActiveMarkup(activity.success)"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else="">
                    This user hasn't logged in yet.
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', first_name: '', last_name: '', email: '', username: '', active: null, created_at: '', updated_at: '', is_super_admin: null, meta: {}, login_activities: [], last_login: '', penultimate_login: ''},
                resources: [],
                listRoute: 'admin_users.index',
                orderAttr: 'attempted_at',
                orderToggle: -1,
                howManyToShow: 25
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
            },
            activities() {
                let vm = this;
                if ( vm.resource.login_activities.length )
                    return _.take(_.orderBy(vm.resource.login_activities, ['attempted_at'], ['desc']), vm.howManyToShow);

                return null;
            },
            orderedActivities() {
                let vm = this;
                if ( vm.activities.length )
                    return _.orderBy(vm.activities, [vm.orderAttr, 'attempted_at'], [vm.orderToggle === 1 ? 'asc' : 'desc', 'desc']);

                return null;
            },
        },
        methods: {
            showResource() {
                this.appShowResource();
            },
        },
    }
</script>
