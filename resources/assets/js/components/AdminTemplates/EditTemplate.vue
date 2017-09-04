<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('update')">
                <h3 class="mb-5">
                    <i class="fa fa-edit"></i> {{ resource.name }}
                </h3>

                <form v-on:submit.prevent='updateResource' v-if="! fetchingData ">
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
                            <button type="submit" class="btn btn-primary btn-outline-primary">Update</button>
                            <form action="" class="form-inline pull-right">
                                <label class="form-control-label mr-sm-2" for="more-options">More Options</label>
                                <select class="custom-select form-control mb-2 mb-sm-0" v-model="moreOption" id="more-options">
                                    <option v-for="option in moreOptions" v-bind:value="option.value" v-if="appUserHasPermission(option.value)">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
            <div v-else="">
                <i class="fa fa-warning"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(function() {
                this.getResource();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {name: '', description: ''},
                validationErrors: {name: '', description: ''},
                listRoute: 'admin_campaigns.index',
                moreOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Delete Campaign', value: 'delete' },
                ],
                moreOption: ''
            }
        },
        methods: {
            getResource() {
                this.appGetResource();
            },
            updateResource() {
                this.appUpdateResource();
            },
            deleteResource() {
                this.appDeleteResource();
            },
        },
        watch: {
            moreOption(action) {
                let vm = this;

                if ( action.length ) {
                    if ( action === 'delete' && vm.appUserHasPermission(action) ) {
                        swal({title: 'Hey, are you sure about this?', type: "warning", showCancelButton: true, confirmButtonText: _.capitalize(action)}, function (confirmed) {
                            if (confirmed)
                                vm.deleteResource();
                            else
                                vm.moreOption = '';
                        });
                    }
                }
            },
        }
    }
</script>
