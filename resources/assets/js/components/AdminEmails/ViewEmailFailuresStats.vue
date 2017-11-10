<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <h3>{{ resource.subject }}</h3>

                <div class="row mt-5">
                    <div class="col">
                        <form class="form-inline pull-left">
                            <label class="form-control-label mr-sm-2" for="statType">Viewing</label>
                            <select class="custom-select form-control mb-2 mb-sm-0" v-model="statType" id="statType">
                                <option v-for="option in statTypes" v-bind:value="option.value">
                                    {{ option.text }}
                                </option>
                            </select>
                        </form>
                        <a href="" v-on:click.prevent="refetchData()" class="pull-right"><i class="fa fa-refresh" v-bind:class="{ 'fa-spin': refreshing }"></i></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <h5 class="mt-4">Total Failures: {{ resource.failures_count }}</h5>

                <div class="row mt-5">
                    <div class="col">
                        <general-pie-chart :chart-data="getPieData()" :options="getPieOptions()"></general-pie-chart>
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
    import GeneralPieChart from './Stats/GeneralPieChart';

    export default {
        mounted() {
            this.$nextTick(function() {
                this.appInitialiseTooltip();
                this.showResource();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: { id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0, status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '',
                    injections_count: 0, deliveries_count: 0, opens_count: 0, clicks_count: 0, failures_count: 0,
                    failures_stats: [] },
                refreshing: false,
                statType: 'failures',
            }
        },
        methods: {
            showResource() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/' + vm.statType + '-stats').then(function (response) {
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
            getPieOptions() {
                let num = this.resource.failures_count;
                let percentage = _.round( (num/this.resource.injections_count)*100, 2 );

                return {
                    responsive: true,
                    maintainAspectRatio: true,
                    title: {
                        display: true,
                        text: 'Failures - ' + num + ' (' + percentage + '%)'
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                };
            },
            getPieData() {
                let vm = this;
                let failures_stats = vm.resource.failures_stats;

                let labels = [];
                let backgroundColor = [];
                let data = [];

                if ( failures_stats.length ) {
                    let palette = vm.getPalette(failures_stats.length);

                    _.forEach(failures_stats, function (fStat, key) {
                        labels.push(fStat.type + ' - ' + fStat.types_count);
                        data.push(fStat.types_count);
                        if ( ! vm.refreshing )
                            backgroundColor.push(vm.getColor(key, palette));
                    });
                }

                return  {
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
            },
        },
        components: {
            GeneralPieChart
        }
    }
</script>