
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, { color: '#F8CA00', failedColor: '#FF003C', thickness: '5px'});

//.default to fix bug!!!
Vue.component('pagination', require('vue-bootstrap-pagination').default);

import VueSelect from 'vue-select';
Vue.component('v-select', VueSelect);

import DatePicker from 'vue-datepicker';
Vue.component('datepicker', DatePicker);

import AppListScreenPlugin from './plugins/AppListScreenPlugin';
Vue.use(AppListScreenPlugin);

import AppCreateScreenPlugin from './plugins/AppCreateScreenPlugin';
Vue.use(AppCreateScreenPlugin);

import AppShowScreenPlugin from './plugins/AppShowScreenPlugin';
Vue.use(AppShowScreenPlugin);

import AppEditScreenPlugin from './plugins/AppEditScreenPlugin';
Vue.use(AppEditScreenPlugin);

import AppHelpers from './plugins/AppHelpers';
Vue.use(AppHelpers);

/**
 * Dashboard
 */
import AdminDashboard from './components/AdminDashboard/AdminDashboard.vue';
import AdminDashboardDashboard from './components/AdminDashboard/Dashboard.vue';


if ( $('#admin-dashboard-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'dashboard.index', component: AdminDashboardDashboard },
            { path: '*', redirect: { name: 'dashboard.index' } }
        ]
    });

    new Vue({
        el: '#admin-dashboard-app',
        components: {
            AdminDashboard
        },
        router: router
    });
}

/**
 * Profile
 */
import AdminProfile from './components/AdminProfile/AdminProfile.vue';
import AdminProfileProfile from './components/AdminProfile/Profile.vue';
import AdminEditProfile from './components/AdminProfile/EditProfile.vue';
import AdminEditPassword from './components/AdminProfile/EditPassword.vue';
import AdminLoginActivities from './components/AdminProfile/LoginActivities.vue';

if ( $('#admin-profile-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'profile.index', component: AdminProfileProfile },
            { path: '/edit-profile', name: 'profile.edit_profile', component: AdminEditProfile },
            { path: '/edit-password', name: 'profile.edit_password', component: AdminEditPassword },
            { path: '/login-activities', name: 'profile.login_activities', component: AdminLoginActivities },
            { path: '*', redirect: { name: 'profile.index' } }
        ]
    });

    new Vue({
        el: '#admin-profile-app',
        components: {
            AdminProfile
        },
        router: router
    });
}

/**
 * Mailing Lists
 */
import AdminMailingLists from './components/AdminMailingLists/AdminMailingLists.vue';
import AdminMailingListsAll from './components/AdminMailingLists/AllMailingLists.vue';
import AdminMailingListsTrash from './components/AdminMailingLists/TrashMailingLists.vue';
import AdminMailingListsNew from './components/AdminMailingLists/NewMailingList.vue';
import AdminMailingListsView from './components/AdminMailingLists/ViewMailingList.vue';
import AdminMailingListsEdit from './components/AdminMailingLists/EditMailingList.vue';

if ( $('#admin-mailing-lists-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_mailing_lists.index', component: AdminMailingListsAll },
            { path: '/trash', name: 'admin_mailing_lists.trash', component: AdminMailingListsTrash },
            { path: '/create', name: 'admin_mailing_lists.create', component: AdminMailingListsNew },
            { path: '/:id(\\d+)/view', name: 'admin_mailing_lists.view', component: AdminMailingListsView },
            { path: '/:id(\\d+)/edit', name: 'admin_mailing_lists.edit', component: AdminMailingListsEdit },
            { path: '*', redirect: { name: 'admin_mailing_lists.index' } }
        ]
    });

    new Vue({
        el: '#admin-mailing-lists-app',
        components: {
            AdminMailingLists
        },
        router: router
    });
}

/**
 * Subscribers
 */
import AdminSubscribers from './components/AdminSubscribers/AdminSubscribers.vue';
import AdminSubscribersAll from './components/AdminSubscribers/AllSubscribers.vue';
import AdminSubscribersTrash from './components/AdminSubscribers/TrashSubscribers.vue';
import AdminSubscribersNew from './components/AdminSubscribers/NewSubscriber.vue';
import AdminSubscribersImport from './components/AdminSubscribers/ImportSubscribers.vue';
import AdminSubscribersView from './components/AdminSubscribers/ViewSubscriber.vue';
import AdminSubscribersEdit from './components/AdminSubscribers/EditSubscriber.vue';

