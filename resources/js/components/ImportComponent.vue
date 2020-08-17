<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    
                    <div v-if="success === ''">
                        <div class="card-header">Step 1 - File Upload</div>
                        <div class="card-body">
                            <form @submit="formSubmit" enctype="multipart/form-data">
                            <strong>File:</strong>
                            <input type="file" class="form-control" v-on:change="onFileChange">
                            <button class="btn btn-success">Upload</button>
                            </form>
                        </div>
                    </div> 
                    <div v-if="success == '200'">
                        <div class="card-header">Step 2 - Define custom fields</div>
                        <form @submit="importData">
                            <input type="hidden" v-bind:value="csv_id"/>
                            <ul>
                                <li v-for="(item, index) of headers">
                                    <input type="text" v-bind:value="item" disabled="disabled"/> - {{ noUnderscore(item) }}
                                </li>
                                <li v-for="(item, index) of custom_headers">
                                    <input type="text" v-bind:value="item" disabled="disabled"/>
                                    <input type="text" v-model="form[item]" required="required" />
                                </li>
                            </ul>  
                            <button>Import</button>
                        </form>
                        <table id="csv_data">
                            <thead>
                                <tr>
                                    <td v-for="(item, index) of headers">
                                        {{item}}
                                    </td>
                                    <td v-for="(item, index) of custom_headers">
                                        {{form[item]}}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(csv, index) of csv_data">
                                    <td v-for="(item, index) of csv">
                                        {{ item }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="success == '201'">
                        <div class="card-header">Imported Success</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
              file: '',
              success: '',
              headers: [],
              custom_headers: [],
              form: {},
              csv_id: ''
            };
        },
        methods: {
            importData(e) {
                e.preventDefault();
                const formData = new FormData()
                let currentObj = this;  
                Object.keys(this.form).forEach(e => {
                    formData.append(e, this.form[e]);
                })
                formData.append('csv_id', this.csv_id);
                axios.post('/api/import-data', formData)
                .then(function (response) {
                    currentObj.success = response.status;
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            },
            noUnderscore(str) {
                let i, frags = str.split('_');
                for (i=0; i<frags.length; i++) {
                    frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
                }
                return frags.join(' ');
            },
            onFileChange(e){
                this.file = e.target.files[0];
            },
            formSubmit(e) {
                e.preventDefault();
                let currentObj = this;
   
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }
    
                let formData = new FormData();
                formData.append('csv_file', this.file);
   
                axios.post('/api/imports', formData, config)
                .then(function (response) {
                    currentObj.success = response.status;
                    currentObj.headers = response.data.csv_headers;
                    currentObj.custom_headers = response.data.csv_custom_headers;
                    currentObj.csv_data = response.data.csv_data;
                    currentObj.csv_id = response.data.csv_id;
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            }
        }
    }
</script>