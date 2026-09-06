@props([
    'name' => 'file',
    'id' => null,
    'label' => null,
    'helper' => null,
    'multiple' => false,
    'accept' => 'image/*',
    'maxSize' => 10, // MB
    'existingFiles' => [],
    'required' => false,
    'circle' => false,
    'avatar' => false,
])

@php
    $isCircle = $circle || $avatar;
    $uploaderId = $id ?? 'filepond_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . uniqid();
    $isAr = app()->getLocale() === 'ar';
    $normalizedExisting = is_array($existingFiles) ? $existingFiles : ($existingFiles ? [$existingFiles] : []);
    
    // Normalize accepted types for FilePond
    $acceptedTypesList = [];
    if (str_contains($accept, 'image/*')) {
        $acceptedTypesList = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml'];
    } elseif ($accept) {
        $acceptedTypesList = array_map('trim', explode(',', $accept));
    }
@endphp

@once
    <!-- FilePond Core & Plugin Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/filepond/filepond.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/filepond/filepond-plugin-image-preview.min.css') }}">
    
    <!-- Filament FileUpload Custom Theme -->
    <style>
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
        }
        .filepond--panel-root {
            border-radius: 1rem;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        html.dark .filepond--panel-root,
        [data-theme="dark"] .filepond--panel-root {
            background-color: rgba(15, 23, 42, 0.45);
            border-color: #334155;
        }
        .filepond--root:hover .filepond--panel-root {
            border-color: #0A4F78;
            background-color: rgba(10, 79, 120, 0.03);
            box-shadow: 0 4px 12px rgba(10, 79, 120, 0.08);
        }
        html.dark .filepond--root:hover .filepond--panel-root,
        [data-theme="dark"] .filepond--root:hover .filepond--panel-root {
            border-color: #2A8FC2;
            background-color: rgba(42, 143, 194, 0.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .filepond--drop-label {
            color: #475569;
            cursor: pointer;
            min-height: 5.5rem;
        }
        html.dark .filepond--drop-label,
        [data-theme="dark"] .filepond--drop-label {
            color: #94a3b8;
        }
        .filepond--drop-label label {
            cursor: pointer;
            font-size: 0.875rem;
            line-height: 1.5;
            font-weight: 500;
        }
        .filepond--label-action {
            color: #0A4F78;
            font-weight: 700;
            text-decoration: underline;
            text-decoration-color: rgba(10, 79, 120, 0.4);
            text-underline-offset: 2px;
            transition: all 0.15s;
        }
        html.dark .filepond--label-action,
        [data-theme="dark"] .filepond--label-action {
            color: #38bdf8;
            text-decoration-color: rgba(56, 189, 248, 0.4);
        }
        .filepond--item-panel {
            border-radius: 0.75rem;
            background-color: #0A4F78;
        }
        .filepond--file-action-button {
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            transition: transform 0.15s ease;
        }
        .filepond--file-action-button:hover {
            transform: scale(1.1);
        }
        .filepond--credits {
            display: none !important;
        }
        
        /* Circle Avatar Layout */
        .filepond--root.is-circle {
            width: 170px;
            height: 170px;
            margin: 0 auto;
        }
        .filepond--root.is-circle .filepond--panel-root {
            border-radius: 50% !important;
        }
        .filepond--root.is-circle .filepond--image-preview-wrapper {
            border-radius: 50% !important;
        }
        .filepond--root.is-circle .filepond--image-preview {
            border-radius: 50% !important;
        }
        .filepond--root.is-circle .filepond--image-clip {
            border-radius: 50% !important;
        }
        .filepond--root.is-circle .filepond--drop-label {
            min-height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
        }
    </style>

    <!-- FilePond Scripts & Plugins -->
    <script src="{{ asset('assets/vendor/filepond/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/filepond/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/filepond/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/filepond/filepond.min.js') }}"></script>
    
    <script>
        // Register FilePond plugins once
        if (typeof FilePond !== 'undefined') {
            FilePond.registerPlugin(
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType,
                FilePondPluginImagePreview
            );
        }
    </script>
@endonce

<div class="file-uploader-wrapper mb-4" id="{{ $uploaderId }}_wrapper">
    @if($label)
        <label class="form-label block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-200 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="{{ $isCircle ? 'flex flex-col items-center justify-center p-2' : '' }}">
        <input type="file"
               name="{{ $multiple ? $name . '[]' : $name }}"
               id="{{ $uploaderId }}_input"
               class="filepond {{ $isCircle ? 'is-circle' : '' }}"
               accept="{{ $accept }}"
               {{ $multiple ? 'multiple' : '' }}
               {{ $required && empty($normalizedExisting) ? 'required' : '' }}
               data-max-file-size="{{ $maxSize }}MB">
    </div>

    @if($helper)
        <p class="text-xs text-slate-400 mt-2 {{ $isCircle ? 'text-center' : '' }}">
            <i class="fa-solid fa-circle-info mr-1 ml-1 opacity-70"></i> {{ $helper }}
        </p>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputElement = document.getElementById('{{ $uploaderId }}_input');
        if (!inputElement || typeof FilePond === 'undefined') return;

        const isAr = {{ $isAr ? 'true' : 'false' }};
        const isCircle = {{ $isCircle ? 'true' : 'false' }};
        const existingFilesList = @json($normalizedExisting);
        const acceptedTypes = @json($acceptedTypesList);

        const pondFiles = [];
        if (existingFilesList && existingFilesList.length > 0) {
            existingFilesList.forEach(url => {
                if (url && typeof url === 'string') {
                    pondFiles.push({
                        source: url,
                        options: {
                            type: 'local'
                        }
                    });
                }
            });
        }

        const pondOptions = {
            storeAsFile: true,
            allowMultiple: {{ $multiple ? 'true' : 'false' }},
            maxFileSize: '{{ $maxSize }}MB',
            checkValidity: true,
            credits: false,
            stylePanelLayout: isCircle ? 'compact circle' : null,
            imagePreviewHeight: isCircle ? 170 : 180,
            imageCropAspectRatio: isCircle ? '1:1' : null,
            imageResizeTargetWidth: isCircle ? 300 : null,
            imageResizeTargetHeight: isCircle ? 300 : null,
            styleLoadIndicatorPosition: 'center bottom',
            styleProgressIndicatorPosition: isCircle ? 'center bottom' : 'right bottom',
            styleButtonRemoveItemPosition: isCircle ? 'center bottom' : 'left bottom',
            styleButtonProcessItemPosition: 'right bottom',
            
            // Localization
            labelIdle: isCircle 
                ? (isAr 
                    ? '<i class="fa-solid fa-camera text-2xl mb-1 text-primary block"></i><span class="filepond--label-action font-bold text-xs">رفع صورة</span>' 
                    : '<i class="fa-solid fa-camera text-2xl mb-1 text-primary block"></i><span class="filepond--label-action font-bold text-xs">Upload Photo</span>')
                : (isAr 
                    ? '<i class="fa-solid fa-cloud-arrow-up text-xl mb-1 text-primary block"></i>اسحب وأفلت الملفات هنا أو <span class="filepond--label-action">استعرض من جهازك</span>' 
                    : '<i class="fa-solid fa-cloud-arrow-up text-xl mb-1 text-primary block"></i>Drag & Drop files or <span class="filepond--label-action">Browse</span>'),
            labelInvalidField: isAr ? 'الحقل يحتوي على ملفات غير صالحة' : 'Field contains invalid files',
            labelFileWaitingForSize: isAr ? 'جاري التحقق من الحجم...' : 'Waiting for size',
            labelFileSizeNotAvailable: isAr ? 'الحجم غير متوفر' : 'Size not available',
            labelFileLoading: isAr ? 'جاري التحميل...' : 'Loading',
            labelFileLoadError: isAr ? 'خطأ أثناء التحميل' : 'Error during load',
            labelFileProcessing: isAr ? 'جاري الرفع...' : 'Uploading',
            labelFileProcessingComplete: isAr ? 'اكتمل التجهيز' : 'Ready',
            labelFileProcessingAborted: isAr ? 'تم الإلغاء' : 'Upload cancelled',
            labelFileProcessingError: isAr ? 'خطأ أثناء المعالجة' : 'Error',
            labelTapToCancel: isAr ? 'انقر للإلغاء' : 'tap to cancel',
            labelTapToRetry: isAr ? 'انقر للمحاولة ثانية' : 'tap to retry',
            labelTapToUndo: isAr ? 'انقر للتراجع' : 'tap to undo',
            labelButtonRemoveItem: isAr ? 'إزالة' : 'Remove',
            labelButtonAbortItemLoad: isAr ? 'إلغاء' : 'Abort',
            labelButtonRetryItemLoad: isAr ? 'إعادة' : 'Retry',
            labelMaxFileSizeExceeded: isAr ? 'الملف أكبر من الحد المسموح به' : 'File is too large',
            labelMaxFileSize: isAr ? 'الحد الأقصى لحجم الملف هو {filesize}' : 'Maximum file size is {filesize}',
            labelFileTypeNotAllowed: isAr ? 'نوع الملف غير مدعوم' : 'File of invalid type',
            fileValidateTypeLabelExpectedTypes: isAr ? 'الأنواع المدعومة: {allTypes}' : 'Expects: {allTypes}',
        };

        if (acceptedTypes.length > 0) {
            pondOptions.acceptedFileTypes = acceptedTypes;
        }

        if (pondFiles.length > 0) {
            pondOptions.files = pondFiles;
        }

        const pond = FilePond.create(inputElement, pondOptions);

        // Feedback toast integration if available
        pond.on('addfile', (error, file) => {
            if (error) {
                if (window.toast) {
                    window.toast.error(error.main || error.sub || (isAr ? 'تعذر رفع الملف المحدد' : 'Failed to add file'));
                }
            }
        });
    });
</script>
