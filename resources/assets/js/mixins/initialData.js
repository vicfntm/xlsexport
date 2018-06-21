export const initialData = {
    methods:{
        setInitial(){
            axios.get('/xls')
                .then(response=>{
                    this.availableFiles = response.data.titles;
                })
                .catch(e=>{
                    console.log(e);
                })
            }
        }
    };
