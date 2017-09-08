<template>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item" v-if="appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_emails.index' }" tag="a" class="nav-link" exact><i class="fa fa-home"></i>
                All Emails</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appBelongingToUser">
            <router-link v-bind:to="{ name: 'admin_emails.list', params: { userId: appBelongingToUser } }" tag="a" class="nav-link" exact><i class="fa fa-user"></i>
                Emails by User</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appBelongingToCampaign">
            <router-link v-bind:to="{ name: 'admin_emails.list', params: { campaignId: appBelongingToCampaign } }" tag="a" class="nav-link" exact><i class="fa fa-folder-open"></i>
                Emails in Campaign</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('read') && appIsTrashPage()"> <!-- ! appCurrentRouteIdParam -->
            <router-link v-bind:to="{ name: 'admin_emails.trash' }" tag="a" class="nav-link" exact><i class="fa fa-trash"></i>
                Deleted Emails</router-link>
        </li>
        <li class="nav-item" v-if="appUserHasPermission('create')">
            <router-link v-bind:to="{ name: 'admin_emails.create' }" tag="a" class="nav-link" exact><i class="fa fa-pencil"></i>
                New Email</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('read')">
            <router-link v-bind:to="{ name: 'admin_emails.view', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-eye"></i>
                View Email</router-link>
        </li>
        <li class="nav-item" v-if="appCurrentRouteIdParam && appUserHasPermission('update')">
            <router-link v-bind:to="{ name: 'admin_emails.edit', params: { id: appCurrentRouteIdParam }}" class="nav-link" exact><i class="fa fa-edit"></i>
                Edit Email</router-link>
        </li>
    </ul>
</template>
