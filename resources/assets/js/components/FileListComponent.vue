<template>
    <div class="col-md-12">
        <h3>Загруженные файлы</h3>
        <div class="alert" :class="alertClass" role="alert" v-if="isAlertActive"> {{ AlertText }}</div>
        <div class="row"  v-for="file in availableFiles" >
            <div class="list-group col-md-11">
                <button type="button" class="list-group-item"@click="setFocusOnFile(file)">{{ file }}</button>
            </div>
            <div class="col-md-1">
                <div class="col-md-1"><span class="glyphicon glyphicon-remove file-remove" aria-hidden="true" @click="clearData(file)"></span></div>
            </div>
        </div>
        <div class="service-block">
            <div class="upload-file" @click="$refs.fileInput.click()">
                <input type="file" @change="onFileUpload($event)" ref="fileInput" class="hide-element">
                <span class="glyphicon glyphicon-plus upl-button" aria-hidden="true" title="Добавление файла (формат *.xls, *xlsx)" ></span>
            </div>
        </div>
    </div>
</template>

<script>
    import {bus} from '../app.js';
    import {initialData} from "../mixins/initialData";

    export default {
        name: "FileListComponent",
        data(){
            return{
                availableFiles: [],
                fileSelected: '',
                text: '',
                currentDataSet: [],
                isAlertActive: false,
                AlertText: '',
                alertClass: ''
            }
        },
        mixins: [initialData],
        methods:{
            onFileUpload($event){
                this.fileSelected = $event.target.files[0];
                const fd = new FormData();
                fd.append('fl', this.fileSelected, this.fileSelected.name);
                axios.post('/xls', fd)
                    .then(response=>{
                        response.data.code ? this.availableFiles.push(this.fileSelected.name) : '';
                        this.AlertText = response.data.statusText;
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        setTimeout(()=>{
                                this.AlertText = '';
                                this.isAlertActive = false;
                                this.alertClass = '';
                            }
                        , 3000);
                    })
                    .catch(e=>{
                        this.AlertText = 'Загрузка не удалась';
                        this.isAlertActive = true;
                        this.alertClass = 'alert-danger';
                        setTimeout(()=>{
                                this.AlertText = '';
                                this.isAlertActive = false;
                                this.alertClass = '';
                            }
                            , 3000);
                    });
            },
            setFocusOnFile(filename){
                bus.$emit('currentFile', filename);

            },
            clearData(name){
                axios.delete(`/clearData/${name}`)
                    .then(response=>{
                        this.AlertText = response.data.statusText;
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        setTimeout(()=>{
                                this.AlertText = '';
                                this.isAlertActive = false;
                                this.alertClass = '';
                            }
                            , 3000);
                        this.setInitial();
                        bus.$emit('reloadInstance', true);
                    })
                    .catch(e=>{
                        this.AlertText = response.data.statusText;
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        setTimeout(()=>{
                                this.AlertText = '';
                                this.isAlertActive = false;
                                this.alertClass = '';
                            }
                            , 3000);
                    });
            }
        },
        created(){
            this.setInitial();
        }
    }
</script>

<style scoped>
    .upload-file{
        width: 32px;
        position: relative;
        text-align: right;
        display: flex;
        justify-content: flex-start;
    }
    .upl-button{
        border-radius: 50%;
        font-size: 44px;
        background-color: #00E676;
        position: absolute;
        padding: 50%;
        color: white;
        cursor: pointer;
    }
    .service-block{
        height: 76px;
    }
    .hide-element{
        display: none;
    }
    .file-remove{
        font-size: 44px;
        cursor: pointer;
    }
</style>