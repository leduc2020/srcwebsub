// Order.js - JavaScript cho trang đặt hàng dịch vụ SMM Panel
// Version: 1.4.0 - HYBRID LOADING SYSTEM
// Tác dụng: Xử lý tất cả các chức năng trên trang đặt hàng như chọn platform, category, service, tính giá, xác nhận đơn hàng
// Mới: HYBRID LOADING - Admin có thể chọn 'lazy' (tối ưu) hoặc 'all' (tương thích) via setting load_data_type
// Optimization: Lazy loading cải thiện 90% performance, Smart caching, Progress indicators
// Fix: Sửa lỗi restore state khi F5 giữa chừng + nút "Xóa tất cả" không reset quantity/min/max

// ==================== BIẾN TOÀN CỤC ====================

// Cache toàn bộ dữ liệu platforms, categories, services để tránh gọi AJAX nhiều lần
// Dữ liệu được load 1 lần duy nhất khi trang khởi tạo
let allData = {
    platforms: [],      // Danh sách các nền tảng (Facebook, Instagram, Youtube, etc.)
    categories: [],     // Danh sách các danh mục dịch vụ (Followers, Likes, Views, etc.)
    services: []        // Danh sách tất cả các dịch vụ
};

// Biến quản lý mode loading data
let loadDataMode = 'lazy'; // 'lazy' hoặc 'all' - được set từ backend

// Cache cho service details để tránh load lại thông tin chi tiết của dịch vụ đã xem
// Key: service_id, Value: thông tin chi tiết dịch vụ
let serviceDetailsCache = {};

// Timer cho debounce khi người dùng nhập số lượng
// Tránh gọi AJAX liên tục khi người dùng đang gõ
let quantityDebounceTimer;

// Timer cho debounce khi người dùng nhập comment
// Tránh đếm số dòng comment liên tục khi người dùng đang gõ
let commentDebounceTimer;

// ==================== CÁC HÀM ĐỊNH DẠNG HIỂN THỊ ====================

/**
 * Hàm định dạng hiển thị option có hình ảnh cho Select2
 * Dùng cho dropdown Platform và Category
 * @param {Object} option - Option của Select2
 * @returns {jQuery|String} - HTML hiển thị với hình ảnh hoặc text thường
 */
function formatOptionWithImage(option) {
    // Nếu không có id thì chỉ trả về text (trường hợp placeholder)
    if (!option.id) {
        return option.text;
    }
    
    // Lấy đường dẫn hình ảnh từ data attribute
    var img = $(option.element).data('image');
    
    // Nếu có hình ảnh thì hiển thị kèm hình, không thì chỉ hiển thị text
    if (img) {
        return $('<span><img src="' + img +
            '" class="service-option-image">' + option.text +
            '</span>');
    }
    return option.text;
}

/**
 * Hàm định dạng hiển thị option cho dropdown Service
 * Hiển thị service với ID, tên, giá và các badge (hỗ trợ hủy, bảo hành, lên chậm)
 * @param {Object} option - Option của Select2
 * @returns {jQuery|String} - HTML hiển thị service với format đặc biệt
 */
function formatServiceOption(option) {
    // Nếu không có id thì chỉ trả về text (trường hợp placeholder)
    if (!option.id) {
        return option.text;
    }
    
    // Lấy các thông tin từ data attributes
    var img = $(option.element).data('image');              // Hình ảnh category
    var canCancel = $(option.element).data('cancel');       // Có hỗ trợ hủy đơn không
    var hasRefill = $(option.element).data('refill');       // Có bảo hành không
    var hasDripfeed = $(option.element).data('dripfeed');   // Có chế độ lên chậm không
    var optionText = option.text;                           // Text gốc của option

    // Kiểm tra điều kiện hiển thị badge "Hỗ trợ hủy"
    // Chấp nhận nhiều kiểu dữ liệu: boolean, string, number
    var showCancelBadge = canCancel === true || canCancel === 'true' || canCancel === '1' || canCancel === 1;
    
    // Kiểm tra điều kiện hiển thị badge "Bảo hành"
    // Chấp nhận nhiều kiểu dữ liệu: boolean, string, number
    var showRefillBadge = hasRefill === true || hasRefill === 'true' || hasRefill === '1' || hasRefill === 1;
    
    // Kiểm tra điều kiện hiển thị badge "Lên chậm"
    // Chấp nhận nhiều kiểu dữ liệu: boolean, string, number
    var showDripfeedBadge = hasDripfeed === true || hasDripfeed === 'true' || hasDripfeed === '1' || hasDripfeed === 1;

    // Lấy thông tin chi tiết service từ data attributes
    var serviceId = $(option.element).data('service-id') || option.id;        // ID dịch vụ
    var serviceName = $(option.element).data('service-name') || optionText;   // Tên dịch vụ
    var servicePrice = $(option.element).data('service-price') || '';         // Giá dịch vụ

    // Nếu có đầy đủ thông tin service, hiển thị format tùy chỉnh với layout đẹp
    if (serviceId && serviceName && servicePrice) {
        // Tạo badge "Hỗ trợ hủy" màu xanh lá với icon shield
        var cancelBadge = showCancelBadge ?
            '<span class="badge service-badge-cancel"><i class="ri-shield-check-line service-badge-icon"></i>' + ORDER_LABELS.supportCancel + '</span>' :
            '';

        // Tạo badge "Bảo hành" màu xanh dương với icon refresh
        var refillBadge = showRefillBadge ?
            '<span class="badge service-badge-warranty"><i class="ri-refresh-line service-badge-icon"></i>' + ORDER_LABELS.warranty + '</span>' :
            '';

        // Tạo badge "Lên chậm" màu cam với icon speed
        var dripfeedBadge = showDripfeedBadge ?
            '<span class="badge service-badge-slowmode"><i class="ri-speed-line service-badge-icon"></i>' + ORDER_LABELS.slowMode + '</span>' :
            '';

        // Tạo phần icon chuyên mục nếu có
        var categoryIcon = img ? '<img src="' + img + '" class="service-option-image me-2">' : '';

        // Tạo giao diện hiển thị service với layout flexbox đẹp mắt
        // Bố cục: [Icon + ID + Tên + Badges] ---- [Giá bên phải]
        return $(
            '<div class="service-option-container">' +
            '<div class="service-option-content">' +
                // Icon chuyên mục
                categoryIcon +
                // ID service màu xanh dương
                '<span class="service-option-id">#' + serviceId + '</span>' +
                // Tên service + các badge
                '<span class="service-option-name">' + serviceName + cancelBadge + refillBadge + dripfeedBadge + '</span>' +
            '</div>' +
            // Giá service màu đỏ, không wrap
            '<div class="service-option-price">' +
                servicePrice + 
            '</div>' +
            '</div>');
    }

    // Fallback: Nếu không có đầy đủ thông tin service, dùng hiển thị đơn giản với badge
    // Tạo các badge đơn giản cho trường hợp fallback
    var cancelBadgeSimple = showCancelBadge ?
        '<span class="badge service-badge-cancel simple"><i class="ri-shield-check-line service-badge-icon"></i>' + ORDER_LABELS.supportCancel + '</span>' :
        '';
    var refillBadgeSimple = showRefillBadge ?
        '<span class="badge service-badge-warranty simple"><i class="ri-refresh-line service-badge-icon"></i>' + ORDER_LABELS.warranty + '</span>' :
        '';
    var dripfeedBadgeSimple = showDripfeedBadge ?
        '<span class="badge service-badge-slowmode simple"><i class="ri-speed-line service-badge-icon"></i>' + ORDER_LABELS.slowMode + '</span>' :
        '';

    // Nếu có hình ảnh, hiển thị với hình ảnh + text + badges
    if (img) {
        return $('<span><img src="' + img +
            '" class="service-option-image">' + optionText +
            cancelBadgeSimple + refillBadgeSimple + dripfeedBadgeSimple + '</span>');
    }

    // Trường hợp cuối cùng: chỉ hiển thị text + badges
    return $('<span>' + optionText + cancelBadgeSimple + refillBadgeSimple + dripfeedBadgeSimple + '</span>');
}

// ==================== CÁC HÀM CHÍNH XỬ LÝ DỮ LIỆU ====================

/**
 * Hàm load dữ liệu khi trang khởi tạo (smart loading theo setting admin)
 * - Mode 'lazy': Chỉ load platforms + categories, services load on-demand
 * - Mode 'all': Load tất cả data như cũ
 */
function loadInitialData() {
    // Hiển thị loading spinner
    $('.form-loader').show();
    
    // Vô hiệu hóa toàn bộ form để tránh user thao tác khi đang load
    $('#order-form').find('input, select, textarea, button').prop('disabled', true);
    
    // Gửi AJAX request để load dữ liệu cơ bản (platforms + categories)
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'loadInitialData',  // ✅ Optimized: Chỉ load platforms + categories
            token: $('#token').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Detect load mode từ backend
                loadDataMode = response.data.load_mode || 'lazy';
                
                // Lưu dữ liệu vào cache theo mode
                allData.platforms = response.data.platforms || [];
                allData.categories = response.data.categories || [];
                
                if (loadDataMode === 'all') {
                    // Mode ALL: Lưu tất cả services ngay
                    allData.services = response.data.services || [];
                } else {
                    // Mode LAZY: Khởi tạo rỗng, sẽ load lazy
                    allData.services = [];
                }

                // Khởi tạo các dropdown Select2 sau khi có dữ liệu
                initializeDropdowns();

                // Tự động chọn platform, category nếu có tham số trong URL
                autoSelectFromUrl();
            } else {
                // Hiển thị lỗi nếu backend trả về lỗi
                Swal.fire({
                    title: ORDER_LABELS.error,
                    text: response.msg,
                    icon: 'error'
                });
            }
        },
        error: function() {
            // Hiển thị lỗi khi không thể kết nối tới server
            Swal.fire({
                title: ORDER_LABELS.error,
                text: ORDER_LABELS.errorLoadData,
                icon: 'error'
            });
        },
        complete: function() {
            // Luôn thực hiện sau khi AJAX hoàn thành (dù thành công hay thất bại)
            $('.form-loader').fadeOut(300);  // Ẩn loading spinner
            // Kích hoạt lại form để user có thể thao tác
            $('#order-form').find('input, select, textarea, button').prop('disabled', false);
        }
    });
}

/**
 * Load services theo category với caching và pagination
 * @param {number} categoryId - ID của category
 * @param {boolean} forceReload - Có ép reload hay không
 */
