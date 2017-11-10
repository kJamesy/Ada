<template>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_contents.index' }" tag="a" class="nav-link" exact><i class="fa fa-home"></i>
                All Contents</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()">
            <router-link v-bind:to="{ name: 'admin_email_contents.trash' }" tag="a" class="nav-link" exact><i class="fa fa-trash"></i>
                Deleted Contents</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_email_contents.create' }" tag="a" class="nav-link" exact><i class="fa fa-pencil"></i>
                New Content</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_email_contents.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-eye"></i>
                View Content</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_email_contents.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-edit"></i>
                Edit Content</router-link>
        </li>
    </ul>
</template>
