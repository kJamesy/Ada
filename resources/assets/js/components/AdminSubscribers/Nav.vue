<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_subscribers.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i>
                All Subscribers</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appBelongingToMList">
            <router-link v-bind:to="{ name: 'admin_subscribers.list', params: { mListId: appBelongingToMList } }" tag="a" class="nav-link" exact><i class="icon ion-ios-list-outline"></i>
                Subscribers in Mailing List</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appUnattached">
            <router-link v-bind:to="{ name: 'admin_subscribers.unattached'}" tag="a" class="nav-link" exact><i class="icon ion-alert"></i>
                Unattached Subscribers</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()"> <!-- ! appCurrentRouteIdParam -->
            <router-link v-bind:to="{ name: 'admin_subscribers.trash' }" tag="a" class="nav-link" exact><i class="icon ion-trash-a"></i>
                Deleted Subscribers</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_subscribers.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i>
                New Subscriber</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create') && appImporting">
            <router-link v-bind:to="{ name: 'admin_subscribers.import' }" tag="a" class="nav-link" exact><i class="icon ion-ios-cloud-upload-outline"></i>
                Import Subscribers</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_subscribers.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-eye"></i>
                View Subscriber</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_subscribers.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i>
                Edit Subscriber</router-link>
        </li>
    </ul>
</template>