function loadServicesByCategory(categoryId, forceReload = false) {
    if (!categoryId) return;
    
    // Kiểm tra cache trước nếu không ép reload
    if (!forceReload && allData.services.length > 0) {
        const cachedServices = allData.services.filter(service => service.category_id == categoryId);
        if (cachedServices.length > 0) {
            populateServicesDropdown(cachedServices);
            return;
        }
    }
    
    // Hiển thị loading cho dropdown service
    $('#service').html('<option>' + ORDER_LABELS.loading + '</option>');
    $('#service').prop('disabled', true);
    
    // Load services từ server
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'loadServicesByCategory',
            category_id: categoryId,
            token: $('#token').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                // Merge vào cache toàn cục (tránh duplicate)
                const newServices = response.data.filter(newService => 
                    !allData.services.some(existing => existing.id === newService.id)
                );
                allData.services = allData.services.concat(newServices);
                
                // Populate dropdown
                populateServicesDropdown(response.data);
            } else {
                $('#service').html('<option>' + ORDER_LABELS.noServices + '</option>');
            }
        },
        error: function() {
            $('#service').html('<option>' + ORDER_LABELS.errorLoadServices + '</option>');
        },
        complete: function() {
            $('#service').prop('disabled', false);
        }
    });
}

/**
 * Populate services dropdown với dữ liệu đã có
 * @param {Array} services - Danh sách services
 */
function populateServicesDropdown(services) {
    $('#service').empty();
    
    if (services.length > 0) {
        // Lấy hình ảnh của category được chọn
        var categoryImage = $('#category option:selected').data('image') || '';

        $.each(services, function(index, service) {
            $('#service').append('<option value="' + service.id + '" data-image="' + categoryImage +
                '" data-min="' + service.min + '" data-max="' + service.max + '" data-service-price="' +
                service.price + '" data-service-name="' + service.name + '" data-service-id="' + service
                .id + '" data-cancel="' + (service.cancel || false) + '" data-refill="' + (service.refill ||
                    false) + '" data-dripfeed="' + (service.dripfeed || false) + '">' +
                service.id + ' - ' + service.name + ' - ' + service.price + '</option>');
        });

        // Tự động select dịch vụ nếu có service_id trong URL
        if (ORDER_CONFIG.serviceIdFromUrl && ORDER_CONFIG.serviceIdFromUrl !== '') {
            var serviceExists = $('#service option[value="' + ORDER_CONFIG.serviceIdFromUrl + '"]').length > 0;
            if (serviceExists) {
                $('#service').val(ORDER_CONFIG.serviceIdFromUrl);
            }
        }

        // Trigger change để cập nhật giá và số lượng
        $('#service').trigger('change');
    } else {
        $('#service').html('<option>' + ORDER_LABELS.noServices + '</option>');
    }
}

/**
 * Load services với chunk loading để tránh timeout
 * @param {number} categoryId - ID của category
 * @param {number} page - Trang hiện tại (0-based)
 * @param {number} limit - Số lượng mỗi chunk
 */
function loadServicesChunked(categoryId, page = 0, limit = 100) {
    if (!categoryId) return;
    
    const isFirstLoad = page === 0;
    
    if (isFirstLoad) {
        $('#service').html('<option>' + ORDER_LABELS.loading + '</option>');
        $('#service').prop('disabled', true);
    }
    
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'loadServicesChunked',
            category_id: categoryId,
            page: page,
            limit: limit,
            token: $('#token').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const services = response.data.services || [];
                const hasMore = response.data.has_more || false;
                const total = response.data.total || 0;
                
                if (isFirstLoad) {
                    // Lần đầu load, clear dropdown
                    $('#service').empty();
                    
                    // Merge vào cache
                    const newServices = services.filter(newService => 
                        !allData.services.some(existing => existing.id === newService.id)
                    );
                    allData.services = allData.services.concat(newServices);
                    
                    populateServicesDropdown(services);
                    
                    // Hiển thị progress nếu còn data
                    if (hasMore) {
                        showServiceLoadingProgress(services.length, total);
                        // Load chunk tiếp theo trong background
                        setTimeout(() => loadServicesChunked(categoryId, page + 1, limit), 100);
                    }
                } else {
                    // Các lần load tiếp theo, append thêm
                    appendServicesToDropdown(services);
                    updateServiceLoadingProgress(page * limit + services.length, total);
                    
                    if (hasMore) {
                        // Tiếp tục load chunk tiếp theo
                        setTimeout(() => loadServicesChunked(categoryId, page + 1, limit), 50);
                    } else {
                        hideServiceLoadingProgress();
                    }
                }
            }
        },
        error: function() {
            if (isFirstLoad) {
                $('#service').html('<option>' + ORDER_LABELS.errorLoadServices + '</option>');
            }
        },
        complete: function() {
            if (isFirstLoad) {
                $('#service').prop('disabled', false);
            }
        }
    });
}

/**
 * Append services vào dropdown đã có
 */
function appendServicesToDropdown(services) {
    const categoryImage = $('#category option:selected').data('image') || '';
    
    $.each(services, function(index, service) {
        $('#service').append('<option value="' + service.id + '" data-image="' + categoryImage +
            '" data-min="' + service.min + '" data-max="' + service.max + '" data-service-price="' +
            service.price + '" data-service-name="' + service.name + '" data-service-id="' + service
            .id + '" data-cancel="' + (service.cancel || false) + '" data-refill="' + (service.refill ||
                false) + '" data-dripfeed="' + (service.dripfeed || false) + '">' +
            service.id + ' - ' + service.name + ' - ' + service.price + '</option>');
    });
    
    // Refresh Select2 để nhận diện options mới
    $('#service').trigger('change.select2');
}

/**
 * Hiển thị progress loading services
 */
function showServiceLoadingProgress(loaded, total) {
    const progress = Math.round((loaded / total) * 100);
    const progressHtml = `
        <div id="service-loading-progress" class="mt-2">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Đang tải dịch vụ...</span>
                <span>${loaded}/${total} (${progress}%)</span>
            </div>
            <div class="progress" style="height: 4px;">
                <div class="progress-bar" style="width: ${progress}%"></div>
            </div>
        </div>
    `;
    
    $('#service').closest('.mb-3').append(progressHtml);
}

/**
 * Cập nhật progress loading services
 */
function updateServiceLoadingProgress(loaded, total) {
    const progress = Math.round((loaded / total) * 100);
    $('#service-loading-progress .progress-bar').css('width', progress + '%');
    $('#service-loading-progress span:last').text(`${loaded}/${total} (${progress}%)`);
}

/**
 * Ẩn progress loading services
 */
function hideServiceLoadingProgress() {
    $('#service-loading-progress').fadeOut(300, function() {
        $(this).remove();
    });
}

// Hàm khởi tạo tất cả dropdowns
function initializeDropdowns() {
    // Khởi tạo Select2 cho platform và category
    $('#platform, #category').select2({
        templateResult: formatOptionWithImage,
        templateSelection: formatOptionWithImage,
        escapeMarkup: function(m) {
            return m;
        }
    });

    // Khởi tạo Select2 riêng cho service với định dạng đặc biệt
    $('#service').select2({
        templateResult: formatServiceOption,
        templateSelection: formatServiceOption,
        escapeMarkup: function(m) {
            return m;
        }
    });

    // Khởi tạo Select2 cho quick search (giữ nguyên AJAX)
    $('#quick-search-service').select2({
        templateResult: formatQuickSearchService,
        templateSelection: formatQuickSearchService,
        escapeMarkup: function(m) {
            return m;
        },
        ajax: {
            url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'searchServices',
                    token: $('#token').val(),
                    keyword: params.term || ''
                };
            },
            processResults: function(data) {
                return {
                    results: data.status === 'success' ? data.data : []
                };
            },
            cache: true
        },
        placeholder: ORDER_LABELS.quickSearchPlaceholder,
        allowClear: true,
        minimumInputLength: 0
    });

    // Bind events
    bindEvents();
}

// Hàm bind các events
function bindEvents() {
    // Xử lý sự kiện thay đổi platform
    $('#platform').on('change', function() {
        var platformId = $(this).val();
        loadCategoriesFromCache(platformId);
    });

    // Xử lý sự kiện thay đổi category
    $('#category').on('change', function() {
        var categoryId = $(this).val();
        loadServicesFromCache(categoryId);
    });

    // Xử lý khi chọn dịch vụ
    $('#service').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var min = selectedOption.data('min') || 0;
        var max = selectedOption.data('max') || 0;

        $('#min-quantity').text(min);
        $('#max-quantity').text(max);

        var serviceId = $(this).val();
        if (serviceId && serviceId !== '' && 
            !selectedOption.text().includes(ORDER_LABELS.noServices) &&
            !selectedOption.text().includes(ORDER_LABELS.selectCategory)) {
            loadServiceDetails(serviceId);
            
            // Cập nhật modal multi-order nếu modal đang mở
            if ($('#multiOrderModal').hasClass('show')) {
                updateMultiOrderServiceInfo();
            }
        } else {
            // Hiển thị loading state khi không có dịch vụ được chọn
            showServiceDetailsLoading();
        }
    });

    // Xử lý quick search selection
    $('#quick-search-service').on('select2:select', function(e) {
        var data = e.params.data;
        if (data && data.id) {
            handleQuickSearchSelection(data);
        }
    });

    // Các event khác
    $(document).on('input', '#quantity', function() {
        updateTotalPriceDebounced();
    });

    $(document).on('input', '#comments', function() {
        var serviceId = $('#service').val();
        if (serviceId && serviceDetailsCache[serviceId] && serviceDetailsCache[serviceId].type ===
            'Custom Comments') {
            countCommentLinesDebounced();
        }
    });
}

// Hàm load categories từ cache
function loadCategoriesFromCache(platformId) {
    $('#category').empty();
    $('#service').empty();
    
    // Hiển thị loading state khi thay đổi platform
    showServiceDetailsLoading();

    if (!platformId) {
        $('#category').html('<option>' + ORDER_LABELS.selectPlatform + '</option>');
        $('#service').html('<option>' + ORDER_LABELS.selectCategory + '</option>');
        return;
    }

    var categories = allData.categories.filter(function(cat) {
        return cat.platform_id == platformId;
    });

    if (categories.length > 0) {
        var categoryFound = false;

        $.each(categories, function(index, category) {
            var selected = '';
            if (ORDER_CONFIG.categorySlug && category.slug === ORDER_CONFIG.categorySlug) {
                selected = 'selected';
                categoryFound = true;
            }
            $('#category').append('<option value="' + category.id + '" data-image="' + category.icon + '" ' +
                selected + '>' + category.name + '</option>');
        });

        // Trigger change để load services
        $('#category').trigger('change');
    } else {
        $('#category').html('<option>' + ORDER_LABELS.noCategories + '</option>');
        $('#service').html('<option>' + ORDER_LABELS.selectCategory + '</option>');
    }

    // Refresh select2
    $('#category').select2({
        templateResult: formatOptionWithImage,
        templateSelection: formatOptionWithImage,
        escapeMarkup: function(m) {
            return m;
        }
    });
}

// Hàm load services từ cache (smart loading theo mode)
function loadServicesFromCache(categoryId) {
    if (loadDataMode === 'all') {
        // MODE ALL: Sử dụng cache có sẵn (như cũ)
        loadServicesFromCacheAll(categoryId);
    } else {
        // MODE LAZY: Sử dụng lazy loading optimized
        loadServicesByCategory(categoryId);
    }
}

