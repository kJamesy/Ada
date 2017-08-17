<template>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.index' }" tag="a" class="nav-link" exact><i class="fa fa-home"></i>
                All Mailing Lists</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()"> <!-- ! appCurrentRouteIdParam -->
            <router-link v-bind:to="{ name: 'admin_mailing_lists.trash' }" tag="a" class="nav-link" exact><i class="fa fa-trash"></i>
                Deleted Mailing Lists</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.create' }" tag="a" class="nav-link" exact><i class="fa fa-pencil"></i>
                New Mailing List</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-eye"></i>
                View Mailing List</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_mailing_lists.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-edit"></i>
                Edit Mailing List</router-link>
        </li>
    </ul>
</template>
