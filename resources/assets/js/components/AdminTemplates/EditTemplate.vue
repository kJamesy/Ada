<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('update')">
                <h3 class="mb-5">
                    <i class="icon ion-ios-compose"></i> {{ resource.name }}
                </h3>

                <form v-on:submit.prevent='updateResource' v-if="! fetchingData ">
                    <div class="form-group ">
                        <label class="form-control-label" for="name">Name <small class="text-danger">{{ validationErrors.name }}</small></label>
                        <div class="">
                            <input type="text" class="form-control" id="name" v-model.trim="resource.name" placeholder="Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="description">Description <small class="text-danger">{{ validationErrors.description }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="description" rows="4" v-model.trim="resource.description" placeholder="Description">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="content">Content <small class="text-danger">{{ validationErrors.content }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="content" v-model.trim="resource.content" rows="4" v-on:click="checkEditor" placeholder="Content">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="ml-md-auto">
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
                resource: {name: '', description: '', content: ''},
                validationErrors: {name: '', description: '', content: ''},
                listRoute: 'admin_templates.index',
                moreOptions: [
                    { text: 'Select Option', value: '' },
                    { text: 'Delete Template', value: 'delete' },
                ],
                moreOption: '',
                templates: [],
                editorReady: false
            }
        },
        methods: {
            getResource() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/' + vm.id + '/edit').then(function(response) {
                    if ( response.data && response.data.resource )
                        vm.resource = response.data.resource;

                    if ( response.data && response.data.templates )
                        vm.templates = response.data.templates;

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

                defaultConfig.plugins[0] += ' template'; //Extra plugin

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
                    templates: vm.templates
                };

                _.delay(function() {
                    tinymce.remove();
                    tinymce.init(_.assign(defaultConfig, newCOnfig));
                }, parseInt(wait));
            },
            checkEditor() {
                let vm = this;
                if ( ! vm.editorReady )
                    vm.initTinyMce(50);
            },
            listenEvents() {
                let vm = this;

                vm.$on('unsuccessfulupdate', function() {
                    vm.initTinyMce(50);
                });

                vm.$on('successfulupdate', function() {
                    vm.initTinyMce(50);
                });
            }
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
