<template>
    <div class="card pd-20">
        <div class="card-body">
            <vue-progress-bar></vue-progress-bar>
            <app-nav></app-nav>
            <router-view v-bind:key="$route.fullPath"></router-view>
        </div>
    </div>
</template>

<script>
    import AppNav from './Nav.vue';

    export default {
        components: {
            AppNav
        }
    }
</script>
