<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_user_guides.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i>
                All User Guides</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()">
            <router-link v-bind:to="{ name: 'admin_user_guides.trash' }" tag="a" class="nav-link" exact><i class="icon ion-trash-a"></i>
                Deleted User Guides</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_user_guides.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i>
                New User Guide</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_user_guides.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-eye"></i>
                View User Guide</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_user_guides.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i>
                Edit User Guide</router-link>
        </li>
    </ul>
</template>
