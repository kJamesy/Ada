<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group ">
                        <label class="form-control-label" for="name">Name <small class="text-danger">{{ validationErrors.name }}</small></label>
                        <div class="">
                            <input type="text" class="form-control" id="name" v-model.trim="resource.name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="description">Description <small class="text-danger">{{ validationErrors.description }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="description" rows="4" v-model.trim="resource.description">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="content">Content <small class="text-danger">{{ validationErrors.content }}</small></label>
                        <div class="">
                            <textarea class="form-control" id="content" v-model.trim="resource.content" rows="4" v-on:click="checkEditor">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="ml-md-auto">
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
                this.goTime();
                this.listenEvents();
            });
        },
        data() {
            return {
                fetchingData: true,
                resource: {name: '', description: '', content: ''},
                validationErrors: {name: '', description: '', content: ''},
                templates: [],
                editorReady: false
            }
        },
        methods: {
            goTime() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.$http.get(vm.appResourceUrl + '/create').then(function(response) {
                    if ( response.data && response.data.templates && response.data.templates.length )
                        vm.templates = response.data.templates;

                    vm.initTinyMce(10);
                    progress.finish();
                    vm.fetchingData = false;

                }, function(error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();

                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            createResource() {
                this.appCreateResource();
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

                vm.$on('unsuccessfulcreate', function() {
                    vm.initTinyMce(50);
                });

                vm.$on('successfulcreate', function() {
                    vm.initTinyMce(50);
                });
            }
        },
    }
</script>