// Hàm load services từ cache khi ở mode ALL (logic cũ)
function loadServicesFromCacheAll(categoryId) {
    $('#service').empty();
    
    // Hiển thị loading state khi thay đổi category
    showServiceDetailsLoading();

    if (!categoryId) {
        $('#service').html('<option>' + ORDER_LABELS.selectCategory + '</option>');
        return;
    }

    var services = allData.services.filter(function(service) {
        return service.category_id == categoryId;
    });

    if (services.length > 0) {
        // Lấy hình ảnh của category được chọn
        var categoryImage = $('#category option:selected').data('image') || '';

        $.each(services, function(index, service) {
            $('#service').append('<option value="' + service.id + '" data-image="' + categoryImage +
                '" data-min="' + service.min + '" data-max="' + service.max + '" data-service-price="' +
                service.price + '" data-service-name="' + service.name + '" data-service-id="' + service
                .id + '" data-cancel="' + (service.cancel || false) + '" data-refill="' + (service.refill ||
                    false) + '" data-dripfeed="' + (service.dripfeed || false) + '">' +
                service.id + ' - ' + service.name + ' - ' + service.price + '</option>');
        });

        // Tự động select dịch vụ nếu có service_id trong URL
        if (ORDER_CONFIG.serviceIdFromUrl && ORDER_CONFIG.serviceIdFromUrl !== '') {
            var serviceExists = $('#service option[value="' + ORDER_CONFIG.serviceIdFromUrl + '"]').length > 0;
            if (serviceExists) {
                $('#service').val(ORDER_CONFIG.serviceIdFromUrl);
            }
        }

        // Trigger change để cập nhật giá và số lượng
        $('#service').trigger('change');
    } else {
        $('#service').html('<option>' + ORDER_LABELS.noServices + '</option>');
    }
}

// Hàm tự động chọn theo URL parameters
function autoSelectFromUrl() {
    // Tự động chọn platform nếu có trong URL
    if (ORDER_CONFIG.platformSlug) {
        var platform = allData.platforms.find(function(p) {
            return p.slug === ORDER_CONFIG.platformSlug;
        });
        if (platform) {
            $('#platform').val(platform.id).trigger('change');
        }
    } else {
        // Load categories cho platform đầu tiên nếu không có platformSlug
        var defaultPlatformId = $('#platform').val();
        if (defaultPlatformId) {
            loadCategoriesFromCache(defaultPlatformId);
        }
    }
}

// Hàm xử lý quick search selection
function handleQuickSearchSelection(data) {
    var serviceId = data.id;
    var platformId = data.platform_id;
    var categoryId = data.category_id;

    if (!platformId || !categoryId) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.noServiceInfo,
            icon: 'error'
        });
        return;
    }

    // Hiển thị loading
    Swal.fire({
        title: ORDER_LABELS.processing,
        text: ORDER_LABELS.selectingService,
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Chọn platform
    $('#platform').val(platformId).trigger('change');

    setTimeout(function() {
        // Chọn category
        $('#category').val(categoryId).trigger('change');

        setTimeout(function() {
            // Chọn service
            $('#service').val(serviceId).trigger('change');

            // Thành công
            setTimeout(function() {
                Swal.close();
                Swal.fire({
                    title: ORDER_LABELS.success,
                    text: ORDER_LABELS.serviceSelected,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Xóa giá trị ô tìm kiếm
                $('#quick-search-service').val(null).trigger('change');
            }, 300);
        }, 300);
    }, 300);
}

// Hàm load service details (giữ nguyên AJAX vì cần thông tin chi tiết)
function loadServiceDetails(serviceId) {
    // Hiển thị loading state
    showServiceDetailsLoading();
    
    // Kiểm tra cache trước
    if (serviceDetailsCache[serviceId]) {
        applyServiceDetails(serviceId, serviceDetailsCache[serviceId]);
        return;
    }

    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'getServiceDetails',
            service_id: serviceId
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Cache kết quả
                serviceDetailsCache[serviceId] = response.data;
                applyServiceDetails(serviceId, response.data);
            } else {
                // Nếu có lỗi, vẫn hiển thị thông tin cơ bản từ option
                var selectedOption = $('#service option:selected');
                var basicData = {
                    name: selectedOption.text(),
                    min: selectedOption.data('min'),
                    max: selectedOption.data('max'),
                    price: selectedOption.data('service-price'),
                    cancel: selectedOption.data('cancel'),
                    refill: selectedOption.data('refill'),
                    dripfeed: selectedOption.data('dripfeed')
                };
                applyServiceDetails(serviceId, basicData);
            }
        },
        error: function() {
            // Lỗi tải thông tin dịch vụ, hiển thị thông tin cơ bản từ option
            var selectedOption = $('#service option:selected');
            var basicData = {
                name: selectedOption.text(),
                min: selectedOption.data('min'),
                max: selectedOption.data('max'),
                price: selectedOption.data('service-price'),
                cancel: selectedOption.data('cancel'),
                refill: selectedOption.data('refill'),
                dripfeed: selectedOption.data('dripfeed')
            };
            applyServiceDetails(serviceId, basicData);
        }
    });
}

// Hàm áp dụng service details
function applyServiceDetails(serviceId, data) {

    // Cập nhật min, max
    if (data.min) {
        $('#quantity').attr('min', data.min);
        $('#min-quantity').text(data.min);
    }

    if (data.max) {
        $('#quantity').attr('max', data.max);
        $('#max-quantity').text(data.max);
    }

    // Xử lý comment container
    if (data.type && (data.type === 'Custom Comments' || data.type === 'Custom Comments Package')) {
        $('#comment-container').show();

        if (data.type === 'Custom Comments') {
            $('#quantity').attr('readonly', true);
            $('#quantity-comment-info').show();
            countCommentLines();
        } else if (data.type === 'Custom Comments Package') {
            $('#quantity').val(data.min || 1);
            $('#quantity').attr('readonly', false); // Cho phép thay đổi số lượng
            $('#quantity-comment-info').hide();
        }
    } else {
        $('#comment-container').hide();
        $('#comments').val('');
        $('#quantity').attr('readonly', false);
        $('#quantity').val(data.min); // Nhập số lượng tối thiểu vào ô số lượng
        $('#quantity-comment-info').hide();
    }

    // Xử lý Package type
    if (data.type && data.type === 'Package') {
        $('#quantity').closest('.mb-3').hide();
        $('#quantity').val(1);
    } else {
        $('#quantity').closest('.mb-3').show();
    }

    // Cập nhật tổng giá
    updateTotalPrice();
    
    // Cập nhật thông tin chi tiết dịch vụ trong card
    updateServiceDetailsCard(serviceId, data);
    
    // Cập nhật modal multi-order nếu modal đang mở
    if ($('#multiOrderModal').hasClass('show')) {
        updateMultiOrderServiceInfo();
    }
}

// Hàm cập nhật tổng giá với debounce
function updateTotalPriceDebounced() {
    clearTimeout(quantityDebounceTimer);
    quantityDebounceTimer = setTimeout(function() {
        updateTotalPrice();
    }, 500); // Delay 500ms sau khi user ngừng gõ
}

// Hàm hiển thị loading state
function showServiceDetailsLoading() {
    $('#serviceDetailsLoading').show();
    $('#serviceDetailsContent').hide();
}

// Hàm cập nhật thông tin chi tiết dịch vụ trong card
function updateServiceDetailsCard(serviceId, data) {
    // Ẩn loading, hiển thị content
    $('#serviceDetailsLoading').hide();
    $('#serviceDetailsContent').show();
    
    // Lấy thông tin cơ bản từ service option
    var selectedOption = $('#service option:selected');
    var serviceName = data.name || selectedOption.data('service-name') || selectedOption.text();
    var servicePrice = data.price || selectedOption.data('service-price') || '';
    
    // Cập nhật ID và tên dịch vụ
    $('#serviceDetailId').text(serviceId);
    $('#serviceDetailName').text(serviceName);
    
    // Cập nhật loại dịch vụ nếu có
    if (data.type && data.type !== '') {
        $('#serviceDetailType').text(data.type);
        $('#serviceTypeRow').show();
    } else {
        $('#serviceTypeRow').hide();
    }
    
    // Cập nhật thời gian hoàn thành trung bình nếu có
    if (data.average_time && data.average_time !== '' && data.average_time !== '0' && data.average_time !== 0) {
        $('#serviceAverageTime').text(data.average_time);
        $('#averageTimeRow').show();
    } else {
        $('#averageTimeRow').hide();
    }
    
    // Cập nhật giới hạn số lượng
    var minQuantity = data.min || 0;
    var maxQuantity = data.max || 0;
    if (minQuantity > 0 || maxQuantity > 0) {
        var quantityText = '';
        if (minQuantity > 0 && maxQuantity > 0) {
            quantityText = new Intl.NumberFormat('vi-VN').format(minQuantity) + ' - ' + 
                          new Intl.NumberFormat('vi-VN').format(maxQuantity);
        } else if (minQuantity > 0) {
            quantityText = ORDER_LABELS.minLabel + ': ' + new Intl.NumberFormat('vi-VN').format(minQuantity);
        } else if (maxQuantity > 0) {
            quantityText = ORDER_LABELS.maxLabel + ': ' + new Intl.NumberFormat('vi-VN').format(maxQuantity);
        }
        $('#serviceQuantityRange').text(quantityText);
    } else {
        $('#serviceQuantityRange').text('Không giới hạn');
    }
    
    // Cập nhật giá dịch vụ
    if (servicePrice && servicePrice !== '') {
        $('#serviceDetailPrice').text(servicePrice);
    } else {
        $('#serviceDetailPrice').text('-');
    }
    

    
    // Cập nhật mô tả dịch vụ
    if (data.description && data.description.trim() !== '') {
        $('#serviceDescriptionContent').html(data.description);
        $('#serviceDetailDescription').show();
    } else {
        $('#serviceDetailDescription').hide();
    }
}

// Hàm đếm comment lines với debounce
function countCommentLinesDebounced() {
    clearTimeout(commentDebounceTimer);
    commentDebounceTimer = setTimeout(function() {
        countCommentLines();
    }, 500); // Delay 500ms cho comment (lâu hơn quantity nếu muốn)
}

