<?php 
	return [
		'status' => [
			'0' => 'Chọn tình trạng',
			'1' => 'Đã kích hoạt',
			'2' => 'Kích hoạt bảo hành',
		],
		'publish' => [
			'0' => 'Chọn tình trạng',
			'1' => 'Không xuất bản',
			'2' => 'Xuất bản',
		],
		'follow' => [
			'1' => 'Follow',
			'2' => 'Nofollow',
			
		],
		// contacts.type — so the admin can tell at a glance which channel a
		// contact came in through. 1 and 2 are historical: 1 is everything
		// imported from the old website, 2 is the cart checkout form.
		'contactType' => [
			'1' => 'Website cũ',
			'2' => 'Đặt hàng',
			'3' => 'Yêu cầu báo giá',
			'4' => 'Liên hệ kinh doanh',
		],
		'contactTypeQuote' => 3,
		'contactTypeBusiness' => 4,
		'suffix' => '.html',
		'defaultPublish' => ['publish','=', 2],
        'retail_customer' => 1,
        'time_expried' => 180,
        'google_client_id' => '',
        'google_secret_id' => '',
        'facebook_client_id' => '',
        'facebook_secret_id' => '',
	];
