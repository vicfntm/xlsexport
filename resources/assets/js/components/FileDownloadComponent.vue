<template>
    <div class="col-md-12">
        <button class="btn btn-block download-file" @click="downloadFile">Выгрузить файл на диск компьютера</button>
    </div>
</template>

<script>
    import {bus} from '../app.js'
    export default {
        name: "FileDownloadComponent",
        data(){
            return{
                currentFile: ''
            }
        },
        created(){
            bus.$on('currentFile', (name)=>{
                this.currentFile = name;
            });
        },
        methods:{
            downloadFile(){
                if(this.currentFile){
                    axios({
                        url: '/loadfile',
                        method: 'POST',
                        responseType: 'blob',
                        data: {
                            file: this.currentFile
                        }
                    }).then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'file.xlsx');
                        document.body.appendChild(link);
                        link.click();
                    });
                }
            }
        }
    }
</script>

<style scoped>
    .download-file{
        background-color: #00E676;
        color: white;
        font-size: 18px;
    }
</style>