// Hàm cập nhật tổng giá (realtime AJAX)
function updateTotalPrice() {
    var quantity = parseInt($('#quantity').val()) || 0;
    var serviceId = $('#service').val();

    if (!serviceId || quantity <= 0) {
        $('#total-price').text('0');
        return;
    }

    // Luôn gọi AJAX để tính giá realtime
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'totalPrice',
            token: $('#token').val(),
            service_id: serviceId,
            amount: quantity
        },
        dataType: 'json',
        beforeSend: function() {
            // Hiển thị loading state cho total price
            $('#total-price').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#total-price').text(response.total_price);   // Số tiền thanh toán sau khi tính thuế VAT
                $('#price').text(response.price);               // Số tiền chưa tính thuế
                $('#price-vat').text(response.price_vat);       // Số tiền thuế VAT
                $('#tax-vat').text(response.tax_vat);           // Thuế VAT (%)
                
                // Ẩn/hiện phần chi tiết giá và thuế VAT
                var priceDetailRow = document.getElementById('price-detail-row');
                var taxDetailRow = document.getElementById('tax-detail-row');
                var priceSeparator = document.getElementById('price-separator');
                
                if (parseFloat(response.tax_vat) > 0) {
                    // Hiển thị khi có thuế VAT
                    if (priceDetailRow) {
                        priceDetailRow.style.display = 'flex';
                        priceDetailRow.classList.add('d-flex');
                    }
                    if (taxDetailRow) {
                        taxDetailRow.style.display = 'flex';
                        taxDetailRow.classList.add('d-flex');
                    }
                    if (priceSeparator) {
                        priceSeparator.style.display = 'block';
                    }
                } else {
                    // Ẩn khi không có thuế VAT
                    if (priceDetailRow) {
                        priceDetailRow.style.display = 'none';
                        priceDetailRow.classList.remove('d-flex');
                    }
                    if (taxDetailRow) {
                        taxDetailRow.style.display = 'none';
                        taxDetailRow.classList.remove('d-flex');
                    }
                    if (priceSeparator) {
                        priceSeparator.style.display = 'none';
                    }
                }
            } else {
                $('#total-price').text('0');
                $('#price-vat').text('0');
                $('#tax-vat').text('0');
                
                // Ẩn phần chi tiết khi có lỗi
                var priceDetailRow = document.getElementById('price-detail-row');
                var taxDetailRow = document.getElementById('tax-detail-row');
                var priceSeparator = document.getElementById('price-separator');
                if (priceDetailRow) {
                    priceDetailRow.style.display = 'none';
                    priceDetailRow.classList.remove('d-flex');
                }
                if (taxDetailRow) {
                    taxDetailRow.style.display = 'none';
                    taxDetailRow.classList.remove('d-flex');
                }
                if (priceSeparator) {
                    priceSeparator.style.display = 'none';
                }
            }
        },
        error: function() {
            $('#total-price').text('0');
        }
    });
}

// Các hàm khác giữ nguyên
function countCommentLines() {
    var commentText = $('#comments').val();
    var lines = commentText.split(/\r\n|\r|\n/);

    var nonEmptyLines = lines.filter(function(line) {
        return line.trim() !== '';
    });

    var lineCount = nonEmptyLines.length;
    $('#quantity').val(lineCount > 0 ? lineCount : 1);

    updateTotalPrice();
}

// Hàm xử lý hiển thị modal xác nhận đặt hàng
function submitOrder() {
    // Kiểm tra dữ liệu đầu vào
    var serviceId = $('#service').val();
    var serviceName = $('#service option:selected').text();
    var link = $('#link').val();
    var quantity = parseInt($('#quantity').val()) || 0;
    var comments = $('#comments').val();
    var schedule = $('#schedule').prop('checked');
    var totalPrice = $('#total-price').text();

    // Kiểm tra dịch vụ
    if (!serviceId || serviceId === '' || $('#service option:selected').text().includes(ORDER_LABELS.noServices) || 
        $('#service option:selected').text().includes(ORDER_LABELS.loading) || 
        $('#service option:selected').text().includes(ORDER_LABELS.selectCategory)) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.pleaseSelectService,
            icon: 'error'
        });
        return false;
    }

    // Kiểm tra liên kết
    if (!link || link.trim() === '') {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.pleaseEnterLink,
            icon: 'error'
        });
        $('#link').focus();
        return false;
    }

    // Kiểm tra số lượng
    if (!quantity || quantity <= 0) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.pleaseEnterValidQuantity,
            icon: 'error'
        });
        $('#quantity').focus();
        return false;
    }

    // Lấy giá trị min/max từ service được chọn
    var selectedService = $('#service option:selected');
    var minQuantity = parseInt(selectedService.data('min')) || parseInt($('#min-quantity').text()) || 0;
    var maxQuantity = parseInt(selectedService.data('max')) || parseInt($('#max-quantity').text()) || 0;

    // Kiểm tra số lượng tối thiểu
    if (minQuantity > 0 && quantity < minQuantity) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.minQuantity + ' ' + minQuantity,
            icon: 'error'
        });
        $('#quantity').focus();
        return false;
    }

    // Kiểm tra số lượng tối đa
    if (maxQuantity > 0 && quantity > maxQuantity) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.maxQuantity + ' ' + maxQuantity,
            icon: 'error'
        });
        $('#quantity').focus();
        return false;
    }

    // Kiểm tra thời gian đặt lịch
    if (schedule && (!$('#schedule-time').val() || $('#schedule-time').val() === '')) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.pleaseSelectScheduleTime,
            icon: 'error'
        });
        return false;
    }

    // Hiển thị thông tin trong modal
    $('#modal-service-info').text(serviceName);
    $('#modal-link').val(link);
    $('#modal-quantity').text(quantity);

    // Xử lý hiển thị bình luận nếu có
    if (comments && comments.trim() !== '' && $('#comment-container').is(':visible')) {
        $('#modal-comment').val(comments);
        $('#modal-comment-row').show();
    } else {
        $('#modal-comment-row').hide();
    }

    // Hiển thị thông tin đặt lịch
    $('#modal-schedule').text(schedule ? ORDER_LABELS.yes : ORDER_LABELS.no);

    // Hiển thị thông tin chi tiết giá tiền
    var price = $('#price').text().trim();
    var priceVat = $('#price-vat').text().trim();
    var taxVat = $('#tax-vat').text().trim();
    
    
    // Cập nhật chi tiết giá trong modal
    if (price && price !== '0' && price !== '') {
        $('#modal-price').text(price);
        $('#modal-price-detail-row').show(); // Hiển thị chi tiết giá trị đơn hàng
    } else {
        $('#modal-price-detail-row').hide(); // Ẩn chi tiết giá trị đơn hàng
    }
    
    if (priceVat && priceVat !== '0' && priceVat !== '' && taxVat && taxVat !== '0' && taxVat !== '') {
        $('#modal-price-vat').text(priceVat);
        $('#modal-tax-vat').text(taxVat);
        $('#modal-tax-detail-row').show(); // Hiển thị chi tiết thuế VAT
        $('#modal-price-separator').show(); // Hiển thị dòng phân cách
    } else {
        $('#modal-tax-detail-row').hide(); // Ẩn chi tiết thuế VAT
        $('#modal-price-separator').hide(); // Ẩn dòng phân cách
    }
    
    // Hiển thị tổng tiền
    $('#modal-total-price').text(totalPrice); // Hiển thị tổng tiền cần thanh toán

    // Hiển thị modal
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
    confirmModal.show();

    return false;
}

// Biến quản lý trạng thái xử lý đơn hàng
let isProcessingOrder = false;

// Xử lý khi nhấn nút xác nhận đặt hàng trong modal
function confirmOrder() {
    var btn = $('#confirm-order-btn');
    
    // Đánh dấu đang xử lý đơn hàng
    isProcessingOrder = true;
    
    // Thêm overlay loading lên modal
    addLoadingOverlayToModal();
    
    // Hiển thị loading cho nút
    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + ORDER_LABELS.processingOrder);
    btn.prop('disabled', true);
    $('.btn-close, button[data-bs-dismiss="modal"]').prop('disabled', true);

    // Hiển thị cảnh báo không tắt trang
    showProcessingWarning();
    
    // Thêm event listener để cảnh báo khi user cố gắng tắt trang
    addBeforeUnloadWarning();

    // Lấy dữ liệu từ form
    var serviceId = $('#service').val();
    var link = $('#link').val();
    var quantity = $('#quantity').val();
    var comments = $('#comments').val();
    var schedule = $('#schedule').prop('checked') ? 1 : 0;
    var scheduleTime = $('#schedule-time').val() || '';

    // Gửi dữ liệu đến server
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/smmpanel.php',
        type: 'POST',
        data: {
            action: 'add',
            key: $('#api_key').val(),
            service: serviceId,
            link: link,
            quantity: quantity,
            comments: comments,
            schedule: schedule,
            schedule_time: scheduleTime
        },
        dataType: 'json',
        success: function(response) {
            // Xóa overlay loading
            removeLoadingOverlayFromModal();
            
            // Đóng modal xác nhận
            var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal'));
            confirmModal.hide();

            // Xử lý kết quả
            if (response.status === 'success') {
                // Hiển thị thông báo thành công trong modal tùy chỉnh
                $('#success-order-msg').text(response.msg);

                // Phát âm thanh thông báo
                playSuccessSound();

                // Hiển thị modal thành công
                var successModal = new bootstrap.Modal(document.getElementById('success-order-modal'));
                successModal.show();

                // Xử lý sự kiện khi nhấn nút xem lịch sử đơn hàng
                $('#view-orders-btn').off('click').on('click', function() {
                    location.href = ORDER_CONFIG.base_url + 'client/orders';
                });

                // Xử lý sự kiện khi nhấn nút đặt hàng tiếp
                $('#new-order-btn').off('click').on('click', function() {
                    // Đóng modal
                    successModal.hide();
                });
            } else {
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: ORDER_LABELS.error,
                    text: response.msg,
                    icon: 'error',
                    confirmButtonText: ORDER_LABELS.ok
                });
            }
        },
        error: function() {
            // Xóa overlay loading
            removeLoadingOverlayFromModal();
            
            // Đóng modal
            var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal'));
            confirmModal.hide();

            // Hiển thị thông báo lỗi
            Swal.fire({
                title: ORDER_LABELS.error,
                text: ORDER_LABELS.connectionError,
                icon: 'error',
                confirmButtonText: ORDER_LABELS.ok
            });
        },
        complete: function() {
            // Đánh dấu đã hoàn thành xử lý
            isProcessingOrder = false;
            
            // Xóa event listener beforeunload
            removeBeforeUnloadWarning();
            
            // Ẩn cảnh báo processing
            hideProcessingWarning();
            
            // Khôi phục trạng thái nút
            btn.html(ORDER_LABELS.confirmOrder);
            btn.prop('disabled', false);
            $('.btn-close, button[data-bs-dismiss="modal"]').prop('disabled', false);
        }
    });
}

// Hàm thêm overlay loading lên modal
function addLoadingOverlayToModal() {
    // Kiểm tra nếu overlay đã tồn tại thì không tạo lại
    if ($('#modal-loading-overlay').length > 0) return;
    
    const overlay = `
        <div id="modal-loading-overlay" class="modal-loading-overlay">
            <div class="modal-loading-content">
                <div class="spinner-border text-primary mb-3 modal-loading-spinner" role="status">
                    <span class="visually-hidden">${ORDER_LABELS.loading}</span>
                </div>
                <h5>${ORDER_LABELS.processingOrder}</h5>
            </div>
        </div>
    `;
    
    $('body').append(overlay);
    
    // Thêm hiệu ứng fade in
    $('#modal-loading-overlay').fadeIn(300);
}

// Hàm xóa overlay loading khỏi modal
function removeLoadingOverlayFromModal() {
    $('#modal-loading-overlay').fadeOut(300, function() {
        $(this).remove();
    });
}

