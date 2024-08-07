<?php
// 2024,08,06
function add_custom_user_role() {
	add_role(
			'ном_хандивлагч', // Internal name of the role (lowercase, no spaces)
			__('Ном Хандивлагч'), // Display name of the role
			array(
				'read' => true
			)
	);
}
add_action('init', 'add_custom_user_role');


function assign_membership_to_nom_handivlagch_users() {
	// Define the membership level ID for "1 жилийн эрх"
	$membership_level_id = 2;
	$user = wp_get_current_user();
	$roles = (array) $user->roles;
	// $user = get_userdata($user_id);

	if($roles && in_array('ном_хандивлагч', $roles)) {
		// Get all users with the 'ном_хандивлагч' role
		$args = array(
			'role'    => 'ном_хандивлагч',
			'fields'  => 'ID' // Only retrieve user IDs for performance
		);
		$users = get_users($args);
		// Assign the membership level to each user
		foreach ($users as $user_id) {
			// Change the membership level for the user
			pmpro_changeMembershipLevel($membership_level_id, $user_id);
		}
	} else {
		pmpro_changeMembershipLevel(0);
	}
}

// Hook into WordPress initialization to run the function
add_action('init', 'assign_membership_to_nom_handivlagch_users');

// 2024.08.07

function save_nom_handivlagch_role_change_date($user_id, $role) {
	// Хэрэв шинэ үүрэг нь "ном_хандивлагч" бол
	if ($role === 'ном_хандивлагч') {
			// Одоогийн огноог хэрэглэгчийн мета өгөгдөлд хадгалах
			update_user_meta($user_id, 'nom_handivlagch_role_assigned_date', current_time('mysql'));
	}
}
add_action('set_user_role', 'save_nom_handivlagch_role_change_date', 10, 2);

// function can_view_course($course_id) {
// 	$current_user = wp_get_current_user();
// 	$role_assigned_date = get_user_meta($current_user->ID, 'nom_handivlagch_role_assigned_date', true);
	
// 	// Хичээлийн нийтэд нээлттэй болсон огноог авах
// 	$course_publication_date = get_the_date('Y-m-d H:i:s', $course_id);

// 	// Хэрэв хэрэглэгч 'ном_хандивлагч' үүрэгтэй бол
// 	if (in_array('ном_хандивлагч', (array) $current_user->roles)) {
// 			if ($role_assigned_date && $course_publication_date <= $role_assigned_date) {
// 					// Хэрэв хичээлийг нийтэд нээлттэй болгосон огноо нь үүргийг авснаас өмнөх бол
// 					return true;
// 			} else {
// 					// Хичээлийг үзэх боломжгүй бол
// 					return false;
// 			}
// 	}

// 	// Бусад бүх хэрэглэгчдэд хичээлийг үзэх боломжийг олгоно
// 	return true;
// }

function can_access_lesson($lesson_id) {
	// Get the current user
	$current_user = wp_get_current_user();
	
	// Get the user's 'ном_хандивлагч' role assigned date
	$role_assigned_date = get_user_meta($current_user->ID, 'nom_handivlagch_role_assigned_date', true);
	
	// If the user doesn't have the 'ном_хандивлагч' role or there's no assigned date, deny access
	if (!$role_assigned_date || !in_array('ном_хандивлагч', (array) $current_user->roles)) {
			return false;
	}
	
	// Get the publication date of the lesson
	$lesson_publication_date = get_the_date('Y-m-d H:i:s', $lesson_id);
	
	// Allow access if the lesson was published before the role was assigned
	return $lesson_publication_date <= $role_assigned_date;
}


// if (can_view_course(get_the_ID())) {
//   // Хичээлийг харуулах
//   the_content();
//   // print_r($current_user);
// } else {
//     // Хичээлийг үзэх боломжгүй тухай мессеж харуулах
//     echo 'Энэ хичээлд нэвтрэх боломжгүй.';
// }

?>
