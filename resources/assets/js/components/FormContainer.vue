<template>
    <div class="row">
        <file-list></file-list>
        <file-editor></file-editor>
        <download-file></download-file>
    </div>
</template>

<script>
    import FileList from './FileListComponent'
    import FileEditor   from './FileEditorComponent'
    import Download from './FileDownloadComponent'
    export default {
        name: "FormContainer",
        components: { FileList, FileEditor, 'download-file': Download }
    }
</script>

<style scoped>

</style>