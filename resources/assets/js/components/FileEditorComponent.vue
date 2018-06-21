<template>
    <div class="col-md-12 mb4">
        <h3>Редактирование записей</h3>
        <div class="alert" :class="alertClass" role="alert" v-if="isAlertActive"> {{ AlertText }}</div>
        <div class="row">
            <div class="col-md-10">
                <span class="col-md-2 bolden" v-for="(element, index) in header" :key="index">{{ element }}</span>
            </div>
        </div>
        <div class="row"  v-for="(row, index) in rows" :key="index">
            <div class="col-md-10">
                <div class="col-md-2 editor-input" v-for="(column, elem) in columns">
                    <input type="text" class="form-control editor-input" aria-label="..." v-model="rows[index][column]">
                </div>
            </div>
            <div class="col-md-1"><span class="glyphicon glyphicon-ok" aria-hidden="true" @click="saveData(rows[index], rows[index].id)"></span></div>
            <div class="col-md-1"><span class="glyphicon glyphicon-remove" aria-hidden="true" @click="deleteData(rows[index], rows[index].id)"></span></div>
        </div>
    </div>
</template>

<script>
    import {bus} from '../app.js'
    export default {
        name: "FileEditorComponent",
        data(){
            return{
                header: ['Фамилия', 'Имя', 'Отчество', 'Год рождения', 'Должность', 'Зп. в год'],
                columns: ['first_name', 'middle_name', 'surname', 'birth_year', 'occupation', 'annual_salary'],
                currentFileName: '',
                rows: [],
                AlertText: '',
                alertClass: '',
                isAlertActive: false
            }
        },
        created(){
            bus.$on('currentFile', (name)=>{
                this.currentFileName = name;
            });
            bus.$on('reloadInstance', (val)=>{
               this.rows = '';
            });
        },
        watch:{
            currentFileName() {
                axios.get('/xls/' + this.currentFileName)
                    .then(response=>{
                        this.rows = response.data;
                    })
                    .catch(e=>{
                    })
            }
        },
        methods: {
            saveData(row, id){
                axios.put(`/xls/${id}`, {row})
                    .then(response=>{
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        this.AlertText = response.data.statusText;
                        setTimeout(()=>{
                            this.isAlertActive = false;
                            this.alertClass = '';
                            this.AlertText = '';
                        }, 3000);
                    })
                    .catch(e=>{
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        this.AlertText = response.data.statusText;
                        setTimeout(()=>{
                            this.isAlertActive = false;
                            this.alertClass = '';
                            this.AlertText = '';
                        }, 3000);
                    })
            },
            deleteData(position, id){
                axios.delete(`/xls/${id}`)
                    .then(response=>{
                        this.rows.splice(this.rows.position, 1);
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        this.AlertText = response.data.statusText;
                        setTimeout(()=>{
                            this.isAlertActive = false;
                            this.alertClass = '';
                            this.AlertText = '';
                        }, 3000);
                    })
                    .catch(e=>{
                        this.isAlertActive = true;
                        this.alertClass = response.data.class;
                        this.AlertText = response.data.statusText;
                        setTimeout(()=>{
                            this.isAlertActive = false;
                            this.alertClass = '';
                            this.AlertText = '';
                        }, 3000);
                    })
            }
        }
    }
</script>

<style scoped>
    .glyphicon-ok, .glyphicon-remove{
        cursor: pointer;
    }
    .bolden{
        font-weight: 700;
    }
    .editor-group{
        display: inline;
    }
    .editor-input{
        border-radius: 0;
    }
    /*calc only just a little reminder that mb1 = 4px */
    .mb4{
        margin-bottom: calc( 4px * 4 );
    }
</style>