if ( $('#admin-subscribers-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_subscribers.index', component: AdminSubscribersAll },
            { path: '/:mListId(\\d+)/in-mailing-list', name: 'admin_subscribers.list', component: AdminSubscribersAll },
            { path: '/unattached', name: 'admin_subscribers.unattached', component: AdminSubscribersAll },
            { path: '/trash', name: 'admin_subscribers.trash', component: AdminSubscribersTrash },
            { path: '/create', name: 'admin_subscribers.create', component: AdminSubscribersNew },
            { path: '/import', name: 'admin_subscribers.import', component: AdminSubscribersImport },
            { path: '/:id(\\d+)/view', name: 'admin_subscribers.view', component: AdminSubscribersView },
            { path: '/:id(\\d+)/edit', name: 'admin_subscribers.edit', component: AdminSubscribersEdit },
            { path: '*', redirect: { name: 'admin_subscribers.index' } }
        ]
    });

    new Vue({
        el: '#admin-subscribers-app',
        components: {
            AdminSubscribers
        },
        router: router
    });
}

/**
 * Campaigns
 */
import AdminCampaigns from './components/AdminCampaigns/AdminCampaigns.vue';
import AdminCampaignsAll from './components/AdminCampaigns/AllCampaigns.vue';
import AdminCampaignsTrash from './components/AdminCampaigns/TrashCampaigns.vue';
import AdminCampaignsNew from './components/AdminCampaigns/NewCampaign.vue';
import AdminCampaignsView from './components/AdminCampaigns/ViewCampaign.vue';
import AdminCampaignsEdit from './components/AdminCampaigns/EditCampaign.vue';

if ( $('#admin-campaigns-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_campaigns.index', component: AdminCampaignsAll },
            { path: '/trash', name: 'admin_campaigns.trash', component: AdminCampaignsTrash },
            { path: '/create', name: 'admin_campaigns.create', component: AdminCampaignsNew },
            { path: '/:id(\\d+)/view', name: 'admin_campaigns.view', component: AdminCampaignsView },
            { path: '/:id(\\d+)/edit', name: 'admin_campaigns.edit', component: AdminCampaignsEdit },
            { path: '*', redirect: { name: 'admin_campaigns.index' } }
        ]
    });

    new Vue({
        el: '#admin-campaigns-app',
        components: {
            AdminCampaigns
        },
        router: router
    });
}

/**
 * Templates
 */
import AdminTemplates from './components/AdminTemplates/AdminTemplates.vue';
import AdminTemplatesAll from './components/AdminTemplates/AllTemplates.vue';
import AdminTemplatesTrash from './components/AdminTemplates/TrashTemplates.vue';
import AdminTemplatesNew from './components/AdminTemplates/NewTemplate.vue';
import AdminTemplatesView from './components/AdminTemplates/ViewTemplate.vue';
import AdminTemplatesEdit from './components/AdminTemplates/EditTemplate.vue';

if ( $('#admin-templates-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_templates.index', component: AdminTemplatesAll },
            { path: '/trash', name: 'admin_templates.trash', component: AdminTemplatesTrash },
            { path: '/create', name: 'admin_templates.create', component: AdminTemplatesNew },
            { path: '/:id(\\d+)/view', name: 'admin_templates.view', component: AdminTemplatesView },
            { path: '/:id(\\d+)/edit', name: 'admin_templates.edit', component: AdminTemplatesEdit },
            { path: '*', redirect: { name: 'admin_templates.index' } }
        ]
    });

    new Vue({
        el: '#admin-templates-app',
        components: {
            AdminTemplates
        },
        router: router
    });
}

/**
 * EmailSettings
 */
import AdminEmailSettings from './components/AdminEmailSettings/AdminEmailSettings.vue';
import AdminEmailSettingsAll from './components/AdminEmailSettings/AllEmailSettings.vue';
import AdminEmailSettingsNew from './components/AdminEmailSettings/NewEmailSetting.vue';
import AdminEmailSettingsView from './components/AdminEmailSettings/ViewEmailSetting.vue';
import AdminEmailSettingsEdit from './components/AdminEmailSettings/EditEmailSetting.vue';

