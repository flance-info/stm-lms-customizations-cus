<?php

if ( isset( $_GET['page'] ) && $_GET['page'] != 'stm-lms-dashboard' ) {
	return;
}
if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
	wp_register_script( 'jspdf', STM_LMS_PRO_URL . '/assets/js/certificate-builder/jspdf.umd.js', array(), stm_lms_custom_styles_v(), false );
	wp_enqueue_script( 'masterstudy_generate_certificate', SLMS_URL . '/assets/js/course-player/generate-certificate.js', array( 'jspdf', 'masterstudy_certificate_fonts' ), stm_lms_custom_styles_v(), true );
	wp_localize_script(
			'masterstudy_generate_certificate',
			'course_certificate',
			array(
					'nonce'    => wp_create_nonce( 'stm_get_certificate' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
	);
}
?>
<transition name="slide">

	<div class="stm-lms-dashboard-inner stm-lms-dashboard-certificate">

		<div class="loading" v-if="loading"></div>

		<div class="inner_course" v-else>

			<div class="stm-lms-dashboard-course--heading">

				<div class="titles">
					<h5 v-if="students.length">
						<?php esc_html_e( 'All' ) ?>
						{{students.length}}
						<?php esc_html_e( 'students of', 'masterstudy-lms-learning-management-system' ); ?></h5>
					<h2 v-html="title"></h2>
				</div>

				<div class="filters">
					<?php
					$fields = SLMS_Form_Builder::get_form_builder_fields();
					?>
					<?php if ( count( $fields ) ): ?>
						<?php foreach ( $fields as $field ): ?>
							<div class="filter-item">
								<?php if ( $field['type'] == 'select' ): ?>
									<select v-model="filter['<?php echo $field['id']; ?>']" @change="changeFilter" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>">
										<?php if ( count( $field['choices'] ) ): ?>
											<option :value="'undefined'"><?php echo __( 'Choose', 'slms' ) . ' ' . $field['label']; ?></option>
											<?php foreach ( $field['choices'] as $key => $choice ): ?>
												<option :value="'<?php echo $choice; ?>'"><?php echo $choice; ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<div class="searchboxes">
					<add_user :course_id="id" v-on:studentAdded="studentAdded" :title="origin_title"></add_user>
					<div class="searchbox-wrapper">
						<i class="fa fa-search"></i>
						<input type="text" v-model="search"
							   placeholder="<?php esc_attr_e( 'Search student...', 'masterstudy-lms-learning-management-system' ); ?>">
					</div>
				</div>

			</div>

			<div class="lms-dashboard-table" v-if="students.length">

				<table>
					<thead>
					<tr>
						<th class="name">
							<div class="sort-table" @click="sortBy('name')"
								 v-bind:class="[sort === 'name' ? 'active' : '', 'direction_' + sortDirection]">
								<i class="fa fa-long-arrow-alt-up"></i>
								<i class="fa fa-long-arrow-alt-down"></i>
								<?php esc_html_e( 'Student name', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</th>
						<th class="VILLE">
							<div class="sort-table" @click="sortBy('VILLE')"
								 v-bind:class="[sort === 'VILLE' ? 'active' : '', 'direction_' + sortDirection]">
								<i class="fa fa-long-arrow-alt-up"></i>
								<i class="fa fa-long-arrow-alt-down"></i>
								<?php esc_html_e( 'Ville', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</th>
						<th class="email">
							<div class="sort-table" @click="sortBy('email')"
								 v-bind:class="[sort === 'email' ? 'active' : '', 'direction_' + sortDirection]">
								<i class="fa fa-long-arrow-alt-up"></i>
								<i class="fa fa-long-arrow-alt-down"></i>
								<?php esc_html_e( 'Student email', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</th>
						<th class="time">
							<div class="sort-table" @click="sortBy('time')"
								 v-bind:class="[sort === 'time' ? 'active' : '', 'direction_' + sortDirection]">
								<i class="fa fa-long-arrow-alt-up"></i>
								<i class="fa fa-long-arrow-alt-down"></i>
								<?php esc_html_e( 'Started', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</th>

						<th class="certified time">
							<div class="sort-certified">
								<?php esc_html_e( 'Certified', 'slms' ); ?>
							</div>
						</th>
						<th class="certificate time">
							<div class="sort-certificate">
								<?php esc_html_e( 'Certificates', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</th>


						<th class="progress_cell">
							<div class="sort-table" @click="sortBy('progress')"
								 v-bind:class="[sort === 'progress' ? 'active' : '', 'direction_' + sortDirection]">
								<i class="fa fa-long-arrow-alt-up"></i>
								<i class="fa fa-long-arrow-alt-down"></i>
								<?php esc_html_e( 'Grade', 'slms' ); ?>
							</div>
						</th>
						<th class="student_progress"></th>
						<th class="delete"></th>
					</tr>
					</thead>
					<tbody>
					<tr v-for="(list, list_key) in studentsList"
						v-bind:class="{'table_loading' : list.loading}">


						<td class="name">

							<div class="author">
								<div class="img" v-html="list.student.avatar"></div>
								<div class="author__info">
									<h5 v-html="list.student.login"></h5>
								</div>
							</div>
						</td>

						<td class="ville" v-html="list.student.ville"></td>

						<td class="email" v-html="list.student.email"></td>

						<td v-html="list.ago" class="time"></td>
						<td class="certify">
							<label>
								<input
										@click="CheckedUserCourseCertificate($event, id, list, list_key)"
										name="stm-certify" type="checkbox" value="" :checked="list.completion ? true : false">
								<?php esc_html_e( 'Certified', 'slms' ); ?>
							</label>
						</td>

						<td class="certificate">

							<?php if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) : ?>
								<a href="#"
								   v-if="list.completion"
								   class="stm_lms_course_completed_summary__certificate masterstudy_preview_certificate"
								   :data-id="id"
								   :data-user_id="list.user_id"
								>
									<i class="fa fa-cloud-download-alt"></i>
									<?php esc_html_e( 'Certificate', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
							<?php endif; ?>
						</td>

						<td class="progress_cell" @click="toUser(id, list.user_id)">
							<div class="progress-wrapper">
								<div class="progress-label">{{list.progress_percent}}%</div>
							</div>
						</td>

						<td class="student_progress">
							<div class="goToProgress" @click="toUser(id, list.user_id)">
								<i class="fa fa-list"></i>
								<?php esc_html_e( 'Validate', 'slms' ); ?>
							</div>
						</td>

						<td class="delete">

							<i class="lnr lnr-trash" @click="deleteUserCourse(id, list, list_key)"></i>

						</td>
					</tr>

					</tbody>
				</table>

				<div class="filter">
					<div class="filter_single">
						<label><?php esc_html_e( 'Show on page', 'masterstudy-lms-learning-management-system' ); ?></label>
						<select v-model="limit" @change="page = 1">
							<option v-bind:value="20">20</option>
							<option v-bind:value="30">30</option>
							<option v-bind:value="40">40</option>
							<option v-bind:value="50">50</option>
							<option v-bind:value="100">100</option>
						</select>
					</div>
					<div class="filter_single">
						<label><?php esc_html_e( 'Page', 'masterstudy-lms-learning-management-system' ); ?></label>
						<select v-model="page">
							<option v-for="n in pages" v-bind:value="n">{{n}}</option>
						</select>
					</div>

				</div>

			</div>

			<h4 v-else><?php esc_html_e( 'No students in course yet...', 'masterstudy-lms-learning-management-system' ); ?></h4>

		</div>


	</div>

</transition>