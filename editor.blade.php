<!doctype html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Expires" content="Mon, 06 Jan 1990 00:00:01 GMT">
        <meta http-equiv="Expires" content="-1">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{config('variables.site_name')}}</title>

        <link rel="apple-touch-icon" href={{ asset("theme-assets/images/ico/apple-icon-120.png") }}>
        <link rel="shortcut icon" type="image/x-icon" href={{ asset("theme-assets/images/ico/favicon.ico") }}>

        <script src="/theme-assets/jquery.min.js" type="text/javascript"></script>
        <link href="{{asset("theme-assets/bootstrap/bootstrap.min.css")}}" rel="stylesheet">
        <script src="{{asset("theme-assets/bootstrap/bootstrap.min.js")}}"></script>

        <!-- include summernote css/js -->
        <link href="{{asset("theme-assets/summernote/summernote.min.css")}}" rel="stylesheet">
        <script src="{{asset("theme-assets/summernote/summernote.min.js")}}"></script>

        <style>
            * {
                margin: 0 auto;
                padding: 0px;
            }

            body, html {
                height: 100%;
                overflow: hidden; /* 스크롤 제거 */
            }
        </style>
    </head>
    <body>
        <form method="post" id="editor_form">
            <textarea id="summernote" name="editordata"></textarea>
        </form>
    </body>

    <script>
        function initializeSummernote() {
            // 툴바 높이를 동적으로 계산
            const toolbarHeight = $('.note-toolbar').outerHeight() || 50; // 툴바 높이 (기본값 50px)
            const windowHeight = $(window).height();

            // Summernote 초기화
            $('#summernote').summernote({
                height: windowHeight - toolbarHeight, // 툴바 높이를 제외한 영역 계산
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']], // 텍스트 스타일
                    ['font', ['fontname', 'fontsize', 'color']], // 글꼴 크기 및 색상
                    ['para', ['ul', 'ol', 'paragraph']], // 목록 및 단락
                    ['insert', ['link', 'picture', 'video']], // 링크 및 이미지 삽입 (동영상, 코드뷰 제거)
                    ['view', ['fullscreen']] // 전체 화면
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        uploadImage(files[0]); // 첫 번째 이미지만 업로드
                    },
                    onMediaInsert: function (node) {
                        if (node.tagName === 'IFRAME') {
                            node.classList.add('note-video-clip'); // 동영상에 클래스 추가
                        }
                    }
                }
            });
        }

        $(document).ready(function () {
            initializeSummernote(); // 초기화

            // 창 크기 변경 시 재초기화
            $(window).resize(function () {
                $('#summernote').summernote('destroy'); // 기존 인스턴스를 제거
                initializeSummernote(); // 다시 초기화
            });
        });

        function editor_post()
        {
            const form = $('#editor_form')[0];
            const formData = new FormData(form);

            $.ajax({
                url: '/web/ajax/editor/store', // 이미지 업로드를 처리할 서버의 엔드포인트
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {

                },
                error: function (xhr) {

                }
            });
        }

        function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);

            $.ajax({
                url: '/web/ajax/upload_image', // 이미지 업로드를 처리할 서버의 엔드포인트
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    // 서버에서 반환된 이미지 URL을 에디터에 삽입
                    $('#summernote').summernote('insertImage', response.url);
                },
                error: function (xhr) {
                    console.error('Image upload failed:', xhr.responseText);
                    alert('이미지 업로드 실패!');
                }
            });
        }
    </script>
</html>
