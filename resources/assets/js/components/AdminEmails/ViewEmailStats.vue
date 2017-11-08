<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('read')">
                <h3>{{ resource.subject }}</h3>
                <h5 v-if="statType === 'general'">Dispatched to {{ resource.injections_count }}</h5>
                <div class="mt-5 mb-3">
                    <label for="statType"></label><select id="statType" v-model="statType">
                    <option v-for="option in statTypes" v-bind:value="option.value">
                        {{ option.text }}
                    </option></select>
                    <a href="" v-on:click.prevent="refetchData()" class="pull-right"><i class="fa fa-refresh" v-bind:class="{ 'fa-spin': refreshing }"></i></a>
                </div>

                <template v-if="statType === 'general'">
                    <div class="row mt-5">
                        <div class="col-sm-6">
                            <general-pie-chart :chart-data="getGeneralPieData('deliveries')" :options="getGeneralPieOptions('deliveries')"></general-pie-chart>
                        </div>
                        <div class="col-sm-6">
                            <general-pie-chart :chart-data="getGeneralPieData('opens')" :options="getGeneralPieOptions('opens')"></general-pie-chart>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-sm-6">
                            <general-pie-chart :chart-data="getGeneralPieData('clicks')" :options="getGeneralPieOptions('clicks')"></general-pie-chart>
                        </div>
                        <div class="col-sm-6">
                            <general-pie-chart :chart-data="getGeneralPieData('failures')" :options="getGeneralPieOptions('failures')"></general-pie-chart>
                        </div>
                    </div>
                </template>
                <template v-if="statType === 'opens'">
                    <div class="row mt-5">
                        <div class="col">
                            <general-pie-chart :chart-data="getOpensPieData('countries')" :options="getOpensPieOptions('countries')"></general-pie-chart>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <general-pie-chart :chart-data="getOpensPieData('devices')" :options="getOpensPieOptions('devices')"></general-pie-chart>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <general-pie-chart :chart-data="getOpensPieData('OSs')" :options="getOpensPieOptions('OSs')"></general-pie-chart>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <general-pie-chart :chart-data="getOpensPieData('browsers')" :options="getOpensPieOptions('browsers')"></general-pie-chart>
                        </div>
                    </div>
                </template>
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

                switch(this.$route.name) {
                    case 'admin_emails.stats':
                        this.statType = 'general';
                        break;
                    case 'admin_emails.open_stats':
                        this.statType = 'opens';
                        break;
                }
                this.showResource();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {id: '', sender: '', reply_to_email: '', subject: '', content: '', recipients_num: 0, status: '', friendly_status: '', created_at: '', updated_at: '', sent_at: '',
                    injections_count: 0, deliveries_count: 0, opens_count: 0, clicks_count: 0, failures_count: 0,
                    country_stats: [], device_stats: [], os_stats: [], browser_stats: []},
                refreshing: false,
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