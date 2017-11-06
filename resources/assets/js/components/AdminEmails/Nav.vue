<template>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_emails.index' }" tag="a" class="nav-link" exact><i class="fa fa-home"></i>
                All Emails</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && draftsNum">
            <router-link v-bind:to="{ name: 'admin_emails.drafts' }" tag="a" class="nav-link" exact><i class="fa fa-spinner"></i>
                Drafts ({{ draftsNum }})</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appBelongingToUser">
            <router-link v-bind:to="{ name: 'admin_emails.user', params: { userId: appBelongingToUser } }" tag="a" class="nav-link" exact><i class="fa fa-user"></i>
                Emails by User</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appBelongingToCampaign">
            <router-link v-bind:to="{ name: 'admin_emails.campaign', params: { campaignId: appBelongingToCampaign } }" tag="a" class="nav-link" exact><i class="fa fa-folder-open"></i>
                Emails in Campaign</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()"> <!-- ! appCurrentRouteIdParam -->
            <router-link v-bind:to="{ name: 'admin_emails.trash' }" tag="a" class="nav-link" exact><i class="fa fa-trash"></i>
                Deleted Emails ({{ deletedNum }})</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_emails.create' }" tag="a" class="nav-link" exact><i class="fa fa-pencil"></i>
                New Email</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_emails.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-eye"></i>
                {{ viewTabText() }}</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read') && showStatsTab()">
            <router-link v-bind:to="{ name: 'admin_emails.stats', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-pie-chart"></i>
                Email Stats </router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_emails.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-edit"></i>
                 {{ editTabText() }}</router-link>
        </li>
    </ul>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function () {
                let vm = this;

                vm.rootEventsHub.$on('show-drafts-tab', function(data) {
                    if ( data.draftsNum )
                        vm.draftsNum = data.draftsNum;
                });

                vm.rootEventsHub.$on('show-deleted-tab', function(data) {
                    if ( data.deletedNum )
                        vm.deletedNum = data.deletedNum;
                });

                vm.rootEventsHub.$on('show-edit-tab', function(data) {
                    if ( data.resource )
                        vm.readyResource = data.resource;
                });

            });
        },
        data() {
            return {
                draftsNum: 0,
                deletedNum: 0,
                readyResource: {is_draft: false, status: -2}
            }
        },
        computed: {
            isLookingAtDraft() {
                let vm = this;

                return (vm.readyResource && vm.readyResource.is_draft) || (vm.readyResource && vm.readyResource.status === -2);
            },
            isLookingAtNonDraft() {
                let vm = this;
                return (vm.readyResource && vm.readyResource.status !== -2 );
            },
            isLookingAtSuccessfullySent() {
                let vm = this;
                return (vm.readyResource && vm.readyResource.status === 1 );
            }
        },
        methods: {
            viewTabText() {
                let vm = this;
                let text = 'View Email';

                if ( vm.isLookingAtDraft )
                    text = 'View Draft';

                return text;
            },
            editTabText() {
                let vm = this;
                let text = 'Edit Email';

                if ( vm.isLookingAtDraft )
                    text = 'Edit Draft';
                else if ( vm.isLookingAtNonDraft)
                    text = 'Forward Email';

                return text;
            },
            showStatsTab() {
                return this.isLookingAtSuccessfullySent;
            }
        }
    }
</script>