
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
 * Settings
 */
import Admin from './components/Admin/Admin.vue';
import AdminDashboard from './components/Admin/Dashboard.vue';
import AdminProfile from './components/Admin/Profile.vue';
import AdminEditProfile from './components/Admin/EditProfile.vue';
import AdminEditPassword from './components/Admin/EditPassword.vue';

if ( $('#admin-app').length ) {
    let router = new VueRouter({
        mode: 'history',
        base: links.base,
        linkActiveClass: 'active',
        routes: [
            { path: '/', name: 'settings.index', component: AdminDashboard },
            { path: '/profile', name: 'settings.profile', component: AdminProfile },
            { path: '/edit-profile', name: 'settings.edit_profile', component: AdminEditProfile },
            { path: '/edit-password', name: 'settings.edit_password', component: AdminEditPassword },
            { path: '*', redirect: { name: 'settings.index' } }
        ]
    });

    new Vue({
        el: '#admin-app',
        components: {
            Admin
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
import AdminSubscribersList from './components/AdminSubscribers/AllSubscribers.vue';
import AdminSubscribersUnattached from './components/AdminSubscribers/AllSubscribers.vue';
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
            { path: '/:mListId(\\d+)/in-mailing-list', name: 'admin_subscribers.list', component: AdminSubscribersList},
            { path: '/unattached', name: 'admin_subscribers.unattached', component: AdminSubscribersUnattached},
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
 * Users
 */
import AdminUsers from './components/AdminUsers/AdminUsers.vue';
import AdminUsersAll from './components/AdminUsers/AllUsers.vue';
import AdminUsersNew from './components/AdminUsers/NewUser.vue';
import AdminUsersView from './components/AdminUsers/ViewUser.vue';
import AdminUsersEdit from './components/AdminUsers/EditUser.vue';
import AdminUsersEditPermissions from './components/AdminUsers/EditUserPermissions.vue';

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