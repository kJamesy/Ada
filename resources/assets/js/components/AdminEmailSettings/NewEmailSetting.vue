<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="name">Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name" v-model.trim="resource.name" v-bind:class="validationErrors.name ? 'is-invalid' : ''" placeholder="Setting Name">
                            <small class="invalid-feedback">
                                {{ validationErrors.name }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="description">Description</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="description" rows="4" v-model.trim="resource.description" v-bind:class="validationErrors.description ? 'is-invalid' : ''" placeholder="Setting Description">
                            </textarea>
                            <small class="invalid-feedback">
                                {{ validationErrors.description }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label" for="setting_value">Setting Value</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="setting_value" rows="4" v-model.trim="resource.setting_value" v-bind:class="validationErrors.setting_value ? 'is-invalid' : ''" placeholder="Setting Value">
                            </textarea>
                            <small class="invalid-feedback">
                                {{ validationErrors.setting_value }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 ml-md-auto">
                            <button type="submit" class="btn btn-info btn-lg">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="! appUserHasPermission('create')">
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
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
                resource: {name: '', description: '', setting_value: ''},
                validationErrors: {name: '', description: '', setting_value: ''}
            }
        },
        methods: {
            createResource() {
                this.appCreateResource();
            }
        }
    }
</script>