if ( $('#admin-email-settings-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_email_settings.index', component: AdminEmailSettingsAll },
            { path: '/create', name: 'admin_email_settings.create', component: AdminEmailSettingsNew },
            { path: '/:id(\\d+)/view', name: 'admin_email_settings.view', component: AdminEmailSettingsView },
            { path: '/:id(\\d+)/edit', name: 'admin_email_settings.edit', component: AdminEmailSettingsEdit },
            { path: '*', redirect: { name: 'admin_email_settings.index' } }
        ]
    });

    new Vue({
        el: '#admin-email-settings-app',
        components: {
            AdminEmailSettings
        },
        router: router
    });
}


/**
 * Emails
 */
import AdminEmails from './components/AdminEmails/AdminEmails.vue';
import AdminEmailsAll from './components/AdminEmails/AllEmails.vue';
import AdminEmailsTrash from './components/AdminEmails/TrashEmails.vue';
import AdminEmailsNew from './components/AdminEmails/NewEmail.vue';
import AdminEmailsView from './components/AdminEmails/ViewEmail.vue';
import AdminEmailsStats from './components/AdminEmails/ViewEmailStats.vue';
import AdminEmailsClicksStats from './components/AdminEmails/ViewEmailClicksStats.vue';
import AdminEmailsFailuresStats from './components/AdminEmails/ViewEmailFailuresStats.vue';
import AdminEmailsEdit from './components/AdminEmails/EditEmail.vue';

import StatsPlugin from './components/AdminEmails/Stats/StatsPlugin';

if ( $('#admin-emails-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_emails.index', component: AdminEmailsAll },
            { path: '/drafts', name: 'admin_emails.drafts', component: AdminEmailsAll },
            { path: '/:campaignId(\\d+)/in-campaign', name: 'admin_emails.campaign', component: AdminEmailsAll },
            { path: '/:userId(\\d+)/by-user', name: 'admin_emails.user', component: AdminEmailsAll },
            { path: '/trash', name: 'admin_emails.trash', component: AdminEmailsTrash },
            { path: '/create', name: 'admin_emails.create', component: AdminEmailsNew },
            { path: '/:id(\\d+)/view', name: 'admin_emails.view', component: AdminEmailsView },
            { path: '/:id(\\d+)/stats/general', name: 'admin_emails.stats', component: AdminEmailsStats },
            { path: '/:id(\\d+)/stats/opens', name: 'admin_emails.open_stats', component: AdminEmailsStats },
            { path: '/:id(\\d+)/stats/clicks', name: 'admin_emails.click_stats', component: AdminEmailsClicksStats },
            { path: '/:id(\\d+)/stats/failures', name: 'admin_emails.failure_stats', component: AdminEmailsFailuresStats },
            { path: '/:id(\\d+)/stats/*', redirect: { name: 'admin_emails.stats' } },
            { path: '/:id(\\d+)/edit', name: 'admin_emails.edit', component: AdminEmailsEdit },
            { path: '*', redirect: { name: 'admin_emails.index' } }
        ]
    });

    Vue.use(StatsPlugin);

    new Vue({
        el: '#admin-emails-app',
        components: {
            AdminEmails
        },
        router: router
    });
}

/**
 * Email Contents
 */
import AdminEmailContents from './components/AdminEmailContents/AdminEmailContents.vue';
import AdminEmailContentsAll from './components/AdminEmailContents/AllEmailContents.vue';
import AdminEmailContentsTrash from './components/AdminEmailContents/TrashEmailContents.vue';
import AdminEmailContentsNew from './components/AdminEmailContents/NewEmailContent.vue';
import AdminEmailContentsView from './components/AdminEmailContents/ViewEmailContent.vue';
import AdminEmailContentsEdit from './components/AdminEmailContents/EditEmailContent.vue';

if ( $('#admin-email-contents-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_email_contents.index', component: AdminEmailContentsAll },
            { path: '/trash', name: 'admin_email_contents.trash', component: AdminEmailContentsTrash },
            { path: '/create', name: 'admin_email_contents.create', component: AdminEmailContentsNew },
            { path: '/:id(\\d+)/view', name: 'admin_email_contents.view', component: AdminEmailContentsView },
            { path: '/:id(\\d+)/edit', name: 'admin_email_contents.edit', component: AdminEmailContentsEdit },
            { path: '*', redirect: { name: 'admin_email_contents.index' } }
        ]
    });

    new Vue({
        el: '#admin-email-contents-app',
        components: {
            AdminEmailContents
        },
        router: router
    });
}

