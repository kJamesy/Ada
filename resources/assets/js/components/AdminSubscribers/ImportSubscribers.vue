<template>
    <div class="mt-5">
        <i class="fa fa-spinner fa-spin" v-if="fetchingData"></i>

        <template v-if="! fetchingData">
            <div v-if="appUserHasPermission('create')">
                <form v-if="! givingFeedback" v-on:submit.prevent='importResources'>
                    <div class="form-group row">
                        <label class="col-md-4 form-control-label">
                            Excel File ({{ allowedExtensions | arrToString }})
                            <br />
                            Expected columns: {{ allowedColumnNames | arrToString }}
                        </label>
                        <div class="col-md-8">
                            <label class="custom-file">
                                <input type="file" id="file" class="custom-file-input" v-on:change="fileInserted($event)" name="upload" v-bind:class="importErrors.file ? 'is-invalid' : ''">
                                <span class="custom-file-control"></span>
                            </label>
                            <div>
                                <small>
                                    <template v-if="upload.name">Name: {{ upload.name }} | </template>
                                    <template v-if="upload.type">Type: {{ upload.type }} | </template>
                                    <template v-if="upload.size">Size: {{ upload.size | convertToKb }} Kb</template>
                                </small>
                                <small class="text-danger">
                                    {{ importErrors.file }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row checkbox">
                        <div class="col-md-8 ml-md-auto">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" v-model="resources.active">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row" v-if="mailing_lists.length">
                        <label class="col-md-4 form-control-label">Mailing Lists</label>
                        <div class="col-md-8">
                            <v-select :options="sortedMailingLists" label="name" placeholder="Select Mailing Lists" v-model="selected_mailing_lists" multiple></v-select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 ml-md-auto">
                            <button type="submit" class="btn btn-primary btn-outline-primary" v-bind:disabled="! proceedWithUpload">Next</button>
                            <router-link v-bind:to="{ name: 'admin_subscribers.index' }" class="btn btn-link pull-right">Cancel</router-link>
                        </div>
                    </div>
                </form>
                <div v-if="givingFeedback">
                    <p>There were {{ rowsCount }} rows in the uploaded file.</p>
                    <div v-if="existingRows.length" class="text-danger">
                        <p>Rows with email addresses that are taken: {{ existingRows.length }} </p>
                        <p>Please check the following row numbers for taken email addresses:<br /> {{ existingRows | arrToString }} </p>
                    </div>
                    <div v-if="badRows.length" class="text-danger">
                        <p>Rows with errors: {{ badRows.length }} </p>
                        <p>Please check the following row numbers for bad names/email:<br /> {{ badRows | arrToString }} </p>
                    </div>
                    <div class="form-group">
                        <label for="finish_import">Select Next Step</label>
                        <select class="custom-select form-control" v-model="finalStep" id="finish_import">
                            <option value="" disabled>Next Step</option>
                            <option value="proceed" v-if="rowsCount - badRows.length">Save Good Rows
                                ({{ rowsCount - badRows.length - existingRows.length }}), Attach Existing Rows ({{ existingRows.length }})</option>
                            <option value="cancel">Cancel Import</option>
                        </select>
                        <small class="text-danger">
                            {{ nextStepValidation }}
                        </small>
                    </div>
                    <button v-on:click.prevent="finaliseImport" class="btn btn-primary btn-outline-primary">Finish</button>
                </div>
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
            });
        },
        data() {
            return {
                fetchingData: true,
                resources: {file: {name: '', size: 0, type: ''}, active: 1, mailing_lists: []},
                importErrors: {file: ''},
                mailing_lists: [],
                selected_mailing_lists: [],
                upload: {name: '', size: '', type: ''},
                proceedWithUpload: false,
                allowedExtensions: ['xls', 'xlt', 'xlsx'],
                allowedColumnNames: ['first_name', 'last_name', 'email'],
                givingFeedback: false,
                fileName: '',
                rowsCount: 0,
                existingRows: [],
                badRows: [],
                finalStep: '',
                nextStepValidation: ''
            }
        },
        computed: {
            sortedMailingLists() {
                return _.sortBy(this.mailing_lists, ['name']);
            },
            flattenedMLists() {
                return _.flatMapDeep(this.selected_mailing_lists, function(mList) {
                    return mList.id;
                });
            }
        },
        methods: {
            goTime() {
                let vm = this;
                let progress = vm.$Progress;

                progress.start();
                vm.appClearValidationErrors();

                vm.$http.get(vm.appResourceUrl + '/create').then(function(response) {
                    if ( response.data && response.data.mailing_lists && response.data.mailing_lists.length )
                        vm.mailing_lists = response.data.mailing_lists;

                    progress.finish();
                    vm.fetchingData = false;

                    if( typeof FormData === 'undefined' ) {
                        swal({ title: 'Uh oh!', text: 'Your browser does not support this upload. Please use a modern browser.', type: 'error', animation: 'slide-from-top'}, function() {
                            vm.$router.push({
                                name: 'admin_subscribers.create'
                            });
                        });
                    }

                }, function(error) {
                    if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();

                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            importResources() {
                let vm = this;
                let progress = vm.$Progress;
                let formData = new FormData();

                formData.append('upload', $("input[name='upload']")[0].files[0]);
                formData.append('importing', 1);

                progress.start();
                vm.fetchingData = true;
                vm.clearImportErrors();

                vm.$http.post(vm.appResourceUrl, formData).then(function(response) {
                    if ( response.data.success && response.data.fileName ) {
                        vm.fileName = response.data.fileName;
                        vm.rowsCount = response.data.rowsCount;
                        vm.existingRows = response.data.existingRows;
                        vm.badRows = response.data.badRows;
                        vm.givingFeedback = true;
                    }

                    progress.finish();
                    vm.fetchingData = false;
                }, function(error) {
                    if ( error.status && error.status === 422 && error.data ) {
                        vm.appValidationErrorAlert();

                        _.forEach(error.data, function(message, field) {
                            vm.$set(vm.importErrors, field, message[0]);
                        });
                    }
                    else if ( error.status && error.status === 403 && error.data )
                        vm.appCustomErrorAlert(error.data.error);
                    else
                        vm.appGeneralErrorAlert();

                    progress.fail();
                    vm.fetchingData = false;
                });
            },
            finaliseImport() {
                let vm = this;
                if ( ! vm.finalStep.length )
                    vm.nextStepValidation = "Please first select how you want to proceed";
                else {
                    let progress = vm.$Progress;
                    let data = {
                        importing: 1,
                        finalising: 1,
                        action: vm.finalStep,
                        fileName: vm.fileName,
                        active: vm.resources.active,
                        mailing_lists: vm.resources.mailing_lists
                    };

                    progress.start();
                    vm.nextStepValidation = '';
                    vm.fetchingData = true;

                    vm.$http.post(vm.appResourceUrl, data).then(function(response) {
                        progress.finish();
                        if ( response.data.success ) {
                            let message = response.data.succeededNum + " subscribers successfully created.";

                            if ( response.data.existingNum )
                                message += " " + response.data.existingNum + " already existed and have been attached to the selected mailing list.";

                            if ( response.data.failedNum )
                                message += " " + response.data.failedNum + " could not be stored due to missing names or bad email.";

                            swal({ title: "Success", text: message, type: 'success', animation: 'slide-from-bottom'}, function() {
                                vm.$router.push({
                                    name: 'admin_subscribers.create',
                                });
                            });
                        }
                        else if ( response.data.cancellation ) {
                            swal({ title: "Success", text: response.data.cancellation, type: 'success', animation: 'slide-from-bottom'}, function() {
                                vm.$router.push({
                                    name: 'admin_subscribers.create',
                                });
                            });
                        }

                        vm.fetchingData = false;
                        progress.finish();
                    }, function(error) {
                        let message = ( error.status && error.status === 422 && error.data ) ? error.data.no_good_records : 'Please refresh the page and try again.';

                        swal({ title: "An Error Occurred", text: message, type: 'error', animation: 'slide-from-top'}, function() {
                            vm.$router.push({
                                name: 'admin_subscribers.create',
                            });
                        });

                        vm.fetchingData = false;
                        progress.fail();
                    });

                }
            },
            fileInserted(event) {
                let vm = this;
                let upload = event.target.files[0];

                if ( upload )
                    vm.$set(vm, 'upload', { name: upload.name, size: upload.size, type: upload.type } );

                if( upload.name && this.hasAllowedExtension(upload.name) ) {
                    vm.$set(vm.resources, 'file', { name: upload.name, size: upload.size, type: upload.type } );
                    vm.proceedWithUpload = true;
                    vm.clearImportErrors();
                }
                else
                    vm.importErrors.file = 'This file is not allowed';
            },
            hasAllowedExtension(filename) {
                let pass = false;
                let loop = true;

                _.forEach(this.allowedExtensions, function(ext) {
                    let theExt = '.' + _.toLower(ext);

                    if ( loop ) {
                        if ( _.endsWith(_.toLower(filename), theExt) ) {
                            pass = true;
                            loop = false;
                        }
                    }
                });

                return pass;
            },
            clearImportErrors() {
                let vm = this;
                _.forEach(vm.importErrors, function (err, attr) {
                    vm.$set(vm.importErrors, attr, '');
                });
            },
        },
        watch: {
            'selected_mailing_lists': function(newVal) {
                this.resources.mailing_lists = this.flattenedMLists;
            },

        },
        filters: {
            convertToKb(bytes) {
                return _.round(bytes/1024, 2);
            },
            arrToString(arr) {
                return _.join(arr, ', ');
            }
        }
    }
</script>