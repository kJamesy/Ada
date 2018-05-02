<template>
    <div class="mt-3">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>
        <div v-if="! fetchingData && appResourceCount">
            <div v-if="appUserHasPermission('read')">
                <a href="#" v-on:click.prevent="exportAll" class="btn btn-link pull-right" title="Export All" data-toggle="tooltip"><i class="icon ion-android-download"></i></a>
                <div class="clearfix mb-2"></div>
                <form v-on:submit.prevent="appDoSearch">
                    <div class="form-group">
                        <input type="text" v-model.trim="appSearchText" placeholder="Search" class="form-control" />
                    </div>
                </form>
                <div class="mt-4 mb-4">
                    <form class="form-inline pull-left" v-if="appSelectedResources.length">
                        <label class="form-control-label mr-sm-2" for="quick-edit">Options</label>
                        <select class="custom-select form-control mb-2 mb-sm-0 mr-sm-5" v-model="appQuickEditOption" id="quick-edit">
                            <option v-for="option in quickEditOptions" v-bind:value="option.value" v-if="appUserHasPermission(option.value)">
                                {{ option.text }}
                            </option>
                        </select>
                    </form>
                    <form class="form-inline pull-right">
                        <span class="mr-3">Page {{ appPagination.current_page }} of {{ appPagination.last_page }} [<b>{{ appPagination.total }} items</b>]</span>
                        <label class="form-control-label mr-sm-2" for="records_per_page">Per Page</label>
                        <select class="custom-select form-control mb-2 mb-sm-0" v-model="appPerPage" id="records_per_page">
                            <option v-for="option in appPerPageOptions" v-bind:value="option.value">
                                {{ option.text }}
                            </option>
                        </select>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-info">
                        <thead>
                            <tr class="pointer-cursor">
                                <th class="normal-cursor checkbox-th" v-if="appUserHasPermission('update')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" v-model="appSelectAll">
                                        <label class="custom-control-label" for="selectAllCheckbox"></label>
                                    </div>
                                </th>
                                <th v-on:click.prevent="appChangeSort('subject')">Subject <span v-html="appGetSortMarkup('subject')"></span></th>
                                <th v-on:click.prevent="appChangeSort('sender')">Sender <span v-html="appGetSortMarkup('sender')"></span></th>
                                <th v-on:click.prevent="appChangeSort('recipients_num')">Recipients <span v-html="appGetSortMarkup('recipients_num')"></span></th>
                                <th v-on:click.prevent="appChangeSort('status')">Status <span v-html="appGetSortMarkup('status')"></span></th>
                                <th v-on:click.prevent="appChangeSort('created_at')" >Created <span v-html="appGetSortMarkup('created_at')"></span></th>
                                <th v-on:click.prevent="appChangeSort('updated_at')" >Deleted <span v-html="appGetSortMarkup('updated_at')"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="resource in orderedAppResources">
                                <td v-if="appUserHasPermission('update')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" v-bind:id="'select_' + resource.id" v-model="appSelectedResources" v-bind:value="resource.id">
                                        <label class="custom-control-label" v-bind:for="'select_' + resource.id"></label>
                                    </div>
                                </td>
                                <td>{{ resource.subject }}</td>
                                <td>
                                    <span v-if="resource.sender">{{ resource.sender }}</span>
                                    <span v-else=""><em>&mdash;</em></span>
                                </td>
                                <td>
                                    <span v-if="resource.recipients_num">{{ resource.recipients_num }}</span>
                                    <span v-else=""><em>&mdash;</em></span>
                                </td>
                                <td v-bind:title="resource.friendly_status" data-toggle="tooltip">
                                    <i v-if="resource.status === -2" class="icon ion-edit"></i>
                                    <i v-if="resource.status === -1" class="icon ion-android-alarm-clock"></i>
                                    <i v-if="resource.status === 0" class="icon ion-alert-circled text-danger"></i>
                                    <i v-if="resource.status === 1" class="icon ion-ios-checkmark-outline"></i>
                                </td>
                                <td><span v-bind:title="resource.created_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.created_at | dateToTheDay }}</span></td>
                                <td><span v-bind:title="resource.updated_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.updated_at | dateToTheDay }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <pagination :pagination="appPagination" :callback="fetchResources" :options="appPaginationOptions" class="mt-5 mb-5"></pagination>
            </div>
            <div v-if="! appUserHasPermission('read')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </div>
        <div v-if="! fetchingData && ! appResourceCount" class="mt-5">
            No items found
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.appInitialiseSettings();
                this.trash = 1;
                this.appInitialiseTooltip();
                this.fetchResources();
            });
        },
        data() {
            return {
                fetchingData: true,
                quickEditOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Restore', value: 'restore' },
                    { text: 'Export', value: 'export' },
                    { text: 'Destroy', value: 'destroy' }
                ],
                quickEditOption: '',
            }
        },
        methods: {
            fetchResources(orderAttr, orderToggle) {
                this.appFetchResources(this, orderAttr, orderToggle);
            },
            quickEditResources() {
                this.appQuickEditResources();
            },
            exportAll() {
                this.appExportAll();
            },
        },
    }
</script>
