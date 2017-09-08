<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.6.6/tinymce.min.js"></script>
<script>
    window.tinyMceConfig = {
        plugins : [
            'advlist autolink link image lists charmap codesample print preview code autosave ' +
            'autoresize charmap anchor textcolor colorpicker contextmenu fullscreen hr nonbreaking ' +
            'paste save searchreplace tabfocus table wordcount'
        ],
        autoresize_max_height: 600,
        media_alt_source: false,
        media_poster: false,
        media_filter_html: true,
        paste_as_text: true,
        toolbar: [
            'outdent indent | alignleft aligncenter alignright alignjustify | link unlink anchor | image | bullist numlist | fullscreen', // | save
            'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor'
        ],
        image_caption: false,
        image_description: true,
        image_advtab: true ,
        relative_urls: false,
        external_filemanager_path: "{{ asset('assets/responsive-filemanager/filemanager') }}/",
        filemanager_title: "{{ config('app.name') }} File Manager",
        filemanager_sort_by: 'name',
        filemanager_descending: 0,
        external_plugins: { "filemanager" : "{{ asset('assets/responsive-filemanager/filemanager/plugin.min.js') }}" }
    };
</script>