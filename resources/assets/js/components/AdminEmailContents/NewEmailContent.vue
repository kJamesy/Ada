<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group">
                        <label class="form-control-label" for="title">Title</label>
                        <div class="">
                            <input type="text" class="form-control" id="title" v-model.trim="resource.title" v-bind:class="validationErrors.title ? 'is-invalid' : ''">
                            <small class="invalid-feedback">
                                {{ validationErrors.title }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="slug">URL Slug</label>
                        <div class="">
                            <input type="text" class="form-control" id="slug" v-model.trim="resource.slug" v-bind:class="validationErrors.slug ? 'is-invalid' : ''">
                            <small class="invalid-feedback">
                                {{ validationErrors.slug }}
                            </small>
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
                        <div class="">
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
                resource: {title: '', slug: '', content: ''},
                validationErrors: {title: '', slug: '', content: ''},
                editorReady: false
            }
        },
        methods: {
            goTime() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();

                vm.initTinyMce(10);
                progress.finish();
                vm.fetchingData = false;
            },
            createResource() {
                this.appCreateResource();
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
        }
    }
</script>
