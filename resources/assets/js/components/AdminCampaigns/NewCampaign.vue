<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="name">Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name" v-model.trim="resource.name" v-bind:class="validationErrors.name ? 'is-invalid' : ''">
                            <small class="invalid-feedback">
                                {{ validationErrors.name }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="description">Description</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="description" rows="4" v-model.trim="resource.description" v-bind:class="validationErrors.description ? 'is-invalid' : ''">
                            </textarea>
                            <small class="invalid-feedback">
                                {{ validationErrors.description }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 ml-md-auto">
                            <button type="submit" class="btn btn-primary btn-outline-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="! appUserHasPermission('create')">
                <i class="fa fa-warning"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.appGoTime();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {name: '', description: ''},
                validationErrors: {name: '', description: ''}
            }
        },
        methods: {
            createResource() {
                this.appCreateResource();
            }
        }
    }
</script>
