<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i>
                All Mailing Lists</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()"> <!-- ! appCurrentRouteIdParam -->
            <router-link v-bind:to="{ name: 'admin_mailing_lists.trash' }" tag="a" class="nav-link" exact><i class="icon ion-trash-a"></i>
                Deleted Mailing Lists</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i>
                New Mailing List</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-eye"></i>
                View Mailing List</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i>
                Edit Mailing List</router-link>
        </li>
    </ul>
</template>
