<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <h3>{{ resource.subject }}</h3>
                <h5>Dispatched to {{ resource.injections_count }}</h5>

                <a href="" v-on:click.prevent="refetchData()" class="pull-right"><i class="fa fa-refresh" v-bind:class="{ 'fa-spin': refreshing }"></i></a>
                <div class="row" style="margin-top: 50px;">
                    <div class="col-sm-6">
                        <general-stats :chart-data="getPieData('deliveries')" :options="getPieOptions('deliveries')"></general-stats>
                    </div>
                    <div class="col-sm-6">
                        <general-stats :chart-data="getPieData('opens')" :options="getPieOptions('opens')"></general-stats>
                    </div>
                </div>
                <div class="row" style="margin-top: 50px;">
                    <div class="col-sm-6">
                        <general-stats :chart-data="getPieData('clicks')" :options="getPieOptions('clicks')"></general-stats>
                    </div>
                    <div class="col-sm-6">
                        <general-stats :chart-data="getPieData('failures')" :options="getPieOptions('failures')"></general-stats>
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
    import GeneralStats from './Stats/GeneralStats';

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
                resource: {id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0, status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '',
                    injections_count: 0, deliveries_count: 0, opens_count: 0, clicks_count: 0, failures_count: 0},
                refreshing: false
            }
        },
        methods: {
            showResource() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/general-stats').then(function (response) {
                    if (response.data && response.data.resource) {
                        _.forEach(vm.resource, function (val, idx) {
                            if ( response.data.resource.hasOwnProperty(idx) )
                                vm.$set(vm.resource, idx, response.data.resource[idx]);
                        });

                        vm.rootEventsHub.$emit('show-edit-tab', { resource: vm.resource });
                        progress.finish();
                    }
                    else {
                        vm.appGeneralErrorAlert();
                        progress.fail();
                    }

                    vm.fetchingData = false;
                    vm.refreshing = false;
                }, function (error) {
                    if (error.status && error.status === 403 && error.data) {
                        swal({
                            title: "Uh oh!",
                            text: error.data.error,
                            type: 'error',
                            animation: 'slide-from-top'
                        }, function () {
                            window.location.replace(vm.appUserHome);
                        });
                    }
                    else if (error.status && error.status === 404 && error.data)
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();
                    progress.fail();
                    vm.fetchingData = false;
                    vm.refreshing = false;
                });
            },
            getPieOptions(stat) {
                let num = this.resource[stat + '_count'];
                let percentage = _.round( (num/this.resource.injections_count)*100, 2 );

                return {
                    responsive: true,
                    maintainAspectRatio: true,
                    title: {
                        display: true,
                        text:  _.capitalize(stat) + ' - ' + num + ' (' + percentage + '%)'
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                };
            },
            getPieData(stat) {
                let vm = this;
                let injections = vm.resource.injections_count;
                let deliveries = vm.resource.deliveries_count;
                let opens = vm.resource.opens_count;
                let clicks = vm.resource.clicks_count;
                let failures = vm.resource.failures_count;

                let labels = [];
                let backgroundColor = [];
                let data = [];

                switch ( _.lowerCase(stat) ) {
                    case 'deliveries':
                        labels = ['Delivered', 'Not Delivered'];
                        backgroundColor = ['#FF6384', '#DDD'];
                        data = [deliveries, _.subtract(injections, deliveries)];
                        break;
                    case 'opens':
                        labels = ['Opened', 'Not Opened'];
                        backgroundColor = ['#EBF90B', '#FAEAB8'];
                        data = [opens, _.subtract(injections, opens)];
                        break;
                    case 'clicks':
                        labels = ['Clicked', 'Not Clicked'];
                        backgroundColor = ['#FF9124', '#F6D8D8'];
                        data = [clicks, _.subtract(injections, clicks)];
                        break;
                    case 'failures':
                        labels = ['Failed', 'Successful'];
                        backgroundColor = ['#C0E5F6', '#059BFF'];
                        data = [failures, _.subtract(injections, failures)];
                        break;
                }

                return {
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: backgroundColor,
                            data: data
                        }
                    ]
                };
            },
            refetchData() {
                this.refreshing = true;
                this.showResource();
            }
        },
        components: {
            GeneralStats
        }
    }
</script>