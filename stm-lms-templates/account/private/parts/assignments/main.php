<div id="stm_lms_enrolled_assignments">

	<div class="asignments_grid__search">
		<h2><?php esc_html_e( 'My Assignments', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>

		<input type="text" class="form-control"
				v-model="s"
				placeholder="<?php esc_attr_e( 'Search assignment', 'masterstudy-lms-learning-management-system-pro' ); ?>"
				@keyup="initSearch()">

	</div>

	<div class="multiseparator"></div>

	<div class="enrolled-assignments-grid" :class="{'loading': loading}">

		<a :href="assignment.url" class="enrolled-assignment" v-for="assignment in assignments">

			<div class="inner" v-bind:class="{'unviewed' : assignment.who_view === '0'}">
				<i class="fa fa-bell"></i>
				<div class="enrolled-assignment--title heading_font" v-html="assignment.assignment_title"></div>

				<div class="enrolled-assignment--course">
					<span><?php esc_html_e( 'Course:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<span class="enrolled-assignment--course-title" v-html="assignment.course_title"></span>
				</div>

				<div class="enrolled-assignment--meta">

					<div class="enrolled-assignment--teacher">
						<img :src="assignment.instructor.avatar_url"/>
						<div class="enrolled-assignment--teacher_name">
							<span><?php esc_html_e( 'Teacher:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
							<span class="enrolled-assignment--value" v-html="assignment.instructor.login"></span>
						</div>
					</div>

					<div class="enrolled-assignment--status" :class="assignment.status.status">
						<span><?php esc_html_e( 'Grade:', 'slms' ); ?></span>
						<span class="enrolled-assignment--value" v-html="assignment.grade"></span>
					</div>

					<div class="enrolled-assignment--time">
						<i class="far fa-clock"></i>
						<span><?php esc_html_e( 'Last update:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
						<span class="enrolled-assignment--value" v-html="assignment.updated_at"></span>
					</div>

				</div>

			</div>

		</a>
	</div>
	<div class="asignments_grid__pagination" v-if="pages !== 1">
		<ul class="page-numbers">

			<li v-for="single_page in pages">

				<a class="page-numbers"
				   href="#"
				   v-if="single_page !== page"
				   @click.prevent="page = single_page; getAssignments()"
				   v-bind:class="computePage(single_page)">
					{{single_page}}
				</a>
				<span v-else class="page-numbers current">{{single_page}}</span>
			</li>
		</ul>
	</div>

	<h4 v-if="!assignments.length">
		<?php esc_html_e( 'You dont have any assignments yet', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</h4>

</div>