// Hàm hiển thị cảnh báo processing
function showProcessingWarning() {
    // Kiểm tra nếu cảnh báo đã tồn tại
    if ($('#processing-warning').length > 0) return;
    
    const warning = `
        <div id="processing-warning" class="alert alert-warning border-0 mb-0 processing-warning">
            <div class="d-flex align-items-start">
                <i class="ri-alert-line text-warning me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">${ORDER_LABELS.orderProcessingTitle}</h6>
                    <p class="mb-0 small">${ORDER_LABELS.orderProcessingMessage}</p>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(warning);
    
    // Animation CSS đã được thêm vào main.css
}

// Hàm ẩn cảnh báo processing
function hideProcessingWarning() {
    $('#processing-warning').fadeOut(300, function() {
        $(this).remove();
    });
}

// Hàm thêm cảnh báo beforeunload
function addBeforeUnloadWarning() {
    $(window).on('beforeunload.orderProcessing', function(e) {
        if (isProcessingOrder) {
            const message = ORDER_LABELS.orderProcessingWarning;
            e.returnValue = message; // For older browsers
            return message; // For modern browsers
        }
    });
}

// Hàm xóa cảnh báo beforeunload
function removeBeforeUnloadWarning() {
    $(window).off('beforeunload.orderProcessing');
}

// Hàm phát âm thanh thông báo thành công
function playSuccessSound() {
    try {
        var sound = document.getElementById('success-sound');
        if (sound) {
            // Đặt lại âm thanh về đầu để đảm bảo phát được
            sound.currentTime = 0;

            // Phát âm thanh
            var playPromise = sound.play();

            // Xử lý lỗi có thể xảy ra khi phát âm thanh (như trình duyệt chặn tự động phát)
            if (playPromise !== undefined) {
                playPromise.catch(function(error) {
                    // Âm thanh không phát được, bỏ qua
                });
            }
        }
    } catch (e) {
        // Lỗi phát âm thanh, bỏ qua
    }
}

// Hàm custom giao diện cho quick-search-service
function formatQuickSearchService(option) {
    if (!option.id) return option.text;
    // Lấy id và tên từ option trả về từ backend
    var serviceId = option.id;
    var serviceName = option.name || option.text || '';
    var servicePrice = option.price || '';
    return $(
        `<div class="quick-service-item">
            <span class="service-id">#${serviceId}</span>
            <span class="service-name">${serviceName}</span>
            <span class="service-badges"></span>
            <span class="service-price">${servicePrice}</span>
        </div>`
    );
}

// Document ready functions
$(document).ready(function() {
    // Load dữ liệu khi trang tải xong (smart loading theo admin setting)
    loadInitialData();

    // Kiểm tra và hiển thị thông báo Telegram
    checkTelegramNoticeVisibility();

    // Mở dropdown khi click vào ô tìm kiếm
    $(document).on('click', '#quick-search-service + .select2 .select2-selection__rendered', function() {
        $('#quick-search-service').select2('open');
    });

    // Tự động mở dropdown khi focus vào ô tìm kiếm
    $('#quick-search-service').on('select2:open', function() {
        setTimeout(function() {
            var searchInput = $('.select2-search__field');
            if (searchInput.length) {
                searchInput.val('').trigger('input');
            }
        }, 0);
    });

    // Xử lý hiển thị trường chọn thời gian khi checkbox được chọn
    $('#schedule').on('change', function() {
        if ($(this).prop('checked')) {
            $('#schedule-time-container').slideDown(100);
        } else {
            $('#schedule-time-container').slideUp(100);
        }
    });

    // Bind confirm order event
    $(document).on('click', '#confirm-order-btn', confirmOrder);

    // Bind paste from clipboard event
    $(document).on('click', '#pasteFromClipboard', pasteFromClipboard);

    // Khởi tạo flatpickr cho schedule time
    initializeFlatpickr();
});

// Hàm khởi tạo flatpickr
function initializeFlatpickr() {
    if (typeof flatpickr !== 'undefined' && document.getElementById('schedule-time')) {
        flatpickr("#schedule-time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            altInput: true,
            altFormat: "d/m/Y H:i",
            minDate: "today",
            minuteIncrement: 1,
            allowInput: true,
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ORDER_LABELS.weekdaysShort,
                    longhand: ORDER_LABELS.weekdaysLong
                },
                months: {
                    shorthand: ORDER_LABELS.monthsShort,
                    longhand: ORDER_LABELS.monthsLong
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                // Thiết lập thời gian tối thiểu là hiện tại + 10 phút
                var now = new Date();
                now.setMinutes(now.getMinutes() + 10);
                instance.set('minDate', now);

                // Nếu chưa có giá trị, thiết lập giá trị mặc định
                if (!dateStr) {
                    instance.setDate(now);
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                // Kiểm tra nếu thời gian đã chọn nhỏ hơn thời gian hiện tại + 10 phút
                if (selectedDates.length > 0) {
                    var selectedDate = selectedDates[0];
                    var now = new Date();
                    now.setMinutes(now.getMinutes() + 10);

                    if (selectedDate < now) {
                        instance.setDate(now);
                        Swal.fire({
                            title: ORDER_LABELS.notification,
                            text: ORDER_LABELS.scheduleTimeWarning,
                            icon: 'warning',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            }
        });
    }
}

// ==================== CHỨC NĂNG MUA NHIỀU ĐƠN HÀNG ====================

// Biến quản lý trạng thái multi-order
let multiOrderState = {
    isRunning: false,       // Có đang chạy không
    isPaused: false,        // Có bị tạm dừng không
    orders: [],            // Danh sách đơn hàng
    currentIndex: 0,       // Index hiện tại đang xử lý
    delay: 1000,           // Thời gian delay giữa các đơn (ms)
    processedCount: 0,     // Số đơn đã xử lý
    successCount: 0,       // Số đơn thành công
    failedCount: 0,        // Số đơn thất bại
    interval: null         // Timer interval
};

// Timer cho debounce khi user thay đổi quantity trong multi-order
let multiOrderQuantityDebounceTimer;
let multiOrderCommentDebounceTimer;

// LocalStorage key for multi-order data
const MULTI_ORDER_STORAGE_KEY = 'multiOrderData';

// Hàm lưu dữ liệu multi-order vào localStorage
function saveMultiOrderToStorage(serviceId, links, delay) {
    const data = {
        serviceId: serviceId,
        links: links,
        delay: delay,
        timestamp: Date.now()
    };
    localStorage.setItem(MULTI_ORDER_STORAGE_KEY, JSON.stringify(data));
}

// Hàm khôi phục dữ liệu multi-order từ localStorage
function loadMultiOrderFromStorage() {
    try {
        const stored = localStorage.getItem(MULTI_ORDER_STORAGE_KEY);
        if (stored) {
            const data = JSON.parse(stored);
            // Kiểm tra dữ liệu không quá cũ (ví dụ 24 giờ)
            if (Date.now() - data.timestamp < 24 * 60 * 60 * 1000) {
                return data;
            }
        }
    } catch (e) {
        console.error('Error loading multi-order data from localStorage:', e);
    }
    return null;
}

// Hàm xóa dữ liệu multi-order từ localStorage
function clearMultiOrderStorage() {
    localStorage.removeItem(MULTI_ORDER_STORAGE_KEY);
}

// Hàm hiển thị modal multi-order
function showMultiLinkModal() {
    // Kiểm tra trạng thái đăng nhập
    if (typeof USER_LOGGED_IN !== 'undefined' && !USER_LOGGED_IN) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.loginRequired || 'Vui lòng đăng nhập để sử dụng tính năng này',
            icon: 'warning',
            confirmButtonText: ORDER_LABELS.ok,
            showCancelButton: true,
            cancelButtonText: ORDER_LABELS.cancel || 'Hủy',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = ORDER_CONFIG.base_url + 'client/login';
            }
        });
        return;
    }
    
    // Kiểm tra nếu chưa chọn dịch vụ
    if (!$('#service').val()) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.serviceNotSelected,
            icon: 'warning',
            confirmButtonText: ORDER_LABELS.ok
        });
        return;
    }
    
    // Reset form và state
    resetMultiOrderState();
    
    // Hiển thị modal
    var modal = new bootstrap.Modal(document.getElementById('multiOrderModal'));
    modal.show();
    
    // Update service và thông tin hiện tại
    updateMultiOrderServiceInfo();
    
    // Gọi lại updateMultiOrderServiceInfo khi có thể service details đã thay đổi
    setTimeout(updateMultiOrderServiceInfo, 100);
    
    // Khôi phục dữ liệu từ localStorage
    const savedData = loadMultiOrderFromStorage();
    if (savedData) {
        // Lấy service_id hiện tại từ form chính  
        const currentServiceId = $('#service').val();
        
        // Restore links và delay
        if (savedData.links && savedData.links.length > 0) {
            const linkTexts = savedData.links.map(link => link.url).join('\n');
            $('#multiLinks').val(linkTexts);
            
            // Restore delay
            if (savedData.delay) {
                $('#multiDelay').val(savedData.delay);
            }
            
            // Restore queue với service_id hiện tại và trạng thái ban đầu
            multiOrderState.orders = savedData.links.map((link, index) => ({
                url: link.url,
                service_id: currentServiceId, // Sử dụng service_id hiện tại
                status: link.status || 'pending',
                order_id: link.order_id || null,
                message: link.message || '',
                comments: link.comments || $('#multiComments').val(),
                index: index // Thêm index để đồng bộ với logic hiện tại
            }));
            
            // Update counts từ saved data
            multiOrderState.processedCount = savedData.links.filter(link => 
                link.status === 'success' || link.status === 'error'
            ).length;
            
            multiOrderState.successCount = savedData.links.filter(link => 
                link.status === 'success'
            ).length;
            
            multiOrderState.failedCount = savedData.links.filter(link => 
                link.status === 'error'
            ).length;
            
            // Tìm vị trí đơn hàng tiếp theo cần xử lý
            multiOrderState.currentIndex = 0;
            for (let i = 0; i < savedData.links.length; i++) {
                if (savedData.links[i].status === 'pending' || savedData.links[i].status === 'waiting') {
                    multiOrderState.currentIndex = i;
                    break;
                }
                multiOrderState.currentIndex = i + 1;
            }
            
            // Update UI
            $('#linkCount').text(savedData.links.length);
            $('#totalOrders').text(savedData.links.length);
            updateProgress();
            
            // Generate orders list với trạng thái đã lưu
            generateOrdersListFromSavedData(savedData.links);
            
            // Update tổng tiền
            updateMultiOrderSummary(savedData.links.map(link => link.url));
        
        }
    }
}

// Reset trạng thái multi-order
function resetMultiOrderState() {
    multiOrderState = {
        isRunning: false,
        isPaused: false,
        orders: [],
        currentIndex: 0,
        delay: 1000,
        processedCount: 0,
        successCount: 0,
        failedCount: 0,
        interval: null
    };
    
    // Reset UI
    $('#multiLinks').val('');
    $('#multiQuantity').val('1');
    $('#multiDelay').val('1');
    $('#multiComments').val('');
    $('#linkCount').text('0');
    $('#totalOrders').text('0');
    $('#processedCount').text('0');
    $('#totalCount').text('0');
    $('#multiOrderProgress').css('width', '0%');
    $('#multi-min-quantity').text('-');
    $('#multi-max-quantity').text('-');
    
    // Reset pricing
    resetMultiOrderPricing();
    
    // Reset buttons
    showMultiOrderButton('start');  
    
    // Clear orders list
    $('#ordersList').html(`
        <div class="text-center text-muted py-4">
            <i class="ri-shopping-cart-line fs-1"></i>
            <p class="mt-2">${ORDER_LABELS.enterLinks}</p>
        </div>
    `);
}

// Hiển thị button tương ứng với trạng thái
function showMultiOrderButton(state) {
    $('#startMultiOrder, #pauseMultiOrder, #resumeMultiOrder, #stopMultiOrder').hide();
    
    switch(state) {
        case 'start':
            $('#startMultiOrder').show();
            break;
        case 'running':
            $('#pauseMultiOrder, #stopMultiOrder').show();
            break;
        case 'paused':
            $('#resumeMultiOrder, #stopMultiOrder').show();
            break;
    }
}

// Update thông tin dịch vụ trong modal
function updateMultiOrderServiceInfo() {
    let serviceId = $('#service').val();
    let selectedOption = $('#service option:selected');
    let serviceNameText = selectedOption.text();
    
    // Hiển thị thông tin dịch vụ
    if (serviceId && serviceNameText) {
        $('#multiServiceInfo').show();
        
        // Ưu tiên lấy tên sạch từ cache, sau đó từ data-attribute, cuối cùng fallback về text gốc.
        const serviceDetails = serviceDetailsCache[serviceId];
        const serviceNameFromData = selectedOption.data('service-name');
        
        const cleanServiceName = (serviceDetails && serviceDetails.name) 
                                 || serviceNameFromData 
                                 || serviceNameText;

        $('#multiServiceName').text(cleanServiceName);
    } else {
        $('#multiServiceInfo').hide();
    }
    
    // Hiển thị comment container nếu service hỗ trợ
    let showCommentContainer = false;
    
    if (serviceDetailsCache[serviceId]) {
        // Kiểm tra theo nhiều cách khác nhau
        if (serviceDetailsCache[serviceId].type === 'comment' || 
            serviceDetailsCache[serviceId].category_type === 'comment' ||
            serviceDetailsCache[serviceId].comment === 1 || 
            serviceDetailsCache[serviceId].comment === true) {
            showCommentContainer = true;
        }
    }
    
    // Cũng kiểm tra từ form chính để đảm bảo consistency
    if ($('#comment-container').is(':visible')) {
        showCommentContainer = true;
    }
    
    if (showCommentContainer) {
        $('#multiCommentContainer').show();
    } else {
        $('#multiCommentContainer').hide();
    }
    
    // Cập nhật min/max quantity từ service details
    if (serviceDetailsCache[serviceId]) {
        let serviceDetails = serviceDetailsCache[serviceId];
        let minQuantity = parseInt(serviceDetails.min) || 1;
        let maxQuantity = parseInt(serviceDetails.max) || 999999;
        
        // Cập nhật input quantity trong modal
        $('#multiQuantity').attr('min', minQuantity);
        $('#multiQuantity').attr('max', maxQuantity);
        
        // Tự động điền số lượng tối thiểu
        $('#multiQuantity').val(minQuantity);
        
        // Cập nhật hiển thị min/max trong form-text
        $('#multi-min-quantity').text(new Intl.NumberFormat('vi-VN').format(minQuantity));
        $('#multi-max-quantity').text(new Intl.NumberFormat('vi-VN').format(maxQuantity));
        
        // Xử lý trường hợp Custom Comments
        if (serviceDetails.type === 'Custom Comments') {
            // Disable quantity input vì sẽ tự động tính theo số dòng comment
            $('#multiQuantity').attr('readonly', true);
            $('#multiQuantity').closest('.mb-3').find('.form-text').html(
                '<i class="ri-information-line text-primary"></i> ' +
                ORDER_LABELS.autoQuantityMessage
            );
        } else if (serviceDetails.type === 'Custom Comments Package') {
            // Cho phép thay đổi quantity cho Custom Comments Package
            $('#multiQuantity').val(minQuantity);
            $('#multiQuantity').attr('readonly', false);
            $('#multiQuantity').closest('.mb-3').find('.form-text').html(
                ORDER_LABELS.minLabel + ': <span id="multi-min-quantity">' + 
                new Intl.NumberFormat('vi-VN').format(minQuantity) + '</span> - ' +
                ORDER_LABELS.maxLabel + ': <span id="multi-max-quantity">' + 
                new Intl.NumberFormat('vi-VN').format(maxQuantity) + '</span>'
            );
        } else {
            // Enable quantity input cho các service khác
            $('#multiQuantity').attr('readonly', false);
            $('#multiQuantity').closest('.mb-3').find('.form-text').html(
                ORDER_LABELS.minLabel + ': <span id="multi-min-quantity">' + 
                new Intl.NumberFormat('vi-VN').format(minQuantity) + '</span> - ' +
                ORDER_LABELS.maxLabel + ': <span id="multi-max-quantity">' + 
                new Intl.NumberFormat('vi-VN').format(maxQuantity) + '</span>'
            );
        }
    } else {
        // Reset về trạng thái bình thường nếu không có service details
        $('#multiQuantity').attr('readonly', false);
    }
}

// Xử lý khi nhập danh sách links
function handleMultiLinksInput() {
    let links = $('#multiLinks').val().trim();
    let linkArray = links ? links.split('\n').filter(link => link.trim() !== '') : [];
    
    $('#linkCount').text(linkArray.length);
    
    if (linkArray.length > 0) {
        updateMultiOrderSummary(linkArray);
        generateOrdersList(linkArray);
        
        // Lưu vào localStorage
        const serviceId = $('#service').val();
        const delay = parseFloat($('#multiDelay').val()) || 1;
        const linksData = linkArray.map(url => ({
            url: url.trim(),
            status: 'pending'
        }));
        
        saveMultiOrderToStorage(serviceId, linksData, delay);
    } else {
        $('#ordersList').html(`
            <div class="text-center text-muted py-4">
                <i class="ri-shopping-cart-line fs-1"></i>
                <p class="mt-2">${ORDER_LABELS.enterLinks}</p>
            </div>
        `);
        
        // Xóa localStorage khi không có links
        clearMultiOrderStorage();
    }
}

// Cập nhật tổng tiền và thông tin
function updateMultiOrderSummary(linkArray) {
    let quantity = parseInt($('#multiQuantity').val()) || 1;
    let serviceId = $('#service').val();
    let totalOrders = linkArray.length;
    
    $('#totalOrders').text(totalOrders);
    
    if (!serviceId || totalOrders === 0) {
        resetMultiOrderPricing();
        return;
    }
    
    // Tính số lượng thực tế cho mỗi đơn hàng
    let actualQuantityPerOrder = quantity;
    
    // Chỉ với Custom Comments thì số lượng = số dòng comment
    if (serviceDetailsCache[serviceId] && serviceDetailsCache[serviceId].type === 'Custom Comments') {
        let comments = $('#multiComments').val().trim();
        if (comments) {
            let lines = comments.split(/\r\n|\r|\n/);
            let nonEmptyLines = lines.filter(function(line) {
                return line.trim() !== '';
            });
            actualQuantityPerOrder = nonEmptyLines.length > 0 ? nonEmptyLines.length : 1;
            
            // Cập nhật hiển thị số lượng trong modal (chỉ để user biết)
            $('#multiQuantity').val(actualQuantityPerOrder);
        }
    }
    // Custom Comments Package sử dụng quantity từ input, không tính theo comment
    
    // Tính tổng số lượng cho tất cả đơn hàng
    let totalQuantity = totalOrders * actualQuantityPerOrder;
    
    // Gọi AJAX để tính giá như form chính
    $.ajax({
        url: ORDER_CONFIG.base_url + 'ajaxs/client/view.php',
        type: 'POST',
        data: {
            action: 'totalPrice',
            service_id: serviceId,
            amount: totalQuantity,
            token: $('#api_key').val() // Sử dụng api_key thay vì token
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Hiển thị thông tin giá chi tiết
                $('#multiPrice').text(response.price);
                $('#multiPriceVat').text(response.price_vat);
                $('#multiTaxVat').text(response.tax_vat);
                $('#totalEstimatedPrice').text(response.total_price);
                
                // Hiển thị hoặc ẩn các dòng thuế VAT
                if (response.tax_vat > 0) {
                    $('#multi-price-detail-row').slideDown(200);
                    $('#multi-tax-detail-row').slideDown(200);
                    $('#multi-price-separator').slideDown(200);
                } else {
                    $('#multi-price-detail-row').slideUp(200);
                    $('#multi-tax-detail-row').slideUp(200);
                    $('#multi-price-separator').slideUp(200);
                }
            } else {
                resetMultiOrderPricing();
            }
        },
        error: function() {
            resetMultiOrderPricing();
        }
    });
}

// Reset pricing information
function resetMultiOrderPricing() {
    $('#multiPrice').text('0');
    $('#multiPriceVat').text('0');
    $('#multiTaxVat').text('0');
    $('#totalEstimatedPrice').text(ORDER_CONFIG.currencyFormat || '0');
    $('#multi-price-detail-row').slideUp(200);
    $('#multi-tax-detail-row').slideUp(200);
    $('#multi-price-separator').slideUp(200);
}

// Debounced version of updateMultiOrderSummary để tránh gọi AJAX quá nhiều
function updateMultiOrderSummaryDebounced() {
    clearTimeout(multiOrderQuantityDebounceTimer);
    multiOrderQuantityDebounceTimer = setTimeout(function() {
        let links = $('#multiLinks').val().trim();
        if (links) {
            let linkArray = links.split('\n').filter(link => link.trim() !== '');
            updateMultiOrderSummary(linkArray);
        }
    }, 500); // 500ms delay
}

// Tạo danh sách orders từ saved data với trạng thái
function generateOrdersListFromSavedData(savedLinks) {
    let html = '';
    
    savedLinks.forEach((link, index) => {
        const statusClass = link.status || 'waiting';
        let statusText = ORDER_LABELS.waiting;
        let statusIcon = 'ri-time-line';
        
        switch(statusClass) {
            case 'processing':
                statusText = ORDER_LABELS.processing;
                statusIcon = 'order-spinner';
                break;
            case 'success':
                statusText = ORDER_LABELS.completed;
                statusIcon = 'ri-check-line';
                break;
            case 'error':
                statusText = ORDER_LABELS.failed;
                statusIcon = 'ri-close-line';
                break;
        }
        
        html += `
            <div class="multi-order-item ${statusClass}" data-index="${index}">
                <div>
                    <div class="multi-order-link" title="${link.url}">${link.url}</div>
                    <small class="text-muted">${ORDER_LABELS.orderNumber} ${index + 1}</small>
                </div>
                <div class="multi-order-status">
                    <span class="status-badge ${statusClass}">${statusClass === 'processing' ? '<div class="order-spinner"></div>' : ''} ${statusText}</span>
                </div>
            </div>
        `;
    });
    
    $('#ordersList').html(html);
    $('#totalCount').text(savedLinks.length);
}

// Tạo danh sách đơn hàng hiển thị
function generateOrdersList(linkArray) {
    let html = '';
    
    linkArray.forEach((link, index) => {
        html += `
            <div class="multi-order-item waiting" data-index="${index}">
                <div>
                    <div class="multi-order-link" title="${link}">${link}</div>
                    <small class="text-muted">${ORDER_LABELS.orderNumber} ${index + 1}</small>
                </div>
                <div class="multi-order-status">
                    <span class="status-badge waiting">${ORDER_LABELS.waiting}</span>
                </div>
            </div>
        `;
    });
    
    $('#ordersList').html(html);
    $('#totalCount').text(linkArray.length);
}

// Bắt đầu mua nhiều đơn
function startMultiOrder() {
    let links = $('#multiLinks').val().trim();
    let linkArray = links ? links.split('\n').filter(link => link.trim() !== '') : [];
    let quantity = parseInt($('#multiQuantity').val()) || 100;
    let delay = parseFloat($('#multiDelay').val()) || 1;
    let comments = $('#multiComments').val().trim();
    
    // Validation
    if (linkArray.length === 0) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.enterLinks,
            icon: 'warning'
        });
        return;
    }
    
    if (delay < 0.1) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.invalidDelay,
            icon: 'warning'
        });
        return;
    }
    
    if (!$('#service').val()) {
        Swal.fire({
            title: ORDER_LABELS.error,
            text: ORDER_LABELS.serviceNotSelected,
            icon: 'warning'
        });
        return;
    }
    
    // Kiểm tra min/max quantity và comment requirement
    let serviceId = $('#service').val();
    if (serviceDetailsCache[serviceId]) {
        let minQuantity = parseInt(serviceDetailsCache[serviceId].min) || 1;
        let maxQuantity = parseInt(serviceDetailsCache[serviceId].max) || 999999;
        
        if (quantity < minQuantity) {
            Swal.fire({
                title: ORDER_LABELS.error,
                text: ORDER_LABELS.minQuantity + ' ' + minQuantity,
                icon: 'warning'
            });
            return;
        }
        
        if (quantity > maxQuantity) {
            Swal.fire({
                title: ORDER_LABELS.error,
                text: ORDER_LABELS.maxQuantity + ' ' + maxQuantity,
                icon: 'warning'
            });
            return;
        }
        
        // Kiểm tra comment requirement
        if ((serviceDetailsCache[serviceId].type === 'Custom Comments' || 
             serviceDetailsCache[serviceId].type === 'Custom Comments Package' ||
             serviceDetailsCache[serviceId].category_type === 'comment' ||
             serviceDetailsCache[serviceId].comment === 1 || 
             serviceDetailsCache[serviceId].comment === true) && 
            (!comments || comments.trim() === '')) {
            Swal.fire({
                title: ORDER_LABELS.error,
                text: ORDER_LABELS.commentRequired,
                icon: 'warning'
            });
            return;
        }
    }
    
    // Kiểm tra nếu tất cả đơn hàng đã hoàn thành
    if (multiOrderState.orders.length > 0) {
        let remainingOrders = multiOrderState.orders.filter(order => 
            order.status === 'pending' || order.status === 'waiting'
        ).length;
        
        if (remainingOrders === 0) {
            Swal.fire({
                title: ORDER_LABELS.notification,
                text: ORDER_LABELS.allOrdersProcessed,
                icon: 'info',
                confirmButtonText: ORDER_LABELS.ok
            });
            return;
        }
    }
    
    // Xác nhận bắt đầu
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: ORDER_LABELS.confirmStartMulti.replace('{count}', linkArray.length),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: ORDER_LABELS.yes,
        cancelButtonText: ORDER_LABELS.no
    }).then((result) => {
        if (result.isConfirmed) {
            // Tính số lượng thực tế cho mỗi đơn hàng
            let actualQuantityPerOrder = quantity;
            
            // Chỉ với Custom Comments thì số lượng = số dòng comment
            if (serviceDetailsCache[serviceId] && 
                serviceDetailsCache[serviceId].type === 'Custom Comments' && 
                comments && comments.trim()) {
                
                let lines = comments.split(/\r\n|\r|\n/);
                let nonEmptyLines = lines.filter(function(line) {
                    return line.trim() !== '';
                });
                actualQuantityPerOrder = nonEmptyLines.length > 0 ? nonEmptyLines.length : 1;
            }
            // Custom Comments Package sử dụng quantity từ input, không tính theo comment
            
            // Kiểm tra nếu đã có orders từ localStorage (đã restore)
            if (multiOrderState.orders.length === 0) {
                // Khởi tạo state mới
                multiOrderState.orders = linkArray.map((link, index) => ({
                    url: link,
                    service_id: $('#service').val(),
                    quantity: actualQuantityPerOrder,
                    comments: comments,
                    status: 'pending',
                    index: index,
                    order_id: null,
                    message: ''
                }));
                
                multiOrderState.currentIndex = 0;
                multiOrderState.processedCount = 0;
                multiOrderState.successCount = 0;
                multiOrderState.failedCount = 0;
            } else {
                // Nếu đã có orders từ localStorage, cập nhật quantity và comments
                multiOrderState.orders.forEach(order => {
                    order.quantity = actualQuantityPerOrder;
                    order.comments = comments;
                    order.service_id = $('#service').val(); // Cập nhật service hiện tại
                });
                
                // Đảm bảo currentIndex không vượt quá số orders
                if (multiOrderState.currentIndex >= multiOrderState.orders.length) {
                    multiOrderState.currentIndex = multiOrderState.orders.length;
                }
            }
            
            // Update common state
            multiOrderState.isRunning = true;
            multiOrderState.isPaused = false;
            multiOrderState.delay = delay * 1000; // Convert to milliseconds
            multiOrderState.interval = null;
            
            showMultiOrderButton('running');
            processNextOrder();
            
            Swal.fire({
                title: ORDER_LABELS.success,
                text: ORDER_LABELS.multiOrderStarted,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Xử lý đơn hàng tiếp theo
function processNextOrder() {
    if (!multiOrderState.isRunning || multiOrderState.isPaused) return;
    
    // Tìm đơn hàng tiếp theo chưa được xử lý
    while (multiOrderState.currentIndex < multiOrderState.orders.length) {
        let currentOrder = multiOrderState.orders[multiOrderState.currentIndex];
        
        // Nếu đơn hàng này chưa được xử lý, thì xử lý nó
        if (currentOrder.status === 'pending' || currentOrder.status === 'waiting') {
            updateOrderStatus(currentOrder.index, 'processing');
            
            // Call API tạo đơn hàng
            createSingleOrder(currentOrder).then(() => {
                multiOrderState.currentIndex++;
                updateProgress();
                
                // Delay trước khi xử lý đơn tiếp theo
                if (multiOrderState.currentIndex < multiOrderState.orders.length) {
                    multiOrderState.interval = setTimeout(() => {
                        processNextOrder();
                    }, multiOrderState.delay);
                } else {
                    completeMultiOrder();
                }
            });
            return;
        } else {
            // Nếu đơn hàng này đã được xử lý, chuyển sang đơn tiếp theo
            multiOrderState.currentIndex++;
        }
    }
    
    // Nếu đã duyệt hết mà không có đơn nào cần xử lý
    if (multiOrderState.currentIndex >= multiOrderState.orders.length) {
        completeMultiOrder();
    }
}

// Tạo một đơn hàng
function createSingleOrder(order) {
    return new Promise((resolve) => {
        $.ajax({
            url: ORDER_CONFIG.base_url + 'ajaxs/client/smmpanel.php',
            type: 'POST',
            data: {
                action: 'add',
                service: order.service_id || $('#service').val(),
                link: order.url,
                quantity: order.quantity,
                comments: order.comments,
                key: $('#api_key').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    order.status = 'success';
                    order.order_id = response.order_id || null;
                    order.message = ORDER_LABELS.orderCreated + (order.order_id ? ` (ID: ${order.order_id})` : '');
                    multiOrderState.successCount++;
                    updateOrderStatus(order.index, 'success', order.message);
                } else {
                    order.status = 'error';
                    order.message = response.msg || ORDER_LABELS.orderFailed;
                    multiOrderState.failedCount++;
                    updateOrderStatus(order.index, 'error', order.message);
                }
            },
            error: function() {
                order.status = 'error';
                order.message = ORDER_LABELS.connectionError;
                multiOrderState.failedCount++;
                updateOrderStatus(order.index, 'error', order.message);
            },
            complete: function() {
                multiOrderState.processedCount++;
                resolve();
            }
        });
    });
}

// Cập nhật trạng thái đơn hàng
function updateOrderStatus(index, status, message = '') {
    let $item = $(`.multi-order-item[data-index="${index}"]`);
    let $badge = $item.find('.status-badge');
    
    // Remove old classes
    $item.removeClass('waiting processing success error');
    $badge.removeClass('waiting processing success error');
    
    // Add new classes
    $item.addClass(status);
    $badge.addClass(status);
    
    // Update badge text
    switch(status) {
        case 'processing':
            $badge.html(`<div class="order-spinner"></div> ${ORDER_LABELS.processing}`);
            break;
        case 'success':
            $badge.text(ORDER_LABELS.completed);
            if (message) {
                $item.attr('title', message);
            }
            break;
        case 'error':
            $badge.text(ORDER_LABELS.failed);
            if (message) {
                $item.attr('title', message);
            }
            break;
        default:
            $badge.text(ORDER_LABELS.waiting);
    }
    
    // Cập nhật trạng thái trong multiOrderState.orders
    if (multiOrderState.orders[index]) {
        multiOrderState.orders[index].status = status;
        multiOrderState.orders[index].message = message;
        
        // Lưu vào localStorage nếu có thay đổi trạng thái
        if (status === 'success' || status === 'error') {
            const serviceId = $('#service').val();
            const delay = parseFloat($('#multiDelay').val()) || 1;
            const linksData = multiOrderState.orders.map(order => ({
                url: order.url,
                status: order.status,
                order_id: order.order_id || null,
                message: order.message || '',
                comments: order.comments || ''
            }));
            
            saveMultiOrderToStorage(serviceId, linksData, delay);
        }
    }
}

// Cập nhật progress bar
function updateProgress() {
    let progress = (multiOrderState.processedCount / multiOrderState.orders.length) * 100;
    $('#multiOrderProgress').css('width', progress + '%');
    $('#processedCount').text(multiOrderState.processedCount);
}

// Tạm dừng multi-order
function pauseMultiOrder() {
    multiOrderState.isPaused = true;
    if (multiOrderState.interval) {
        clearTimeout(multiOrderState.interval);
        multiOrderState.interval = null;
    }
    showMultiOrderButton('paused');
    
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: ORDER_LABELS.multiOrderPaused,
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
}

// Tiếp tục multi-order
function resumeMultiOrder() {
    multiOrderState.isPaused = false;
    showMultiOrderButton('running');
    processNextOrder();
    
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: ORDER_LABELS.multiOrderResumed,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}

// Dừng multi-order
function stopMultiOrder() {
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: ORDER_LABELS.confirmStopMulti,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: ORDER_LABELS.yes,
        cancelButtonText: ORDER_LABELS.no
    }).then((result) => {
        if (result.isConfirmed) {
            multiOrderState.isRunning = false;
            multiOrderState.isPaused = false;
            
            if (multiOrderState.interval) {
                clearTimeout(multiOrderState.interval);
                multiOrderState.interval = null;
            }
            
            showMultiOrderButton('start');
            
            Swal.fire({
                title: ORDER_LABELS.notification,
                text: ORDER_LABELS.multiOrderStopped,
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Hoàn thành multi-order
function completeMultiOrder() {
    multiOrderState.isRunning = false;
    multiOrderState.isPaused = false;
    showMultiOrderButton('start');
    
    let message = ORDER_LABELS.multiOrderCompleted + '\n' +
                 `${ORDER_LABELS.success}: ${multiOrderState.successCount}\n` +
                 `${ORDER_LABELS.failed}: ${multiOrderState.failedCount}`;
    
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: message,
        icon: multiOrderState.failedCount > 0 ? 'warning' : 'success',
        confirmButtonText: ORDER_LABELS.ok
    });
    
    // Play success sound if any orders succeeded
    if (multiOrderState.successCount > 0) {
        playSuccessSound();
    }
    
    // Xóa localStorage khi hoàn thành tất cả đơn hàng
    clearMultiOrderStorage();
}

// Clear tất cả đơn hàng
function clearAllOrders() {
    Swal.fire({
        title: ORDER_LABELS.notification,
        text: ORDER_LABELS.confirmClearAll,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: ORDER_LABELS.yes,
        cancelButtonText: ORDER_LABELS.no
    }).then((result) => {
        if (result.isConfirmed) {
            // Chỉ clear orders và UI liên quan, không reset quantity và min/max
            clearOrdersOnly();
            clearMultiOrderStorage(); // Xóa localStorage khi clear all
        }
    });
}

// Clear chỉ orders mà không ảnh hưởng đến quantity, min/max, delay
function clearOrdersOnly() {
    // Reset state orders
    multiOrderState.isRunning = false;
    multiOrderState.isPaused = false;
    multiOrderState.orders = [];
    multiOrderState.currentIndex = 0;
    multiOrderState.processedCount = 0;
    multiOrderState.successCount = 0;
    multiOrderState.failedCount = 0;
    if (multiOrderState.interval) {
        clearTimeout(multiOrderState.interval);
        multiOrderState.interval = null;
    }
    
    // Reset chỉ UI orders-related, giữ nguyên quantity, delay
    $('#multiLinks').val('');
    $('#linkCount').text('0');
    $('#totalOrders').text('0');
    $('#processedCount').text('0');
    $('#totalCount').text('0');
    $('#multiOrderProgress').css('width', '0%');
    
    // Reset pricing
    resetMultiOrderPricing();
    
    // Reset buttons
    showMultiOrderButton('start');  
    
    // Clear orders list
    $('#ordersList').html(`
        <div class="text-center text-muted py-4">
            <i class="ri-shopping-cart-line fs-1"></i>
            <p class="mt-2">${ORDER_LABELS.enterLinks}</p>
        </div>
    `);
}

// Bind events cho multi-order khi document ready
$(document).ready(function() {
    // Bind các event cho multi-order sau khi các event khác đã được bind
    setTimeout(function() {
        // Xử lý input links
        $('#multiLinks').on('input', handleMultiLinksInput);
        
        // Xử lý thay đổi quantity với debounce
        $('#multiQuantity').on('input', updateMultiOrderSummaryDebounced);
        
        // Xử lý thay đổi comment với debounce (cho Custom Comments)
        $('#multiComments').on('input', function() {
            let serviceId = $('#service').val();
            if (serviceDetailsCache[serviceId] && 
                (serviceDetailsCache[serviceId].type === 'Custom Comments' || 
                 serviceDetailsCache[serviceId].type === 'Custom Comments Package')) {
                
                // Debounce update để không gọi quá nhiều
                clearTimeout(multiOrderCommentDebounceTimer);
                multiOrderCommentDebounceTimer = setTimeout(function() {
                    let links = $('#multiLinks').val().trim();
                    if (links) {
                        let linkArray = links.split('\n').filter(link => link.trim() !== '');
                        updateMultiOrderSummary(linkArray);
                    }
                }, 500);
            }
        });
        
        // Xử lý thay đổi delay để lưu vào localStorage
        $('#multiDelay').on('change', function() {
            const links = $('#multiLinks').val().trim();
            if (links) {
                const linkArray = links.split('\n').filter(link => link.trim() !== '');
                if (linkArray.length > 0) {
                    const serviceId = $('#service').val();
                    const delay = parseFloat($(this).val()) || 1;
                    const linksData = linkArray.map(url => ({
                        url: url.trim(),
                        status: 'pending'
                    }));
                    
                    saveMultiOrderToStorage(serviceId, linksData, delay);
                }
            }
        });
        
        // Bind button events
        $('#startMultiOrder').on('click', startMultiOrder);
        $('#pauseMultiOrder').on('click', pauseMultiOrder);
        $('#resumeMultiOrder').on('click', resumeMultiOrder);
        $('#stopMultiOrder').on('click', stopMultiOrder);
        $('#clearAllOrders').on('click', clearAllOrders);
        
        // Xử lý khi đóng modal - dừng process nếu đang chạy
        $('#multiOrderModal').on('hidden.bs.modal', function() {
            if (multiOrderState.isRunning && !multiOrderState.isPaused) {
                multiOrderState.isRunning = false;
                if (multiOrderState.interval) {
                    clearTimeout(multiOrderState.interval);
                    multiOrderState.interval = null;
                }
            }
        });
        

    }, 100);
});

// Thêm hàm global để có thể gọi từ HTML
window.showMultiLinkModal = showMultiLinkModal;

// ==================== CHỨC NĂNG ẨN THÔNG BÁO TELEGRAM ====================

/**
 * Kiểm tra và hiển thị thông báo Telegram dựa trên localStorage
 * Thông báo sẽ được ẩn trong 1 giờ sau khi user nhấn nút đóng
 */
function checkTelegramNoticeVisibility() {
    const hideTime = localStorage.getItem('telegram_notice_hidden');
    if (hideTime) {
        const currentTime = new Date().getTime();
        const hiddenTime = parseInt(hideTime);
        const oneHour = 60 * 60 * 1000; // 1 giờ = 60 phút * 60 giây * 1000ms
        
        // Nếu chưa đủ 1 giờ thì giữ ẩn
        if (currentTime - hiddenTime < oneHour) {
            // Giữ ẩn, không làm gì
            return;
        } else {
            // Đã đủ 1 giờ, xóa record và hiển thị lại
            localStorage.removeItem('telegram_notice_hidden');
            $('#telegramNoticeCard').fadeIn(300);
        }
    } else {
        // Chưa từng ẩn, hiển thị bình thường
        $('#telegramNoticeCard').fadeIn(300);
    }
}

/**
 * Ẩn thông báo Telegram trong 1 giờ
 * Lưu timestamp vào localStorage và hiển thị thông báo xác nhận
 */
function hideTelegramNotice() {
    // Lưu thời gian hiện tại vào localStorage
    const currentTime = new Date().getTime();
    localStorage.setItem('telegram_notice_hidden', currentTime.toString());
    
    // Ẩn thông báo với hiệu ứng fadeOut
    $('#telegramNoticeCard').fadeOut(300);
}

// Thêm hàm global để có thể gọi từ HTML
window.hideTelegramNotice = hideTelegramNotice;



// ==================== CHỨC NĂNG DÁN TỪ CLIPBOARD ====================

/**
 * Hàm dán văn bản từ clipboard vào ô liên kết
 * Sử dụng Clipboard API hiện đại với fallback cho trình duyệt cũ
 */
async function pasteFromClipboard() {
    const linkInput = document.getElementById('link');
    const pasteButton = document.getElementById('pasteFromClipboard');
    
    if (!linkInput) return;
    
    try {
        // Disable button và hiển thị loading
        pasteButton.disabled = true;
        pasteButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        
        let clipboardText = '';
        
        // Thử sử dụng Clipboard API hiện đại
        if (navigator.clipboard && navigator.clipboard.readText) {
            clipboardText = await navigator.clipboard.readText();
        } else {
            // Fallback cho trình duyệt cũ - tạo textarea tạm thời
            const tempTextarea = document.createElement('textarea');
            tempTextarea.style.position = 'fixed';
            tempTextarea.style.left = '-999999px';
            tempTextarea.style.top = '-999999px';
            document.body.appendChild(tempTextarea);
            
            tempTextarea.focus();
            tempTextarea.select();
            
            // Thực hiện paste command
            if (document.execCommand('paste')) {
                clipboardText = tempTextarea.value;
            }
            
            document.body.removeChild(tempTextarea);
        }
        
                // Kiểm tra và xử lý nội dung clipboard
        if (clipboardText && clipboardText.trim() !== '') {
            // Trim whitespace và chỉ lấy dòng đầu tiên nếu có nhiều dòng
            const cleanText = clipboardText.trim().split('\n')[0].trim();
            
            if (cleanText.length > 0) {
                linkInput.value = cleanText;
                linkInput.focus();
                
                // Hiển thị thông báo thành công
                showMessage(ORDER_LABELS.pasteSuccess || 'Đã dán liên kết thành công!', 'success');
            } else {
                showMessage(ORDER_LABELS.pasteEmpty || 'Clipboard trống hoặc không có nội dung hợp lệ.', 'error');
            }
        } else {
            showMessage(ORDER_LABELS.pasteEmpty || 'Clipboard trống hoặc không có nội dung hợp lệ.', 'error');
        }
        
    } catch (error) {
        console.error('Error accessing clipboard:', error);
        
        // Xử lý các lỗi khác nhau
        if (error.name === 'NotAllowedError') {
            showMessage(ORDER_LABELS.pastePermissionDenied || 'Không có quyền truy cập clipboard. Vui lòng cấp quyền hoặc dán thủ công (Ctrl+V).', 'error');
        } else if (error.name === 'NotFoundError') {
            showMessage(ORDER_LABELS.pasteNotFound || 'Clipboard trống hoặc không có nội dung text.', 'error');
        } else {
            showMessage(ORDER_LABELS.pasteError || 'Không thể truy cập clipboard. Vui lòng dán thủ công (Ctrl+V).', 'error');
        }
    } finally {
        // Khôi phục button
        pasteButton.disabled = false;
        pasteButton.innerHTML = '<i class="ri-clipboard-line me-1"></i>' + (ORDER_LABELS.pasteButtonText || 'Past');
    }
}


// Thêm hàm global để có thể gọi từ HTML
window.pasteFromClipboard = pasteFromClipboard;


