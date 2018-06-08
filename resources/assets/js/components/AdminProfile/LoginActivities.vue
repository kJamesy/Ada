<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="profile.login_activities.length" class="table-responsive">
                <table class="table table-bordered table-hover table-info">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Successful</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(activity, index) in orderedActivities">
                        <td>{{ activity.attempted_at | dateToTheMinWithDayOfWeek }} <template v-if=" ! index">(Current Session)</template> </td>
                        <td v-html="appActiveMarkup(activity.success)"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else="">
                You haven't logged in yet.
            </div>
        </template>
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
                profile: {},
                orderAttr: 'attempted_at',
                orderToggle: -1,
                howManyToShow: 25
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
            },
            activities() {
                let vm = this;
                if ( vm.profile.login_activities.length )
                    return _.take(_.orderBy(vm.profile.login_activities, ['attempted_at'], ['desc']), vm.howManyToShow);

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
        }
    }
</script>
