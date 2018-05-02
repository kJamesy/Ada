<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_settings.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i>
                All Email Settings</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_email_settings.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i>
                New Email Setting</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_settings.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-eye"></i>
                View Email Setting</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_email_settings.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i>
                Edit Email Setting</router-link>
        </li>
    </ul>
</template>
