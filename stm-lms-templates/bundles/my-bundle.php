<?php
/**
 * @var $bundle_id
 */
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings;

if ( ! is_user_logged_in() ) {
	STM_LMS_User::js_redirect( STM_LMS_User::login_page_url() );
	die;
}

stm_lms_register_style( 'user_info_top' );
stm_lms_register_style( 'bundles/my-bundle' );
slms_register_script( 'bundles/my-bundle', array( 'vue.js', 'vue-resource.js' ) );
wp_localize_script(
	'stm-lms-bundles/my-bundle',
	'stm_lms_my_bundle_courses',
	array(
		'list'         => STM_LMS_Instructor::get_courses(
			array(
				'posts_per_page' => -1,
				'post_status'    => array( 'publish' ),
			),
			true
		),
		'bundle_id'    => $bundle_id,
		'bundle_limit' => ( new CourseBundleSettings() )->get_bundle_courses_limit(),
		'editor_id'    => "stm_lms_bundle_name_{$bundle_id}",
		'translations' => array(
			'empty_name'        => esc_html__( 'Please enter bundle name', 'masterstudy-lms-learning-management-system-pro' ),
			'empty_description' => esc_html__( 'Please enter bundle description', 'masterstudy-lms-learning-management-system-pro' ),
			'empty_courses'     => esc_html__( 'Please add courses in bundle', 'masterstudy-lms-learning-management-system-pro' ),
			'empty_price'       => esc_html__( 'Please enter bundle price', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

$bundle_data = '';

if ( ! empty( $bundle_id ) ) {

	$bundle_data = ( new CourseBundleRepository() )->get_bundle_data( $bundle_id );

    $bundle_data->bundle_point_price = intval( get_post_meta( $bundle_id, 'bundle_point_price', true ) );

	if ( $bundle_data ) {
		wp_localize_script( 'stm-lms-bundles/my-bundle', 'stm_lms_my_bundle', array( 'data' => $bundle_data ) );
	}
}

?>


<div class="stm_lms_my_bundles">

	<h2><?php esc_html_e( 'Add new bundle', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>

</div>

<div class="multiseparator"></div>


<div id="stm_lms_my_bundle">

	<?php STM_LMS_Templates::show_lms_template( 'bundles/my-bundle/select-course' ); ?>
	<?php STM_LMS_Templates::show_lms_template( 'bundles/my-bundle/title' ); ?>
	<?php STM_LMS_Templates::show_lms_template( 'bundles/my-bundle/image' ); ?>
	<?php STM_LMS_Templates::show_lms_template( 'bundles/my-bundle/description', compact( 'bundle_id', 'bundle_data' ) ); ?>
	<?php STM_LMS_Templates::show_lms_template( 'bundles/my-bundle/price' ); ?>

    <div class="stm_lms_my_bundle__title">
        <h4 class="stm_lms_my_bundle__label">
            <?php _e( 'Bundle point price', 'slms' ); ?>
        </h4>
        <input type="number"
               class="form-control" v-model="bundle_point_price" oninput="this.value"
               placeholder="<?php esc_attr_e( 'Enter bundle point price', 'slms' ); ?>"/>
    </div>

	<a href="#" @click.prevent="saveBundle()" class="btn btn-default" v-bind:class="{'loading' : loading}">
		<span><?php esc_html_e( 'Save Bundle', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
	</a>

	<transition name="slide-fade">
		<div class="stm-lms-message" v-bind:class="status" v-if="message" v-html="message"></div>
	</transition>

</div>