<template>
    <ul class="nav nav-pills mt-3">
        <li class="nav-item">
            <router-link v-bind:to="{ name: 'profile.index' }" tag="a" class="nav-link" exact><i class="icon ion-ios-person"></i> Profile</router-link>
        </li>
        <li class="nav-item">
            <router-link v-bind:to="{ name: 'profile.edit_profile' }" tag="a" class="nav-link" exact><i class="icon ion-edit"></i> Edit</router-link>
        </li>
        <li class="nav-item">
            <router-link v-bind:to="{ name: 'profile.edit_password' }" tag="a" class="nav-link" exact><i class="icon ion-locked"></i> Password</router-link>
        </li>
        <li class="nav-item">
            <router-link v-bind:to="{ name: 'profile.login_activities' }" tag="a" class="nav-link" exact><i class="fa fa-exchange"></i> Login Activities</router-link>
        </li>
    </ul>
</template>
