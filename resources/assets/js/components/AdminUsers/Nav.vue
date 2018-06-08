<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_users.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i> All Users</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_users.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i> New User</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_users.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="ion-ios-body-outline"></i> View User</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_users.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i> Edit User</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_users.edit_permissions', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ribbon-b"></i> Edit Permissions</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_users.view_login_activities', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-exchange"></i>
                Login Activities</router-link>
        </li>
    </ul>
</template>