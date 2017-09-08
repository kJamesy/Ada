<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('update')">
                <h3 class="mb-5">
                    <i class="fa fa-edit"></i> {{ resource.name }}
                </h3>

                <form v-on:submit.prevent='updateResource' v-if="! fetchingData ">
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
                this.initTinyMce(400);
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
                editorReady: false
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
            initTinyMce(wait) {
                let vm = this;

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
                    }
                };

                _.delay(function() {
                    tinymce.remove();
                    tinymce.init(_.assign(tinyMceConfig, newCOnfig));
                }, parseInt(wait));
            },
            checkEditor() {
                let vm = this;
                if ( ! vm.editorReady )
                    vm.initTinyMce(100);
            },
            listenEvents() {
                let vm = this;

                vm.$on('successfulfetch', function() {
                    vm.initTinyMce(100);
                });

                vm.$on('unsuccessfulupdate', function() {
                    vm.initTinyMce(100);
                });

                vm.$on('successfulupdate', function() {
                    vm.initTinyMce(100);
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
