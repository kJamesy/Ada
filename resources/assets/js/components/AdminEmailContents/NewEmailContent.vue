<template>
    <div class="mt-5">
        <div class="sk-spinner sk-spinner-pulse bg-gray-800" v-if="fetchingData"></div>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-on:submit.prevent='createResource'>
                    <div class="form-group">
                        <label class="form-control-label" for="title">Title</label>
                        <div class="">
                            <input type="text" class="form-control" id="title" v-model.trim="resource.title" v-bind:class="validationErrors.title ? 'is-invalid' : ''" placeholder="Title">
                            <small class="invalid-feedback">
                                {{ validationErrors.title }}
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="slug">URL Slug</label>
                        <div class="">
                            <input type="text" class="form-control" id="slug" v-model.trim="resource.slug" v-bind:class="validationErrors.slug ? 'is-invalid' : ''" placeholder="url-slug">
                            <small class="invalid-feedback">
                                {{ validationErrors.slug }}
                            </small>
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
    import slugify from 'slugify';

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
                    vm.initTinyMce(10);
            },
            listenEvents() {
                let vm = this;

                vm.$on('unsuccessfulcreate', function() {
                    vm.initTinyMce(10);
                });

                vm.$on('successfulcreate', function(data) {
                    vm.initTinyMce(10);

                    if ( data.response )
                        vm.$router.push({name: 'admin_email_contents.view', params: { id: data.response.data.id }});
                });
            }
        },
        watch: {
            'resource.title'(newValue) {
                this.resource.slug = slugify(newValue, {lower: true, remove: /[^\w\s-]/g});
            }
        }
    }
</script>
