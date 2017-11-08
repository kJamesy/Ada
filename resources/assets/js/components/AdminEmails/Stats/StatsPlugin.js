'use strict';

const StatsPlugin = {
    install(Vue, options) {

        Vue.mixin({
            mounted() {
                this.$nextTick(function() {

                });
            },
            data() {
                return {
                    statTypes: [
                        { text: 'General Stats', value: 'general' },
                        { text: 'Opens Stats', value: 'opens' },
                        { text: 'Clicks Stats', value: 'clicks' },
                        { text: 'Failures Stats', value: 'failures' },
                    ],
                    statType: 'general',
                    routeNames: { general: 'admin_emails.stats', opens: 'admin_emails.open_stats', clicks: 'admin_emails.click_stats' }
                }
            },
            computed: {
                countriesCount() {
                    let vm = this;
                    return vm.doCount(vm.resource.country_stats, 'country_count');
                },
                devicesCount() {
                    let vm = this;
                    return vm.doCount(vm.resource.device_stats, 'device_count');
                },
                OSsCount() {
                    let vm = this;
                    return vm.doCount(vm.resource.os_stats, 'OS_count');
                },
                browsersCount() {
                    let vm = this;
                    return vm.doCount(vm.resource.browser_stats, 'browser_count');
                }
            },
            methods: {
                doCount(countObj, prop) {
                    let total = 0;

                    if ( typeof countObj === 'object' ) {
                        _.forEach(countObj, function(count) {
                            total += parseInt(count[prop]);
                        });
                    }

                    return total;
                },
                getGeneralPieOptions(stat) {
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
                getOpensPieOptions(stat) {
                    let friendlyName = ( stat === 'OSs' ) ? 'Operating systems' : stat;

                    return {
                        responsive: true,
                        maintainAspectRatio: true,
                        title: {
                            display: true,
                            text:  _.capitalize(friendlyName) + ' - ' + this[stat + 'Count']
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    };
                },
                getGeneralPieData(stat) {
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
                getOpensPieData(stat) {
                    let vm = this;
                    let country_stats = vm.resource.country_stats;
                    let device_stats = vm.resource.device_stats;
                    let os_stats = vm.resource.os_stats;
                    let browser_stats = vm.resource.browser_stats;

                    let labels = [];
                    let backgroundColor = [];
                    let data = [];

                    switch ( stat ) {
                        case 'countries':
                            _.forEach(country_stats, function(cStat) {
                                labels.push(cStat.country_name + ' - ' + cStat.country_count);
                                data.push(cStat.country_count);
                                if ( ! vm.refreshing )
                                    backgroundColor.push('#'+Math.floor(Math.random()*16777215).toString(16));
                            });
                            break;
                        case 'devices':
                            _.forEach(device_stats, function(dStat) {
                                labels.push(dStat.device_name + ' - ' + dStat.device_count);
                                data.push(dStat.device_count);
                                if ( ! vm.refreshing )
                                    backgroundColor.push('#'+Math.floor(Math.random()*16777215).toString(16));
                            });
                            break;
                        case 'OSs':
                            _.forEach(os_stats, function(oStat) {
                                labels.push(oStat.OS_name + ' - ' + oStat.OS_count);
                                data.push(oStat.OS_count);
                                if ( ! vm.refreshing )
                                    backgroundColor.push('#'+Math.floor(Math.random()*16777215).toString(16));
                            });
                            break;
                        case 'browsers':
                            _.forEach(browser_stats, function(bStat) {
                                labels.push(bStat.browser_name + ' - ' + bStat.browser_count);
                                data.push(bStat.browser_count);
                                if ( ! vm.refreshing )
                                    backgroundColor.push('#'+Math.floor(Math.random()*16777215).toString(16));
                            });
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
            },
            watch: {
                statType(newVal, oldVal) {
                    let vm = this;
                    if ( newVal !== oldVal ) {
                        if ( typeof vm.routeNames[newVal] === 'string' )
                            vm.$router.push({name: vm.routeNames[newVal]});
                    }
                }
            }
        });
    }
};

export default StatsPlugin;