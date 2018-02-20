<template>
    <div>
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>
        <div v-if="! fetchingData">
            <div class="row" v-if="lastEmail.injections_count">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Last Sent Email Delivery Stats</h4>
                            <p class="card-text">
                                <strong>Subject:</strong> {{ lastEmail.subject }} <br />
                                <strong>Sender:</strong> {{ lastEmail.sender }} <br />
                                <strong>Sent:</strong> <span v-bind:title="lastEmail.sent_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ lastEmail.sent_at | dateToTheDay }}</span> <br />
                                <strong>Recipients:</strong> {{ lastEmail.injections_count }}
                            </p>
                            <general-pie-chart :chart-data="getGeneralPieData('deliveries')" :options="getGeneralPieOptions('deliveries')"></general-pie-chart>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Last Sent Email Failure Stats</h4>
                            <p class="card-text">
                                <strong>Subject:</strong> {{ lastEmail.subject }} <br />
                                <strong>Sender:</strong> {{ lastEmail.sender }} <br />
                                <strong>Sent:</strong> <span v-bind:title="lastEmail.sent_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ lastEmail.sent_at | dateToTheDay }}</span> <br />
                                <strong>Recipients:</strong> {{ lastEmail.injections_count }}
                            </p>
                            <general-pie-chart :chart-data="getGeneralPieData('failures')" :options="getGeneralPieOptions('failures')"></general-pie-chart>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-6" v-if="nextScheduledEmail.sent_at && nextScheduledEmail.recipients_num">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Next Scheduled Email</h4>
                            <p class="card-text">
                                <strong>Subject:</strong> {{ nextScheduledEmail.subject }} <br />
                                <strong>Sender:</strong> {{ nextScheduledEmail.sender }} <br />
                                <strong>Scheduled For:</strong> <span v-bind:title="nextScheduledEmail.sent_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ nextScheduledEmail.sent_at | dateToTheDay }}</span> <br />
                                <strong>Recipients:</strong> {{ nextScheduledEmail.recipients_num }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Subscribers</h4>
                            <p class="card-text">
                                <strong>New Today:</strong> {{ todaysSubscribers.length }} <br />
                                <strong>Total:</strong> {{ subscribersCount }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import GeneralPieChart from '../AdminEmails/Stats/GeneralPieChart';
    import googlePalette from 'google-palette';

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
                lastEmail: {},
                nextScheduledEmail: {},
                todaysSubscribers: [],
                subscribersCount: 0
            }
        },
        computed: {
            showStats() {
                return this.lastEmail.injections_count || this.nextScheduledEmail.subject;
            }
        },
        methods: {
            fetchProfile() {
                let vm = this;
                let progress = vm.$Progress;

                vm.$http.get(vm.appResourceUrl + '/show').then(function(response) {

                    if ( response.data ) {
                        if ( response.data.profile )
                            vm.profile = response.data.profile;
                        if ( response.data.lastEmail )
                            vm.lastEmail = response.data.lastEmail;
                        if ( response.data.nextScheduledEmail )
                            vm.nextScheduledEmail = response.data.nextScheduledEmail;
                        if ( response.data.todaysSubscribers )
                            vm.todaysSubscribers = response.data.todaysSubscribers;
                        if ( response.data.subscribersCount )
                            vm.subscribersCount = response.data.subscribersCount;
                    }

                    vm.appInitialiseTooltip();
                    vm.fetchingData = false;
                    progress.finish();
                }, function(error) {
                    vm.appGeneralErrorAlert();
                    progress.fail();
                });
            },
            getGeneralPieOptions(stat) {
                let num = this.lastEmail[stat + '_count'];
                let percentage = _.round( (num/this.lastEmail.injections_count)*100, 2 );

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
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                return data.labels[tooltipItem.index];
                            }
                        }
                    }
                };
            },
            getGeneralPieData(stat) {
                let vm = this;
                let injections = vm.lastEmail.injections_count;
                let deliveries = vm.lastEmail.deliveries_count;
                let opens = vm.lastEmail.opens_count;
                let clicks = vm.lastEmail.clicks_count;
                let failures = vm.lastEmail.failures_count;

                let palette = [];

                let labels = [];
                let backgroundColor = [];
                let data = [];

                switch ( _.lowerCase(stat) ) {
                    case 'deliveries':
                        palette = vm.getPalette(2);

                        labels = ['Delivered: ' + deliveries, 'Not Delivered: ' + _.subtract(injections, deliveries)];
                        backgroundColor = [vm.getColor(0, palette), vm.getColor(1, palette)];
                        data = [deliveries, _.subtract(injections, deliveries)];
                        break;
                    case 'opens':
                        palette = vm.getPalette(2);

                        labels = ['Opened: ' + opens, 'Not Opened: ' + _.subtract(injections, opens)];
                        backgroundColor = [vm.getColor(0, palette), vm.getColor(1, palette)];
                        data = [opens, _.subtract(injections, opens)];
                        break;
                    case 'clicks':
                        palette = vm.getPalette(2);

                        labels = ['Clicked: ' + clicks, 'Not Clicked: ' + _.subtract(injections, clicks)];
                        backgroundColor = [vm.getColor(0, palette), vm.getColor(1, palette)];
                        data = [clicks, _.subtract(injections, clicks)];
                        break;
                    case 'failures':
                        palette = vm.getPalette(2);

                        labels = ['Failed: ' + failures, 'Successful: ' + _.subtract(injections, failures)];
                        backgroundColor = [vm.getColor(0, palette), vm.getColor(1, palette)];
                        data = [failures, _.subtract(injections, failures)];
                        break;
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
            getColor(key, palette) {
                if ( palette && palette.length && palette[key] !== 'undefined' )
                    return '#' + palette[key];
                else
                    return '#' + Math.floor(Math.random()*16777215).toString(16);
            },
            getPalette(num) {
                let colorBrewers = ['cb-PuRd', 'cb-YlOrRd', 'cb-RdYlBu', 'cb-RdYlGn', 'cb-Spectral', 'cb-RdBu'];
                let tols = ['tol-dv', 'tol-rainbow'];
                let palette = _.reverse(googlePalette(tols[_.random(0, tols.length -1)], num));

                if ( num <= 9 )
                    palette = _.reverse(googlePalette(colorBrewers[_.random(0, colorBrewers.length - 1)], num));

                return palette;
            },
        },
        components: {
            GeneralPieChart
        }
    }
</script>
