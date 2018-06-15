<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('update')">
                <h3 class="mb-5">
                    <i class="icon ion-ios-compose"></i> {{ resource.title }}
                </h3>

                <form v-on:submit.prevent='updateResource'>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="title">Title</label>
                            <div class="">
                                <input type="text" class="form-control" id="title" v-model.trim="resource.title" v-bind:class="validationErrors.title ? 'is-invalid' : ''" placeholder="Title">
                                <small class="invalid-feedback">
                                    {{ validationErrors.title }}
                                </small>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="slug">URL Slug</label>
                            <div class="">
                                <input type="text" class="form-control" id="slug" v-model.trim="resource.slug" v-bind:class="validationErrors.slug ? 'is-invalid' : ''" placeholder="url-slug">
                                <small class="invalid-feedback">
                                    {{ validationErrors.slug }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="parent_id">Parent <small class="text-danger">{{ validationErrors.parent_id }}</small></label>
                            <div class="">
                                <select class="custom-select form-control" v-model="resource.parent_id" id="parent_id">
                                    <option value="">(No parent)</option>
                                    <option v-for="option in sortedParents" v-bind:value="option.id" v-if="isNotCurrent(option.id)">
                                        {{ option.title }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label" for="slug">Menu Order</label>
                            <div class="">
                                <input type="text" class="form-control" id="order" v-model.trim="resource.order" v-bind:class="validationErrors.order ? 'is-invalid' : ''" placeholder="0">
                                <small class="invalid-feedback">
                                    {{ validationErrors.order }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="content">Content <small class="text-danger">{{ validationErrors.content }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="content" v-model.trim="resource.content" rows="4" v-on:click="checkEditor" placeholder="Content"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                            <button type="submit" class="btn btn-info btn-lg">Update</button>
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
                <i class="icon ion-alert"></i> {{ appUnauthorisedErrorMessage }}
            </div>
        </template>
    </div>
</template>

<script>
    import slugify from 'slugify';

    export default {
        mounted() {
            this.$nextTick(function() {
                this.getResource();
                this.listenEvents();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {title: '', slug: '', parent_id: '', order: '', content: ''},
                validationErrors: {title: '', slug: '', parent_id: '', order: '', content: ''},
                listRoute: 'admin_user_guides.index',
                moreOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Delete User Guide', value: 'delete' },
                ],
                moreOption: '',
                parents: [],
                originalTitle: '',
                originalSlug: '',
                editorReady: false
            }
        },
        computed: {
            sortedParents() {
                return _.sortBy(this.parents, ['title']);
            },
        },
        methods: {
            getResource() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/edit').then(function(response) {
                    if ( response.data ) {
                        if ( response.data.resource ) {
                            vm.resource = response.data.resource;
                            vm.originalTitle = response.data.resource.title;
                            vm.originalSlug = response.data.resource.slug;
                        }

                        vm.resource.parent_id = response.data.resource.parent_id ? response.data.resource.parent_id : '';

                        if ( response.data.parents && response.data.parents.length )
                            vm.parents = response.data.parents;
                    }

                    vm.initTinyMce(10);
                    progress.finish();
                    vm.fetchingData = false;

                }, function(error) {
                    if ( error.status && error.status === 403 && error.data ) {
                        swal({ title: "Uh oh!", text: error.data.error, type: 'error', animation: 'slide-from-top'}, function(){
                            window.location.replace(vm.appUserHome);
                        });
                    }
                    else if ( error.status && error.status === 404 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();
                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            updateResource() {
                this.appUpdateResource();
            },
            deleteResource() {
                this.appDeleteResource();
            },
            initTinyMce(wait) {
                let vm = this;
                let defaultConfig = tinyMceConfig;

                let newCOnfig = {
                    selector: '#content',
                    setup: function(editor) {
                        editor.on('init', function() {
                            editor.setContent(vm.resource.content);
                            vm.editorReady = true;
                        });

                        editor.on('change keyup blur', function() {
                            vm.resource.content = editor.getContent();
                        });
                    },
                };

                _.delay(function() {
                    tinymce.remove();
                    tinymce.init(_.assign(defaultConfig, newCOnfig));
                }, parseInt(wait));
            },
            checkEditor() {
                let vm = this;
                if ( ! vm.editorReady )
                    vm.initTinyMce(10);
            },
            listenEvents() {
                let vm = this;

                vm.$on('unsuccessfulupdate', function() {
                    vm.initTinyMce(50);
                });

                vm.$on('successfulupdate', function(data) {
                    vm.initTinyMce(50);
                    if ( data.response )
                        vm.resource.slug = data.response.data.slug;
                });
            },
            isNotCurrent(optionId) {
                return this.resource.id !== optionId;
            }
        },
        watch: {
            'resource.title'(newValue) {
                let vm = this;

                if ( newValue.toLowerCase() === vm.originalTitle.toLowerCase() )
                    vm.resource.slug = vm.originalSlug;
                else
                    vm.resource.slug = slugify(newValue, {lower: true, remove: /[^\w\s-]/g});
            },
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