/**
 * Users
 */
import AdminUsers from './components/AdminUsers/AdminUsers.vue';
import AdminUsersAll from './components/AdminUsers/AllUsers.vue';
import AdminUsersNew from './components/AdminUsers/NewUser.vue';
import AdminUsersView from './components/AdminUsers/ViewUser.vue';
import AdminUsersEdit from './components/AdminUsers/EditUser.vue';
import AdminUsersEditPermissions from './components/AdminUsers/EditUserPermissions.vue';
import AdminUsersLoginActivities from './components/AdminUsers/ViewLoginActivities';

if ( $('#admin-users-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_users.index', component: AdminUsersAll },
            { path: '/create', name: 'admin_users.create', component: AdminUsersNew },
            { path: '/:id(\\d+)/view', name: 'admin_users.view', component: AdminUsersView },
            { path: '/:id(\\d+)/edit', name: 'admin_users.edit', component: AdminUsersEdit },
            { path: '/:id(\\d+)/permissions', name: 'admin_users.edit_permissions', component: AdminUsersEditPermissions },
            { path: '/:id(\\d+)/login-activities', name: 'admin_users.view_login_activities', component: AdminUsersLoginActivities },
            { path: '*', redirect: { name: 'admin_users.index' } }
        ]
    });

    new Vue({
        el: '#admin-users-app',
        components: {
            AdminUsers
        },
        router: router
    });
}


/**
 * User Guides
 */
import AdminUserGuides from './components/AdminUserGuides/AdminUserGuides.vue';
import AdminUserGuidesAll from './components/AdminUserGuides/AllUserGuides.vue';
import AdminUserGuidesTrash from './components/AdminUserGuides/TrashUserGuides.vue';
import AdminUserGuidesNew from './components/AdminUserGuides/NewUserGuide.vue';
import AdminUserGuidesView from './components/AdminUserGuides/ViewUserGuide.vue';
import AdminUserGuidesEdit from './components/AdminUserGuides/EditUserGuide.vue';

if ( $('#admin-user-guides-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_user_guides.index', component: AdminUserGuidesAll },
            { path: '/trash', name: 'admin_user_guides.trash', component: AdminUserGuidesTrash },
            { path: '/create', name: 'admin_user_guides.create', component: AdminUserGuidesNew },
            { path: '/:id(\\d+)/view', name: 'admin_user_guides.view', component: AdminUserGuidesView },
            { path: '/:id(\\d+)/edit', name: 'admin_user_guides.edit', component: AdminUserGuidesEdit },
            { path: '*', redirect: { name: 'admin_user_guides.index' } }
        ]
    });

    new Vue({
        el: '#admin-user-guides-app',
        components: {
            AdminUserGuides
        },
        router: router
    });
}

/**
 * Developer Guides
 */
import AdminDeveloperGuides from './components/AdminDeveloperGuides/AdminDeveloperGuides.vue';
import AdminDeveloperGuidesAll from './components/AdminDeveloperGuides/AllDeveloperGuides.vue';
import AdminDeveloperGuidesTrash from './components/AdminDeveloperGuides/TrashDeveloperGuides.vue';
import AdminDeveloperGuidesNew from './components/AdminDeveloperGuides/NewDeveloperGuide.vue';
import AdminDeveloperGuidesView from './components/AdminDeveloperGuides/ViewDeveloperGuide.vue';
import AdminDeveloperGuidesEdit from './components/AdminDeveloperGuides/EditDeveloperGuide.vue';

if ( $('#admin-developer-guides-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'admin_developer_guides.index', component: AdminDeveloperGuidesAll },
            { path: '/trash', name: 'admin_developer_guides.trash', component: AdminDeveloperGuidesTrash },
            { path: '/create', name: 'admin_developer_guides.create', component: AdminDeveloperGuidesNew },
            { path: '/:id(\\d+)/view', name: 'admin_developer_guides.view', component: AdminDeveloperGuidesView },
            { path: '/:id(\\d+)/edit', name: 'admin_developer_guides.edit', component: AdminDeveloperGuidesEdit },
            { path: '*', redirect: { name: 'admin_developer_guides.index' } }
        ]
    });

    new Vue({
        el: '#admin-developer-guides-app',
        components: {
            AdminDeveloperGuides
        },
        router: router
    });
}