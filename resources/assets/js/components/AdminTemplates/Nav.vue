<template>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_templates.index' }" tag="a" class="nav-link" exact><i class="fa fa-home"></i>
                All Templates</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()">
            <router-link v-bind:to="{ name: 'admin_templates.trash' }" tag="a" class="nav-link" exact><i class="fa fa-trash"></i>
                Deleted Templates</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_templates.create' }" tag="a" class="nav-link" exact><i class="fa fa-pencil"></i>
                New Template</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_templates.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-eye"></i>
                View Template</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_templates.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-edit"></i>
                Edit Template</router-link>
        </li>
    </ul>
</template>
