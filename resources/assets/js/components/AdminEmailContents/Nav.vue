<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_contents.index' }" tag="a" class="nav-link" exact><i class="icon ion-android-home"></i>
                All Contents</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()">
            <router-link v-bind:to="{ name: 'admin_email_contents.trash' }" tag="a" class="nav-link" exact><i class="icon ion-trash-a"></i>
                Deleted Contents</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_email_contents.create' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i>
                New Content</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_contents.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-eye"></i>
                View Content</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_email_contents.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="icon ion-ios-compose"></i>
                Edit Content</router-link>
        </li>
    </ul>
</template>
