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
                                <th v-on:click.prevent="appChangeSort('name')">Name <span v-html="appGetSortMarkup('name')"></span></th>
                                <th v-on:click.prevent="appChangeSort('subscribers_count')">Subscribers <span v-html="appGetSortMarkup('subscribers_count')"></span></th>
                                <th v-on:click.prevent="appChangeSort('updated_at')" >Updated <span v-html="appGetSortMarkup('updated_at')"></span></th>
                                <th v-if="appUserHasPermission('read')"></th>
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
                                <td v-bind:title="resource.description" data-toggle="tooltip">{{ resource.name }}</td>
                                <td>
                                    <a v-bind:class="! parseInt(resource.subscribers_count) ? 'disabled' : ''" v-bind:href="getSubscribersInMListLink(resource.id)" class="btn btn-link">
                                        {{ resource.subscribers_count }}
                                    </a>
                                </td>
                                <td><span v-bind:title="resource.updated_at | dateToTheMinWithDayOfWeek" data-toggle="tooltip">{{ resource.updated_at | dateToTheDay }}</span></td>
                                <td v-if="appUserHasPermission('read')">
                                    <router-link v-bind:to="{ name: 'admin_mailing_lists.view', params: { id: resource.id }}" class="btn btn-sm rounded-circle btn-pink"><i class="icon ion-eye"></i></router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <pagination :pagination="appPagination" :callback="fetchResources" :options="appPaginationOptions" class="mt-5 mb-3"></pagination>

            </div>
            <div v-if="! appUserHasPermission('read')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </div>
        <div v-if="! fetchingData && ! appResourceCount" class="mt-5">
            No items found
        </div>
        <div class="mt-3 mb-3 font-italic text-right" v-if="! fetchingData && appDeletedNum">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.trash'}" class="btn btn-link"><i class="icon ion-trash-a"></i> Deleted Items ({{ appDeletedNum }})</router-link>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.appInitialiseSettings();
                this.appInitialiseTooltip();
                this.fetchResources();
            });
        },
        data() {
            return {
                fetchingData: true,
                quickEditOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Export', value: 'export' },
                    { text: 'Delete', value: 'delete' }
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
            getSubscribersInMListLink(mList) {
                return this.appAdminHome + '/subscribers/' + mList + '/in-mailing-list';
            },
        },
    }
</script>
